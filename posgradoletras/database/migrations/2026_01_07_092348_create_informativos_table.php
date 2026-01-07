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
        Schema::create('informativos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria', 100);
            $table->string('titulo');
            $table->tinyInteger('tipo')->default(0)->comment('0=URL/PDF, 1=Enlace externo');
            $table->text('url')->nullable();
            $table->integer('orden')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informativos');
    }
};
