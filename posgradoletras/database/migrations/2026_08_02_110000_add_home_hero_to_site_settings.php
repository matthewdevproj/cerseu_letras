<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Textos del hero de la portada.
 *
 * Sigue el patrón de los campos `diplomados_hero_*` que ya existían: el hero
 * son campos con forma fija (antetítulo, titular, bajada y dos botones), no
 * texto libre, así que encajan mejor aquí que como secciones HTML.
 *
 * Las columnas quedan nulables a propósito: mientras estén vacías la vista
 * pinta los textos actuales, y el sitio se comporta igual que antes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('home_hero_kicker')->nullable()->after('footer_text');
            $table->string('home_hero_titulo')->nullable()->after('home_hero_kicker');
            $table->text('home_hero_texto')->nullable()->after('home_hero_titulo');
            $table->string('home_hero_cta1_texto')->nullable()->after('home_hero_texto');
            $table->string('home_hero_cta1_url')->nullable()->after('home_hero_cta1_texto');
            $table->string('home_hero_cta2_texto')->nullable()->after('home_hero_cta1_url');
            $table->string('home_hero_cta2_url')->nullable()->after('home_hero_cta2_texto');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_kicker', 'home_hero_titulo', 'home_hero_texto',
                'home_hero_cta1_texto', 'home_hero_cta1_url',
                'home_hero_cta2_texto', 'home_hero_cta2_url',
            ]);
        });
    }
};
