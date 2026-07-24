<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Usuarios del sistema
            UserSeeder::class,
            
            // Configuración del sitio
            SiteSettingsSeeder::class,
            
            // Datos principales
            ProgramasSeeder::class,
            DiplomadoSeeder::class,
            DocentesSeeder::class,
            DirectorioPosgradoSeeder::class,

            // Relaciones
            DocenteProgramaSeeder::class,
            
            // Contenido adicional
            TestimoniosSeeder::class,
            DocumentsSeeder::class,
            CronogramasSeeder::class,
            AdmisionDiplomadoSettingSeeder::class,
        ]);
    }
}
