<?php

namespace Database\Seeders;

use App\Models\Programa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DiplomadoSeeder extends Seeder
{
    public function run(): void
    {
        $diplomados = [
            [
                'grado'    => 'Diplomado',
                'nombre'   => 'Redacción Académica y Científica',
                'mencion'  => null,
                'modalidad' => 'Semipresencial',
                'duracion' => 2,
                'vacantes' => 30,
                'creditos' => 24,
                'sumilla'  => 'El Diplomado en Redacción Académica y Científica forma especialistas capaces de producir textos académicos y científicos con rigor metodológico, claridad expositiva y dominio de las normas de citación internacionales. Está orientado a docentes, investigadores, estudiantes de posgrado y profesionales que requieran fortalecer sus competencias comunicativas en contextos académicos.',
                'modalidad' => 'Semipresencial',
                'is_active' => true,
                'objetivos_academicos' => [
                    'Desarrollar competencias para la redacción de artículos científicos, tesis y documentos académicos de alto nivel.',
                    'Aplicar las normas internacionales de citación y referenciación (APA, Chicago, MLA) en la producción académica.',
                    'Fortalecer la capacidad crítica y argumentativa en la escritura académica.',
                    'Elaborar proyectos de investigación con estructura y rigor metodológico adecuados.',
                ],
                'perfil_ingresante' => [
                    'Bachilleres, licenciados o magísteres en cualquier disciplina.',
                    'Interés en el desarrollo de habilidades de escritura académica y científica.',
                    'Docentes universitarios y profesionales que deseen mejorar su producción académica.',
                ],
                'perfil_graduado' => [
                    'Redactar textos académicos y científicos con claridad, coherencia y rigor argumentativo.',
                    'Aplicar con solvencia las normas de citación y estilo científico internacionales.',
                    'Planificar y ejecutar proyectos de investigación con una estructura metodológica sólida.',
                    'Evaluar críticamente publicaciones académicas y científicas de su área de especialidad.',
                ],
                'plan_estudios' => [
                    ['ciclo' => '1', 'nombre' => 'Fundamentos de la Escritura Académica', 'creditos' => 4, 'sumilla' => 'Introduce los principios básicos de la comunicación académica: coherencia, cohesión, claridad y adecuación al registro formal. Se estudian géneros discursivos académicos y científicos.'],
                    ['ciclo' => '1', 'nombre' => 'Gramática y Estilo en la Escritura Científica', 'creditos' => 4, 'sumilla' => 'Revisión de las estructuras gramaticales del español académico. Análisis de errores frecuentes y estrategias para lograr precisión léxica y fluidez estilística en textos científicos.'],
                    ['ciclo' => '1', 'nombre' => 'Normas de Citación y Referenciación Internacional', 'creditos' => 4, 'sumilla' => 'Estudio comparado de los sistemas de citación APA, Chicago y MLA. Práctica en la elaboración de bibliografías, notas al pie y referencias integradas en el texto académico.'],
                    ['ciclo' => '2', 'nombre' => 'Redacción de Artículos Científicos y Ensayos', 'creditos' => 4, 'sumilla' => 'Metodología para la elaboración de artículos científicos: estructura IMRD (Introducción, Métodos, Resultados y Discusión). Taller de escritura de ensayos argumentativos y reseñas bibliográficas.'],
                    ['ciclo' => '2', 'nombre' => 'Diseño de Proyectos de Investigación', 'creditos' => 4, 'sumilla' => 'Planificación y redacción de proyectos de investigación académica. Formulación de problemas, objetivos, hipótesis y marco teórico. Diseño metodológico y cronograma de actividades.'],
                    ['ciclo' => '2', 'nombre' => 'Taller de Producción Académica', 'creditos' => 4, 'sumilla' => 'Espacio práctico para la elaboración y revisión de textos académicos propios. Los participantes desarrollan un artículo o proyecto final con acompañamiento docente, aplicando todos los contenidos del diplomado.'],
                ],
            ],
        ];

        foreach ($diplomados as $data) {
            $base = $data['nombre'];
            $slug = Str::slug($base);
            $original = $slug;
            $count = 1;
            while (Programa::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $count++;
            }
            $data['slug'] = $slug;

            Programa::create($data);
        }
    }
}
