<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Las migraciones que rescatan el contenido antes de que otras lo borren.
 *
 * Se simula una instalación antigua —con las columnas viejas y datos dentro— y
 * se comprueba que el texto acaba en los campos nuevos en vez de perderse.
 */
class MigracionPreservaDatosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // SQLite no revierte el DDL dentro de la transacción del test, así que
        // las tablas de respaldo sobreviven de un test al siguiente y hacían
        // que la migración se saltara la copia por creerla ya hecha.
        Schema::dropIfExists('programas_textos_previos');
        Schema::dropIfExists('docentes_datos_previos');
    }

    /** Recrea las columnas que el esquema actual ya no tiene, con contenido. */
    private function simularBaseAntigua(): int
    {
        Schema::table('programas', function (Blueprint $t) {
            $t->text('presentacion')->nullable();
            $t->text('descripcion')->nullable();
            $t->text('perfil_egresado')->nullable();
        });

        $id = DB::table('programas')->insertGetId([
            'nombre' => 'Maestría de prueba',
            'slug' => 'maestria-de-prueba',
            'grado' => 'Curso',
            'is_active' => true,
            'presentacion' => 'Texto de presentación del programa.',
            'descripcion' => 'Descripción larga del programa.',
            'perfil_egresado' => "Investiga con rigor\nPublica en revistas indexadas\nDirige equipos",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }

    /**
     * Carga una migración por su ruta.
     *
     * No sirve `require`: Laravel ya incluyó esos archivos al preparar la base
     * de tests, así que devolvería `true` en lugar del objeto.
     */
    private function migracion(string $archivo)
    {
        $migrator = app('migrator');
        $resolver = new \ReflectionMethod($migrator, 'resolvePath');
        $resolver->setAccessible(true);

        return $resolver->invoke($migrator, database_path("migrations/{$archivo}.php"));
    }

    /** Ejecuta las dos migraciones nuevas en su orden real. */
    private function correrMigraciones(): void
    {
        $this->migracion('2026_01_05_164100_preservar_datos_antes_de_migrar')->up();

        // Entre una y otra, la migración original borra las columnas.
        Schema::table('programas', fn (Blueprint $t) => $t->dropColumn(['presentacion', 'descripcion', 'perfil_egresado']));

        $this->migracion('2026_01_06_133600_restaurar_textos_de_programas')->up();
    }

    public function test_el_texto_sobrevive_al_borrado_de_columnas(): void
    {
        $id = $this->simularBaseAntigua();

        $this->correrMigraciones();

        $programa = DB::table('programas')->find($id);

        // Sin esto, `migrate` sobre una base con datos reales los borraba en
        // silencio: es justo el contenido de los programas que hay que migrar.
        $this->assertSame('Descripción larga del programa.', $programa->sumilla);
        $this->assertSame('Texto de presentación del programa.', $programa->por_que_text);
    }

    public function test_el_perfil_del_egresado_se_convierte_en_lista(): void
    {
        $id = $this->simularBaseAntigua();

        $this->correrMigraciones();

        // `perfil_graduado` es JSON, no texto: no era un simple renombrado.
        $lista = json_decode(DB::table('programas')->find($id)->perfil_graduado, true);

        $this->assertSame(
            ['Investiga con rigor', 'Publica en revistas indexadas', 'Dirige equipos'],
            $lista
        );
    }

    public function test_no_pisa_contenido_que_ya_estuviera_escrito(): void
    {
        $id = $this->simularBaseAntigua();
        DB::table('programas')->where('id', $id)->update(['sumilla' => 'Sumilla ya redactada']);

        $this->correrMigraciones();

        $this->assertSame('Sumilla ya redactada', DB::table('programas')->find($id)->sumilla);
    }

    public function test_el_respaldo_se_conserva_por_si_el_traslado_no_encaja(): void
    {
        $id = $this->simularBaseAntigua();

        $this->correrMigraciones();

        $this->assertTrue(Schema::hasTable('programas_textos_previos'));
        $this->assertSame(
            'Descripción larga del programa.',
            DB::table('programas_textos_previos')->find($id)->descripcion
        );
    }

    public function test_sobre_una_base_nueva_no_hace_nada_ni_falla(): void
    {
        // Es lo que ocurre en una instalación desde cero: las columnas viejas
        // no existen y las migraciones deben pasar de largo sin romperse.
        $this->migracion('2026_01_05_164100_preservar_datos_antes_de_migrar')->up();
        $this->migracion('2026_01_06_133600_restaurar_textos_de_programas')->up();

        $this->assertFalse(Schema::hasTable('programas_textos_previos'));
    }

    public function test_tambien_rescata_los_datos_de_docentes(): void
    {
        Schema::table('docentes', function (Blueprint $t) {
            $t->string('telefono')->nullable();
            $t->string('especialidad')->nullable();
        });

        $id = DB::table('docentes')->insertGetId([
            'nombres' => 'Ana', 'apellidos' => 'Quispe', 'estado' => 'activo',
            'telefono' => '987 654 321', 'especialidad' => 'Lingüística andina',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->migracion('2026_01_05_164100_preservar_datos_antes_de_migrar')->up();

        // No tienen campo equivalente en el esquema nuevo, así que al menos
        // quedan recuperables en vez de desaparecer.
        $guardado = DB::table('docentes_datos_previos')->find($id);
        $this->assertSame('987 654 321', $guardado->telefono);
        $this->assertSame('Lingüística andina', $guardado->especialidad);
    }
}
