<?php

namespace Database\Seeders;

use App\Models\Docente;
use Illuminate\Database\Seeder;

class DocentesSeeder extends Seeder
{
    public function run(): void
    {
        $docentes = [
            [
                'nombres' => 'María',
                'apellidos' => 'García Pérez',
                'grado' => 'Dra.',
                'email' => 'maria.garcia@universidad.edu.pe',
                'orcid' => '0000-0001-1234-5678',
                'cti_vitae' => 'https://ctivitae.concytec.gob.pe/cvweb/12345678',
                'linkedin' => 'https://linkedin.com/in/mariagarcia',
                'biografia' => 'Doctora en Literatura Latinoamericana con más de 15 años de experiencia en investigación y docencia. Especialista en narrativa contemporánea.',
                'estado' => 1,
                'lineas_investigacion' => [
                    'Narrativa latinoamericana contemporánea',
                    'Estudios culturales',
                    'Literatura y memoria'
                ],
                'grupo_investigacion' => [
                    'Literatura y Sociedad',
                    'Estudios Literarios Contemporáneos'
                ],
            ],
            [
                'nombres' => 'José',
                'apellidos' => 'Rodríguez López',
                'grado' => 'Dr.',
                'email' => 'jose.rodriguez@universidad.edu.pe',
                'orcid' => '0000-0002-2345-6789',
                'cti_vitae' => 'https://ctivitae.concytec.gob.pe/cvweb/23456789',
                'linkedin' => 'https://linkedin.com/in/joserodriguez',
                'biografia' => 'Doctor en Lingüística con especialización en sociolingüística y lenguas andinas. Investigador RENACYT.',
                'estado' => 1,
                'lineas_investigacion' => [
                    'Sociolingüística',
                    'Lenguas andinas',
                    'Política lingüística'
                ],
                'grupo_investigacion' => [
                    'Estudios del Lenguaje',
                    'Lenguas Originarias del Perú'
                ],
            ],
            [
                'nombres' => 'Ana',
                'apellidos' => 'Martínez Silva',
                'grado' => 'Dra.',
                'email' => 'ana.martinez@universidad.edu.pe',
                'orcid' => '0000-0003-3456-7890',
                'cti_vitae' => 'https://ctivitae.concytec.gob.pe/cvweb/34567890',
                'linkedin' => null,
                'biografia' => 'Especialista en literatura colonial y estudios virreinales. Ha publicado numerosos artículos en revistas indexadas.',
                'estado' => 1,
                'lineas_investigacion' => [
                    'Literatura colonial',
                    'Estudios virreinales',
                    'Crónicas de Indias'
                ],
                'grupo_investigacion' => [
                    'Literatura Colonial y Virreinal'
                ],
            ],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Sánchez Torres',
                'grado' => 'Mg.',
                'email' => 'carlos.sanchez@universidad.edu.pe',
                'orcid' => '0000-0004-4567-8901',
                'cti_vitae' => 'https://ctivitae.concytec.gob.pe/cvweb/45678901',
                'linkedin' => 'https://linkedin.com/in/carlossanchez',
                'biografia' => 'Magíster en Literatura Peruana, especializado en poesía del siglo XX. Docente con 10 años de experiencia.',
                'estado' => 1,
                'lineas_investigacion' => [
                    'Poesía peruana contemporánea',
                    'Generación del 50',
                    'Literatura urbana'
                ],
                'grupo_investigacion' => [
                    'Poesía y Modernidad'
                ],
            ],
            [
                'nombres' => 'Laura',
                'apellidos' => 'Fernández Rojas',
                'grado' => 'Dra.',
                'email' => 'laura.fernandez@universidad.edu.pe',
                'orcid' => '0000-0005-5678-9012',
                'cti_vitae' => 'https://ctivitae.concytec.gob.pe/cvweb/56789012',
                'linkedin' => 'https://linkedin.com/in/laurafernandez',
                'biografia' => 'Doctora en Lingüística Aplicada, especialista en adquisición de segunda lengua y lingüística cognitiva.',
                'estado' => 1,
                'lineas_investigacion' => [
                    'Lingüística cognitiva',
                    'Adquisición de lenguas',
                    'Psicolingüística'
                ],
                'grupo_investigacion' => [
                    'Lingüística Cognitiva y Aplicada'
                ],
            ],
        ];

        foreach ($docentes as $docente) {
            Docente::create($docente);
        }
    }
}
