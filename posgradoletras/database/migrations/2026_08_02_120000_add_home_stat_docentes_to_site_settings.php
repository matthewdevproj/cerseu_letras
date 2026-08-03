<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cifra de docentes RENACYT del contador de la portada.
 *
 * Las otras cuatro cifras salen de datos (programas por grado y los años desde
 * la fundación). Esta no se puede calcular —ninguna tabla guarda la condición
 * RENACYT de un docente— y estaba escrita en la plantilla, así que envejecía
 * en silencio igual que los «473 años». Al menos que se edite sin desplegar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->unsignedInteger('home_stat_docentes')->nullable()->after('home_hero_cta2_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('home_stat_docentes');
        });
    }
};
