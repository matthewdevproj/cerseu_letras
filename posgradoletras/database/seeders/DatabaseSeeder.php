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

            // Sin estos tres, una instalación nueva arranca con el menú vacío
            // y con /tramites, /admision y /nosotros en blanco: ese contenido
            // es administrable y no vive en las vistas.
            MenuItemSeeder::class,
            ContenidoInicialSeeder::class,
            NosotrosContentSeeder::class,
        ]);
    }
}
