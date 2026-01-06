<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Docente;

class DocenteSeeder extends Seeder
{
    public function run(): void
    {
        $docentes = [
            [
                'nombres' => 'Juan Carlos',
                'apellidos' => 'Pérez López',
                'grado' => 'Dr.',
                'email' => 'jperezl@unmsm.edu.pe',
                'biografia' => 'Doctor en Literatura por la UNMSM. Especialista en narrativa peruana del siglo XX.',
                'estado' => 1,
                'foto' => 'profesores/juan-perez.jpg',
                'lineas_investigacion' => ['Narrativa indigenista', 'Literatura y violencia política'],
                'grupo_investigacion' => ['nombre' => 'Estudios Literarios Latinoamericanos', 'link' => ''],
            ],
            [
                'nombres' => 'María Elena',
                'apellidos' => 'García Torres',
                'grado' => 'Dra.',
                'email' => 'mgarciat@unmsm.edu.pe',
                'biografia' => 'Doctora en Lingüística. Investigadora de lenguas andinas y amazónicas.',
                'estado' => 1,
                'foto' => 'profesores/maria-garcia.jpg',
                'lineas_investigacion' => ['Sociolingüística', 'Contacto de lenguas'],
                'grupo_investigacion' => ['nombre' => 'Lenguas en Contacto', 'link' => ''],
            ],
            [
                'nombres' => 'Luis Alberto',
                'apellidos' => 'Sánchez Ruiz',
                'grado' => 'Mg.',
                'email' => 'lsanchezr@unmsm.edu.pe',
                'biografia' => 'Magíster en Filosofía. Docente de ética y filosofía política.',
                'estado' => 1,
                'foto' => 'profesores/luis-sanchez.jpg',
                'lineas_investigacion' => ['Ética contemporánea', 'Filosofía política moderna'],
                'grupo_investigacion' => ['nombre' => 'Ética y Política', 'link' => ''],
            ],
            [
                'nombres' => 'Ana María',
                'apellidos' => 'Flores Quispe',
                'grado' => 'Dra.',
                'email' => 'afloresq@unmsm.edu.pe',
                'biografia' => 'Doctora en Comunicación. Experta en nuevos medios y cultura digital.',
                'estado' => 1,
                'foto' => 'profesores/ana-flores.jpg',
                'lineas_investigacion' => ['Cibercultura', 'Periodismo digital'],
                'grupo_investigacion' => ['nombre' => 'Comunicación y Sociedad', 'link' => ''],
            ],
            [
                'nombres' => 'Carlos Eduardo',
                'apellidos' => 'Mendoza Rojas',
                'grado' => 'Dr.',
                'email' => 'cmendozar@unmsm.edu.pe',
                'biografia' => 'Doctor en Semiótica. Investigador de procesos de significación en la cultura visual.',
                'estado' => 1,
                'foto' => 'profesores/carlos-mendoza.jpg',
                'lineas_investigacion' => ['Semiótica de la imagen', 'Análisis del discurso'],
                'grupo_investigacion' => ['nombre' => 'Semiótica y Cultura', 'link' => ''],
            ],
            [
                'nombres' => 'Rosa Isabel',
                'apellidos' => 'Vargas Machuca',
                'grado' => 'Dra.',
                'email' => 'rvargasm@unmsm.edu.pe',
                'biografia' => 'Doctora en Literatura. Especialista en crónicas y literatura virreinal.',
                'estado' => 1,
                'foto' => 'profesores/rosa-vargas.jpg',
                'lineas_investigacion' => ['Literatura colonial', 'Estudios transatlánticos'],
                'grupo_investigacion' => ['nombre' => 'Estudios Coloniales', 'link' => ''],
            ],
        ];

        foreach ($docentes as $docente) {
            Docente::create($docente);
        }
    }
}
