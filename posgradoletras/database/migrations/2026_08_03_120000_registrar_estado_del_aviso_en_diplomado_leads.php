<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Deja constancia de si el aviso por correo de cada solicitud llegó a salir.
 *
 * El envío va dentro de un `try/catch` que no interrumpe al visitante —correcto,
 * la solicitud no se debe perder porque falle el correo—, pero hasta ahora el
 * fallo solo se escribía en el log. Nadie en la Unidad lee el log, así que una
 * solicitud sin avisar era indistinguible de una avisada.
 *
 * Con estas dos columnas el panel puede señalarlas y ofrecer reenviar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diplomado_leads', function (Blueprint $table) {
            $table->timestamp('aviso_enviado_en')->nullable()->after('programa_id');
            $table->string('aviso_error')->nullable()->after('aviso_enviado_en');
        });

        // Las solicitudes anteriores a esta migración quedan con `null`, que es
        // «no se sabe». Marcarlas como pendientes daría una lista de avisos por
        // reenviar que en realidad puede que sí salieran.
    }

    public function down(): void
    {
        Schema::table('diplomado_leads', function (Blueprint $table) {
            $table->dropColumn(['aviso_enviado_en', 'aviso_error']);
        });
    }
};
