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
            
            // Oferta real del CERSEU: la programación 2026 con sus cursos, sus
            // docentes responsables y sus convocatorias. Sustituye a los
            // seeders de demostración (maestrías, doctorados y diplomados
            // inventados) y al reparto aleatorio de docentes por programa, que
            // habría puesto profesores de mentira en fichas reales.
            OfertaCerseuSeeder::class,
            
            // Contenido adicional. Sin testimonios: los que había eran de
            // demostración y hablaban de maestrías y doctorados. La sección de
            // la portada y /testimonios se ocultan solas mientras no haya
            // ninguno, y el panel sigue permitiendo cargar los reales.
            DocumentsSeeder::class,
            CronogramasSeeder::class,
            AdmisionSettingSeeder::class,

            // Sin estos tres, una instalación nueva arranca con el menú vacío
            // y con /tramites, /admision y /nosotros en blanco: ese contenido
            // es administrable y no vive en las vistas.
            MenuItemSeeder::class,
            ContenidoInicialSeeder::class,
            NosotrosContentSeeder::class,
        ]);
    }
}
