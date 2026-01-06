<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cronogramas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // Ej: 2026-I
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('effective_date'); // Fecha de vigencia
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cronograma_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cronograma_id')->constrained()->onDelete('cascade');
            $table->string('section')->nullable(); // Nombre de la sección (Proceso de Admisión, etc.)
            $table->boolean('is_section_heading')->default(false);
            $table->string('actividad');
            $table->string('fecha_text')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        // Tabla pivot para documentos asociados al cronograma
        Schema::create('cronograma_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cronograma_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma_documents');
        Schema::dropIfExists('cronograma_items');
        Schema::dropIfExists('cronogramas');
    }
};
