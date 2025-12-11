<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('programas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('slug')->unique()->nullable(); // Agregado
            $table->string('grado', 50);
            $table->string('nombre', 255);
            $table->string('mencion', 255)->nullable();
            $table->string('modalidad', 100)->nullable();
            $table->integer('vacantes')->nullable();
            $table->integer('duracion')->nullable()->comment('En semestres');
            $table->integer('creditos')->nullable();
            $table->string('grado_otorga', 255)->nullable();
            $table->string('plan_url', 255)->nullable();
            $table->text('por_que_text')->nullable();
            $table->text('presentacion')->nullable();
            $table->text('sumilla')->nullable(); // Agregado
            $table->text('descripcion')->nullable(); // Agregado
            $table->string('imagen')->nullable(); // Agregado
            $table->text('perfil_egresado')->nullable();
            $table->json('plan_estudios')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
