<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La oferta del CERSEU pasa a ser talleres y cursos.
     *
     * El sitio venía de la Unidad de Posgrado: maestrías y doctorados como
     * oferta principal, y los diplomados como un módulo aparte con sus propias
     * tablas. El CERSEU no otorga grados académicos, así que queda un solo
     * esquema con dos tipos de oferta corta —`taller` y `curso`— que se
     * diferencian en la duración y en nada más.
     *
     * Duplicar las tablas de diplomados habría dejado dos copias que mantener
     * en paralelo. Como su esquema ya era genérico —ni una columna propia de
     * diplomados—, basta quitarles el «diplomado» del nombre y agregarles un
     * discriminador `tipo`.
     *
     * Sobre los datos: los diplomados existentes son talleres, y las maestrías
     * y doctorados se convierten en cursos conservando su contenido (plan de
     * estudios, docentes, inversión, testimonios) en vez de borrarse.
     */
    public function up(): void
    {
        Schema::rename('admision_diplomado_settings', 'admision_settings');
        Schema::rename('admision_diplomado_cronograma_items', 'admision_cronograma_items');
        Schema::rename('diplomado_leads', 'leads');

        Schema::table('admision_cronograma_items', function (Blueprint $table) {
            $table->renameColumn('admision_diplomado_setting_id', 'admision_setting_id');
        });

        // Sin unique todavía: la columna nace con el mismo valor en todas las
        // filas y el índice se agrega una vez repartidos los tipos.
        Schema::table('admision_settings', function (Blueprint $table) {
            $table->string('tipo')->default('taller')->after('id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->string('tipo')->default('taller')->after('id');
        });

        DB::table('admision_settings')->update(['tipo' => 'taller']);
        DB::table('leads')->update(['tipo' => 'taller']);

        // Puede haber más de una fila de ajustes de antes: el código siempre
        // leía la primera con ->first(). Se conserva esa como la de talleres y
        // el resto se descarta, que es lo que el sitio venía mostrando igual.
        $sobrantes = DB::table('admision_settings')->orderBy('id')->pluck('id')->skip(1);
        if ($sobrantes->isNotEmpty()) {
            DB::table('admision_cronograma_items')->whereIn('admision_setting_id', $sobrantes)->delete();
            DB::table('admision_settings')->whereIn('id', $sobrantes)->delete();
        }

        Schema::table('admision_settings', function (Blueprint $table) {
            $table->unique('tipo');
        });

        DB::table('admision_settings')->insertOrIgnore([
            'tipo' => 'curso',
            'hero_titulo' => 'Convocatoria 2026-I',
            'hero_subtitulo' => 'Sección Cursos · CERSEU',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // El grado cambia también en los datos, no solo en la interfaz: el
        // código compara contra este valor en una veintena de sitios y tener
        // el rótulo y el dato en desacuerdo se presta a errores.
        DB::table('programas')->where('grado', 'Diplomado')->update(['grado' => 'Taller']);
        DB::table('programas')->whereIn('grado', ['Maestría', 'Doctorado'])->update(['grado' => 'Curso']);

        $this->renombrarHero('diplomados', 'talleres');

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('cursos_hero_titulo')->nullable();
            $table->text('cursos_hero_texto')->nullable();
            $table->string('cursos_hero_claim')->nullable();
            $table->string('cursos_hero_imagen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Deja el esquema como estaba. No devuelve los grados: una vez convertidos,
     * un curso creado desde cero y una maestría convertida son la misma fila y
     * no hay cómo distinguirlas. Todo vuelve a `Diplomado`, que es el único
     * grado que el módulo de diplomados sabía servir.
     */
    public function down(): void
    {
        foreach (['titulo', 'texto', 'claim', 'imagen'] as $campo) {
            if (Schema::hasColumn('site_settings', "cursos_hero_{$campo}")) {
                Schema::table('site_settings', function (Blueprint $table) use ($campo) {
                    $table->dropColumn("cursos_hero_{$campo}");
                });
            }
        }

        $this->renombrarHero('talleres', 'diplomados');

        DB::table('programas')->whereIn('grado', ['Taller', 'Curso'])->update(['grado' => 'Diplomado']);

        $deCursos = DB::table('admision_settings')->where('tipo', 'curso')->pluck('id');
        if ($deCursos->isNotEmpty()) {
            DB::table('admision_cronograma_items')->whereIn('admision_setting_id', $deCursos)->delete();
            DB::table('admision_settings')->whereIn('id', $deCursos)->delete();
        }
        DB::table('leads')->where('tipo', 'curso')->delete();

        Schema::table('admision_settings', function (Blueprint $table) {
            $table->dropUnique(['tipo']);
            $table->dropColumn('tipo');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });

        Schema::table('admision_cronograma_items', function (Blueprint $table) {
            $table->renameColumn('admision_setting_id', 'admision_diplomado_setting_id');
        });

        Schema::rename('leads', 'diplomado_leads');
        Schema::rename('admision_cronograma_items', 'admision_diplomado_cronograma_items');
        Schema::rename('admision_settings', 'admision_diplomado_settings');
    }

    /**
     * Renombra los cuatro campos del hero de un módulo.
     *
     * Uno por bloque: en SQLite cada renombrado reconstruye la tabla, y
     * agruparlos en un solo Schema::table hace que el segundo trabaje sobre un
     * esquema que ya cambió bajo sus pies.
     */
    private function renombrarHero(string $de, string $a): void
    {
        foreach (['titulo', 'texto', 'claim', 'imagen'] as $campo) {
            if (! Schema::hasColumn('site_settings', "{$de}_hero_{$campo}")) {
                continue;
            }
            Schema::table('site_settings', function (Blueprint $table) use ($de, $a, $campo) {
                $table->renameColumn("{$de}_hero_{$campo}", "{$a}_hero_{$campo}");
            });
        }
    }
};
