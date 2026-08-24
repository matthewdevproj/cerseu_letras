<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('directorio_posgrado', function (Blueprint $table) {
            $table->id();
            $table->string('unidad_nombre');           // "AUTORIDADES", "PERSONAL ADMINISTRATIVO"
            $table->string('cargo');
            $table->string('nombre_persona');
            $table->string('anexo')->nullable();       // Puede estar vacío
            $table->string('correo_persona')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);  // Para ocultar sin eliminar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directorio_posgrado');
    }
};
