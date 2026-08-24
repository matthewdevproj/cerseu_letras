<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * El anexo telefónico, en su propia columna.
     *
     * No vale meterlo dentro de `telefono`: de ahí sale el enlace de WhatsApp,
     * que se arma quitando todo lo que no sea dígito (ver SiteSetting::whatsapp).
     * Un «914 033 129 anexo 2808» habría producido el número 9140331292808.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('anexo', 20)->nullable()->after('telefono');
        });

        DB::table('site_settings')->whereNull('anexo')->update(['anexo' => '2808']);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('anexo');
        });
    }
};
