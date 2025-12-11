<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('docente_programa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->onDelete('cascade');
            $table->foreignId('programa_id')->constrained('programas')->onDelete('cascade');
            $table->boolean('es_coordinador')->default(false);
            $table->string('rol', 100)->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps(); // Agregado

            $table->unique(['docente_id', 'programa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_programa');
    }
};
