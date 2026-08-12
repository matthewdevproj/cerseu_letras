<?php

namespace Database\Seeders;

use App\Models\Programa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProgramasSeeder extends Seeder
{
    public function run(): void
    {
        $programas = [
            [
                'grado' => 'Maestría',
                'nombre' => 'Literatura Peruana y Latinoamericana',
                'mencion' => null,
                'modalidad' => 'Presencial',
                'vacantes' => 20,
                'duracion' => 4,
                'creditos' => 48,
                'grado_otorga_label' => 'Grado que otorga',
                'grado_otorga' => 'Magíster en Literatura Peruana y Latinoamericana',
                'slug' => Str::slug('Literatura Peruana y Latinoamericana'),
                'objetivos_academicos' => [
                    'Formar investigadores en literatura peruana y latinoamericana',
                    'Desarrollar competencias críticas y analíticas',
                    'Promover la investigación literaria de calidad'
                ],
                'perfil_ingresante' => [
                    'Bachiller en Literatura, Lingüística o áreas afines',
                    'Interés en la investigación literaria',
                    'Conocimiento básico de teorías literarias'
                ],
                'perfil_graduado' => [
                    'Investigador especializado en literatura peruana y latinoamericana',
                    'Capacidad para realizar análisis crítico literario',
                    'Aptitud para la docencia universitaria'
                ],
                'plan_url' => null,
                'horario_url' => null,
                'por_que_text' => 'Porque es el único programa en el país con este enfoque especializado en literatura peruana y latinoamericana.',
                'sumilla' => 'Programa de maestría que forma investigadores especializados en el estudio crítico de la literatura peruana y latinoamericana.',
                'plan_estudios' => [
                    'Semestre I' => ['Teoría Literaria Contemporánea', 'Literatura Colonial', 'Seminario de Investigación I'],
                    'Semestre II' => ['Literatura del Siglo XIX', 'Literatura del Siglo XX', 'Seminario de Investigación II'],
                ],
                'is_active' => true,
            ],
            [
                'grado' => 'Maestría',
                'nombre' => 'Lingüística',
                'mencion' => null,
                'modalidad' => 'Presencial',
                'vacantes' => 15,
                'duracion' => 4,
                'creditos' => 48,
                'grado_otorga_label' => 'Grado que otorga',
                'grado_otorga' => 'Magíster en Lingüística',
                'slug' => Str::slug('Lingüística'),
                'objetivos_academicos' => [
                    'Formar especialistas en análisis lingüístico',
                    'Desarrollar investigación en lingüística aplicada',
                    'Promover el estudio de lenguas peruanas'
                ],
                'perfil_ingresante' => [
                    'Bachiller en Lingüística, Literatura o áreas afines',
                    'Conocimientos básicos de lingüística general',
                    'Interés en la investigación del lenguaje'
                ],
                'perfil_graduado' => [
                    'Investigador especializado en lingüística',
                    'Capacidad de análisis lingüístico avanzado',
                    'Docente calificado en lingüística'
                ],
                'plan_url' => null,
                'horario_url' => null,
                'por_que_text' => 'Por su enfoque integral en lingüística teórica y aplicada con énfasis en lenguas peruanas.',
                'sumilla' => 'Maestría especializada en el estudio científico del lenguaje y sus aplicaciones.',
                'plan_estudios' => [
                    'Semestre I' => ['Teoría Lingüística', 'Fonología', 'Seminario de Investigación I'],
                    'Semestre II' => ['Sintaxis', 'Semántica', 'Seminario de Investigación II'],
                ],
                'is_active' => true,
            ],
            [
                'grado' => 'Doctorado',
                'nombre' => 'Literatura Peruana y Latinoamericana',
                'mencion' => null,
                'modalidad' => 'Presencial',
                'vacantes' => 10,
                'duracion' => 6,
                'creditos' => 64,
                'grado_otorga_label' => 'Grado que otorga',
                'grado_otorga' => 'Doctor en Literatura Peruana y Latinoamericana',
                'slug' => Str::slug('Doctorado Literatura Peruana y Latinoamericana'),
                'objetivos_academicos' => [
                    'Formar investigadores de alto nivel en literatura',
                    'Producir conocimiento original en estudios literarios',
                    'Desarrollar liderazgo académico en el campo'
                ],
                'perfil_ingresante' => [
                    'Magíster en Literatura o áreas afines',
                    'Proyecto de investigación sólido',
                    'Experiencia en investigación literaria'
                ],
                'perfil_graduado' => [
                    'Investigador de alto nivel académico',
                    'Productor de conocimiento original',
                    'Líder en estudios literarios latinoamericanos'
                ],
                'plan_url' => null,
                'horario_url' => null,
                'por_que_text' => 'Es el único doctorado del país enfocado exclusivamente en literatura peruana y latinoamericana.',
                'sumilla' => 'Doctorado para la formación de investigadores de excelencia en literatura peruana y latinoamericana.',
                'plan_estudios' => [
                    'Año I' => ['Seminario de Investigación Avanzada I', 'Tópicos Avanzados en Literatura'],
                    'Año II' => ['Seminario de Investigación Avanzada II', 'Seminario de Tesis'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($programas as $programa) {
            Programa::create($programa);
        }
    }
}
