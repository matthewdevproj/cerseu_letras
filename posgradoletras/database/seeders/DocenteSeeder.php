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
                'especialidad' => 'Literatura Latinoamericana',
                'email' => 'jperezl@unmsm.edu.pe',
                'telefono' => '999888777',
                'biografia' => 'Doctor en Literatura por la UNMSM. Especialista en narrativa peruana del siglo XX.',
                'estado' => 1,
                'foto' => 'profesores/juan-perez.jpg',
                'lineas_investigacion' => ['Narrativa indigenista', 'Literatura y violencia política'],
                'grupo_investigacion' => 'Estudios Literarios Latinoamericanos',
            ],
            [
                'nombres' => 'María Elena',
                'apellidos' => 'García Torres',
                'grado' => 'Dra.',
                'especialidad' => 'Lingüística Andina',
                'email' => 'mgarciat@unmsm.edu.pe',
                'telefono' => '999111222',
                'biografia' => 'Doctora en Lingüística. Investigadora de lenguas andinas y amazónicas.',
                'estado' => 1,
                'foto' => 'profesores/maria-garcia.jpg',
                'lineas_investigacion' => ['Sociolingüística', 'Contacto de lenguas'],
                'grupo_investigacion' => 'Lenguas en Contacto',
            ],
            [
                'nombres' => 'Luis Alberto',
                'apellidos' => 'Sánchez Ruiz',
                'grado' => 'Mg.',
                'especialidad' => 'Filosofía Política',
                'email' => 'lsanchezr@unmsm.edu.pe',
                'telefono' => '999333444',
                'biografia' => 'Magíster en Filosofía. Docente de ética y filosofía política.',
                'estado' => 1,
                'foto' => 'profesores/luis-sanchez.jpg',
                'lineas_investigacion' => ['Ética contemporánea', 'Filosofía política moderna'],
                'grupo_investigacion' => 'Ética y Política',
            ],
            [
                'nombres' => 'Ana María',
                'apellidos' => 'Flores Quispe',
                'grado' => 'Dra.',
                'especialidad' => 'Comunicación Digital',
                'email' => 'afloresq@unmsm.edu.pe',
                'telefono' => '999555666',
                'biografia' => 'Doctora en Comunicación. Experta en nuevos medios y cultura digital.',
                'estado' => 1,
                'foto' => 'profesores/ana-flores.jpg',
                'lineas_investigacion' => ['Cibercultura', 'Periodismo digital'],
                'grupo_investigacion' => 'Comunicación y Sociedad',
            ],
            [
                'nombres' => 'Carlos Eduardo',
                'apellidos' => 'Mendoza Rojas',
                'grado' => 'Dr.',
                'especialidad' => 'Semiótica',
                'email' => 'cmendozar@unmsm.edu.pe',
                'telefono' => '999777888',
                'biografia' => 'Doctor en Semiótica. Investigador de procesos de significación en la cultura visual.',
                'estado' => 1,
                'foto' => 'profesores/carlos-mendoza.jpg',
                'lineas_investigacion' => ['Semiótica de la imagen', 'Análisis del discurso'],
                'grupo_investigacion' => 'Semiótica y Cultura',
            ],
            [
                'nombres' => 'Rosa Isabel',
                'apellidos' => 'Vargas Machuca',
                'grado' => 'Dra.',
                'especialidad' => 'Literatura Colonial',
                'email' => 'rvargasm@unmsm.edu.pe',
                'telefono' => '999000111',
                'biografia' => 'Doctora en Literatura. Especialista en crónicas y literatura virreinal.',
                'estado' => 1,
                'foto' => 'profesores/rosa-vargas.jpg',
                'lineas_investigacion' => ['Literatura colonial', 'Estudios transatlánticos'],
                'grupo_investigacion' => 'Estudios Coloniales',
            ],
        ];

        foreach ($docentes as $docente) {
            Docente::create($docente);
        }
    }
}
