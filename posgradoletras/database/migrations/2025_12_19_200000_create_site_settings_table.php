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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Información General
            $table->string('site_name')->default('Unidad de Posgrado - Facultad de Letras y Ciencias Humanas');
            $table->text('site_description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();

            // Colores
            $table->string('primary_color')->default('#7f1d1d'); // guinda
            $table->string('secondary_color')->default('#fbbf24'); // dorado

            // Header y Footer
            $table->string('header_text')->nullable();
            $table->string('footer_text')->nullable();

            // Información de Contacto
            $table->string('email')->nullable();
            $table->string('email_admision')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->string('horario_atencion')->nullable();

            // Redes Sociales
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            // URLs externas
            $table->string('web_facultad')->nullable();
            $table->string('directorio_facultad')->nullable();

            $table->timestamps();
        });

        // Insertar configuración inicial con los datos proporcionados
        DB::table('site_settings')->insert([
            'site_name' => 'Unidad de Posgrado - Facultad de Letras y Ciencias Humanas',
            'site_description' => 'Posgrado de la Facultad de Letras y Ciencias Humanas de la Universidad Nacional Mayor de San Marcos',
            'email' => 'posgrado.letras@unmsm.edu.pe',
            'email_admision' => 'admisionposgrado.letras@unmsm.edu.pe',
            'telefono' => '982 085 037',
            'direccion' => 'Ciudad Universitaria, Av. Venezuela s/n, Lima',
            'facebook' => 'https://www.facebook.com/posgradoletrasUNMSM/',
            'instagram' => 'https://www.instagram.com/posgradoletrasunmsm/',
            'twitter' => 'https://x.com/PGLetras_UNMSM',
            'linkedin' => 'https://www.linkedin.com/in/posgrado-de-la-facultad-de-letras-unmsm-1a95862ab/',
            'youtube' => null,
            'tiktok' => 'https://www.tiktok.com/@posgradoletrasunmsm',
            'web_facultad' => 'https://letras.unmsm.edu.pe',
            'directorio_facultad' => 'https://letras.unmsm.edu.pe/directorio/',
            'primary_color' => '#7f1d1d',
            'secondary_color' => '#fbbf24',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
