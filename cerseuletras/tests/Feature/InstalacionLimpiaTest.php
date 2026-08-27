<?php

namespace Tests\Feature;

use App\Models\TipoOferta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Una instalación limpia no publica contenido de otra unidad.
 *
 * Es la regla que el proyecto se dio después de encontrar contenido de la
 * Unidad de Posgrado cinco veces seguidas, cada una en un sitio distinto: un
 * seeder (los diez documentos), una migración (el cronograma de admisión), el
 * respaldo de un controlador (sus títulos), un seeder desincronizado de la base
 * (la misión y los valores) y, el último, el proceso de admisión entero con sus
 * seis convocatorias de diplomados.
 *
 * Las cuatro primeras se encontraron a mano, y la de la migración solo apareció
 * clonando el repositorio en limpio, porque las migraciones corren antes que los
 * seeders y nadie audita contenido ahí. Esta prueba hace ese trabajo sola: monta
 * la base desde cero —migraciones y seeders, como un despliegue nuevo— y recorre
 * todas las tablas buscando el vocabulario que delata a la otra unidad.
 *
 * Si falla, lo que hay que quitar es el contenido, no la prueba. Un texto
 * legítimo del CERSEU no dice «bachiller» ni «diplomado»: su oferta está abierta
 * a toda la comunidad y no exige un grado previo.
 */
class InstalacionLimpiaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vocabulario que solo tiene sentido en el portal de Posgrado.
     *
     * «maestría» y «doctorado» son grados que el CERSEU no ofrece; «bachiller»
     * y «expediente» pertenecen a una admisión con requisitos de titulación;
     * «diplomado» es el nombre de la oferta de la otra unidad.
     */
    private const VOCABULARIO_AJENO = [
        'bachiller',
        'diplomado',
        'maestría',
        'doctorado',
        'entrevista personal',
        'Estudios de Posgrado',
    ];

    /**
     * Columnas donde un texto es contenido publicable. Se excluyen las de
     * identidad y las marcas de tiempo: un slug o una fecha no publican nada.
     */
    private function esColumnaDeTexto(string $tabla, string $columna): bool
    {
        if (in_array($columna, ['id', 'slug', 'created_at', 'updated_at', 'deleted_at'], true)) {
            return false;
        }

        return in_array(
            Schema::getColumnType($tabla, $columna),
            ['string', 'text', 'json'],
            true
        );
    }

    public function test_los_seeders_no_publican_contenido_de_otra_unidad(): void
    {
        $this->seed();

        // El enlace a la web de la unidad hermana sí es legítimo y va en el
        // menú: es un acceso institucional, no contenido suplantado.
        $exentas = ['menu_items', 'migrations', 'users', 'sessions', 'cache', 'jobs'];

        $hallazgos = [];

        foreach (Schema::getTableListing() as $tabla) {
            $tabla = str_contains($tabla, '.') ? substr($tabla, strrpos($tabla, '.') + 1) : $tabla;

            if (in_array($tabla, $exentas, true)) {
                continue;
            }

            $columnas = array_filter(
                Schema::getColumnListing($tabla),
                fn (string $c) => $this->esColumnaDeTexto($tabla, $c)
            );

            if ($columnas === []) {
                continue;
            }

            foreach (DB::table($tabla)->get() as $fila) {
                foreach ($columnas as $columna) {
                    $valor = (string) ($fila->{$columna} ?? '');

                    foreach (self::VOCABULARIO_AJENO as $palabra) {
                        if (mb_stripos($valor, $palabra) !== false) {
                            $hallazgos[] = sprintf(
                                '%s.%s contiene «%s»: %s',
                                $tabla,
                                $columna,
                                $palabra,
                                mb_substr(preg_replace('/\s+/', ' ', $valor), 0, 120)
                            );
                        }
                    }
                }
            }
        }

        $this->assertSame(
            [],
            array_values(array_unique($hallazgos)),
            "Una instalación limpia publica contenido de la Unidad de Posgrado:\n"
                . implode("\n", array_unique($hallazgos))
        );
    }

    public function test_la_admision_queda_con_estructura_y_sin_proceso(): void
    {
        $this->seed(\Database\Seeders\AdmisionSettingSeeder::class);

        // La fila existe para que el panel tenga dónde escribir desde el primer
        // arranque; lo que no trae es el proceso de nadie.
        foreach (TipoOferta::cases() as $tipo) {
            $ajustes = \App\Models\AdmisionSetting::where('tipo', $tipo->value)->first();

            $this->assertNotNull($ajustes, "Falta la fila de admisión de {$tipo->plural()}.");
            $this->assertEmpty($ajustes->pasos ?: []);
            $this->assertEmpty($ajustes->requisitos_lista ?: []);
            $this->assertNull($ajustes->pago_costo);
            $this->assertSame(0, $ajustes->cronogramaItems()->count());
        }
    }

    public function test_sin_titular_propio_la_api_no_inventa_una_convocatoria(): void
    {
        $this->seed(\Database\Seeders\AdmisionSettingSeeder::class);

        // Anunciar una convocatoria sobre una página que dice que el proceso no
        // está publicado es afirmar algo que la propia página desmiente.
        $this->getJson('/api/v1/admision/talleres')
            ->assertOk()
            ->assertJsonPath('data.titulo', 'Admisión · Talleres')
            ->assertJsonPath('data.convocatorias', []);
    }
}
