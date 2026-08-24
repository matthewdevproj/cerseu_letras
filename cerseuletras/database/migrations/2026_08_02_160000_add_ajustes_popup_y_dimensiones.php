<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dimensiones reales de la imagen y ajustes del popup.
 *
 * Las dimensiones evitan que la ventana dé un salto al cargar: el componente
 * declaraba 520x650 fijos, y desde que la caja se adapta a la imagen esa
 * medida inventada provocaba un reflow de 260 px en cuanto llegaba el archivo.
 *
 * Los ajustes (retardo, repetición, avance automático) estaban escritos en la
 * llamada del componente dentro de `home.blade.php`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->unsignedSmallInteger('imagen_ancho')->nullable()->after('imagen');
            $table->unsignedSmallInteger('imagen_alto')->nullable()->after('imagen_ancho');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('popup_retardo_ms')->nullable()->after('home_stat_docentes');
            $table->string('popup_frecuencia', 20)->nullable()->after('popup_retardo_ms');
            $table->boolean('popup_auto_avance')->default(false)->after('popup_frecuencia');
        });
    }

    public function down(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn(['imagen_ancho', 'imagen_alto']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['popup_retardo_ms', 'popup_frecuencia', 'popup_auto_avance']);
        });
    }
};
