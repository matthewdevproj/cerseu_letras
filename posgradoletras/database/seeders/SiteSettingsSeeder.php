<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // `site_settings` es de una sola fila: el modelo lo impide en
        // `creating`, y un `create` a secas rompía la cadena de seeders.
        if (SiteSetting::query()->exists()) {
            return;
        }

        SiteSetting::create([
            'site_name' => 'Unidad de Posgrado de Letras',
            'site_description' => 'Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas - Programas de Maestría y Doctorado',
            'logo_path' => null,
            'favicon_path' => null,
            'primary_color' => '#1a365d',
            'secondary_color' => '#2c5282',
            'header_text' => 'Unidad de Posgrado de Letras',
            'footer_text' => '© 2026 Unidad de Posgrado de Letras y Ciencias Humanas. Todos los derechos reservados.',
            'email' => 'posgrado.letras@universidad.edu.pe',
            'email_admision' => 'admision.posgrado@universidad.edu.pe',
            'telefono' => '(01) 619-7000 anexo 2100',
            'direccion' => 'Av. Universitaria 1801, San Miguel, Lima 15088',
            'horario_atencion' => 'Lunes a Viernes: 9:00 AM - 5:00 PM',
            'facebook' => 'https://facebook.com/posgradoletras',
            'instagram' => 'https://instagram.com/posgradoletras',
            'twitter' => 'https://twitter.com/posgradoletras',
            'linkedin' => 'https://linkedin.com/company/posgrado-letras',
            'youtube' => 'https://youtube.com/@posgradoletras',
            'tiktok' => null,
            'web_facultad' => 'https://letras.universidad.edu.pe',
            'directorio_facultad' => 'https://letras.universidad.edu.pe/directorio',
        ]);
    }
}
