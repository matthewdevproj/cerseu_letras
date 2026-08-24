<?php

use App\Models\TipoOferta;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tercer tipo de oferta: las especializaciones.
     *
     * Y con él, la constatación de que los tres no se miden igual. Un taller se
     * anuncia por horas académicas; un curso por sesiones y horas; una
     * especialización por módulos y meses. Hasta ahora solo existían `duracion`
     * y `horas_academicas`, así que se añaden las dos columnas que faltaban.
     * Qué muestra cada tipo lo decide TipoOferta::medidas(), no las vistas.
     */
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->unsignedInteger('sesiones')->nullable()->after('duracion');
            $table->unsignedInteger('modulos')->nullable()->after('sesiones');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('especializaciones_hero_titulo')->nullable();
            $table->text('especializaciones_hero_texto')->nullable();
            $table->string('especializaciones_hero_claim')->nullable();
            $table->string('especializaciones_hero_imagen')->nullable();
        });

        // Cada módulo necesita su fila de ajustes para que el panel tenga dónde
        // escribir desde el primer momento, igual que talleres y cursos.
        DB::table('admision_settings')->insertOrIgnore([
            'tipo' => TipoOferta::Especializacion->value,
            'hero_titulo' => 'Convocatoria 2026-I',
            'hero_subtitulo' => 'Sección Especializaciones · CERSEU',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $deEspecializacion = DB::table('admision_settings')
            ->where('tipo', TipoOferta::Especializacion->value)->pluck('id');

        if ($deEspecializacion->isNotEmpty()) {
            DB::table('admision_cronograma_items')
                ->whereIn('admision_setting_id', $deEspecializacion)->delete();
            DB::table('admision_settings')->whereIn('id', $deEspecializacion)->delete();
        }

        DB::table('leads')->where('tipo', TipoOferta::Especializacion->value)->delete();
        DB::table('programas')->where('grado', TipoOferta::Especializacion->grado())->delete();

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'especializaciones_hero_titulo',
                'especializaciones_hero_texto',
                'especializaciones_hero_claim',
                'especializaciones_hero_imagen',
            ]);
        });

        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['sesiones', 'modulos']);
        });
    }
};
