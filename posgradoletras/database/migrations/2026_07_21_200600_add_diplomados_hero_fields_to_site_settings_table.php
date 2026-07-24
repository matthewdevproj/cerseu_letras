<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('diplomados_hero_titulo')->nullable()->after('header_text');
            $table->text('diplomados_hero_texto')->nullable()->after('diplomados_hero_titulo');
            $table->string('diplomados_hero_claim')->nullable()->after('diplomados_hero_texto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['diplomados_hero_titulo', 'diplomados_hero_texto', 'diplomados_hero_claim']);
        });
    }
};
