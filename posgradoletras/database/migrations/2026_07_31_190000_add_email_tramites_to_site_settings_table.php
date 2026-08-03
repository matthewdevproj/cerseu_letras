<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Correo de trámites, el único dato de contacto que solo vivía en
     * `config/contacts.php` y por tanto exigía un despliegue para cambiarlo.
     * Con esto todos los contactos del sitio se editan desde el panel.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('email_tramites')->nullable()->after('email_admision');
        });

        // Se arranca con el valor que el sitio ya venía mostrando.
        DB::table('site_settings')
            ->whereNull('email_tramites')
            ->update(['email_tramites' => config('contacts.tramites', 'upg.letras@unmsm.edu.pe')]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('email_tramites');
        });
    }
};
