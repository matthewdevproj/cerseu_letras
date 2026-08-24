<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 150);
            $table->string('apellidos', 150);
            $table->string('grado', 100)->nullable();
            $table->string('especialidad', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('orcid', 100)->nullable();
            $table->string('cti_vitae', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto')->nullable(); // Agregado
            $table->json('lineas_investigacion')->nullable(); // Agregado
            $table->string('grupo_investigacion')->nullable(); // Agregado
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
