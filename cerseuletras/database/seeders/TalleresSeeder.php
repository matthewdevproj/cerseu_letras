<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\Programa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TalleresSeeder extends Seeder
{
    /**
     * Datos oficiales tomados de "Ajustes para la página web de Diplomados.pdf"
     * (Observación N.° 3, cronograma y costos de derechos de enseñanza).
     */
    public function run(): void
    {
        $planEstudiosGenerico = [
            ['ciclo' => '1', 'nombre' => 'Fundamentos Teóricos y Metodológicos', 'creditos' => 6, 'sumilla' => 'Marco teórico y metodológico base del taller.'],
            ['ciclo' => '1', 'nombre' => 'Seminario de Especialización I', 'creditos' => 6, 'sumilla' => 'Profundización en las principales líneas temáticas del curso.'],
            ['ciclo' => '2', 'nombre' => 'Seminario de Especialización II', 'creditos' => 6, 'sumilla' => 'Aplicación práctica de herramientas y casos de estudio.'],
            ['ciclo' => '2', 'nombre' => 'Taller de Proyecto Final', 'creditos' => 6, 'sumilla' => 'Elaboración y sustentación del trabajo final del taller.'],
        ];

        $diplomados = [
            [
                'nombre' => 'Taller en Curaduría con Énfasis en Arte Peruano y Latinoamericano Moderno y Contemporáneo',
                'costo_total' => 3000,
                'fecha_limite_inscripcion' => '25 de septiembre de 2026',
                'coordinador' => ['nombres' => 'Claudia', 'apellidos' => 'Rodríguez Salinas'],
            ],
            [
                'nombre' => 'Taller en Filosofía de la Educación, Ética y Epistemología de las Ciencias Sociales',
                'costo_total' => 1200,
                'fecha_limite_inscripcion' => '28 de septiembre de 2026',
                'coordinador' => ['nombres' => 'Jorge Luis', 'apellidos' => 'Mendoza Vargas'],
            ],
            [
                'nombre' => 'Taller en Gestión Cultural y Desarrollo de Públicos',
                'costo_total' => 3000,
                'fecha_limite_inscripcion' => '25 de septiembre de 2026',
                'coordinador' => ['nombres' => 'Ana María', 'apellidos' => 'Quispe Huamán'],
            ],
            [
                'nombre' => 'Taller en Proyectos de Innovación Social con Inteligencia Artificial en Educación y Comunicaciones',
                'costo_total' => 1500,
                'fecha_limite_inscripcion' => '25 de septiembre de 2026',
                'coordinador' => ['nombres' => 'Renato', 'apellidos' => 'Fernández Castro'],
            ],
            [
                'nombre' => 'Taller Internacional de Lingüística Forense',
                'costo_total' => 4450,
                'fecha_limite_inscripcion' => '25 de septiembre de 2026',
                'observaciones' => 'El costo incluye matrícula.',
                'coordinador' => ['nombres' => 'Patricia', 'apellidos' => 'Ibáñez Torres'],
            ],
            [
                'nombre' => 'Taller Internacional en Corrección Lingüística',
                'costo_total' => 4000,
                'fecha_limite_inscripcion' => '28 de septiembre de 2026',
                'coordinador' => ['nombres' => 'Miguel Ángel', 'apellidos' => 'Torres Rojas'],
            ],
        ];

        foreach ($diplomados as $data) {
            $slug = Str::slug($data['nombre']);
            $original = $slug;
            $count = 1;
            while (Programa::where('slug', $slug)->exists()) {
                $slug = $original . '-' . $count++;
            }

            $cuota = round($data['costo_total'] / 2);

            $programa = Programa::create([
                'grado' => 'Taller',
                'nombre' => $data['nombre'],
                'mencion' => null,
                'modalidad' => 'Virtual',
                'duracion' => 2,
                'vacantes' => 30,
                'creditos' => 24,
                'horas_academicas' => 480,
                'fecha_limite_inscripcion' => $data['fecha_limite_inscripcion'],
                'sumilla' => "El {$data['nombre']} está dirigido a profesionales y especialistas que buscan actualizar y profundizar sus conocimientos desde una perspectiva académica rigurosa, con un enfoque virtual y aplicado.",
                'is_active' => true,
                'slug' => $slug,
                'objetivos_academicos' => [
                    'Fortalecer competencias especializadas de acuerdo a los ejes temáticos del diplomado.',
                    'Aplicar herramientas metodológicas actualizadas a problemas reales del campo de especialización.',
                ],
                'perfil_ingresante' => [
                    'Bachilleres, licenciados o profesionales con interés en el área de especialización del diplomado.',
                ],
                'perfil_graduado' => [
                    'Egresa con competencias especializadas aplicables a su ejercicio profesional o académico.',
                ],
                'plan_estudios' => $planEstudiosGenerico,
                'inversion_economica' => [
                    'derecho_inscripcion' => [
                        'bachiller_unmsm' => 200,
                        'otras_universidades' => 280,
                    ],
                    'costo_total' => $data['costo_total'],
                    'costo_diploma' => 650,
                    'cuotas' => [
                        ['numero' => 1, 'monto' => $cuota, 'fecha' => 'Hasta 3 días hábiles después de publicados los resultados'],
                        ['numero' => 2, 'monto' => $data['costo_total'] - $cuota, 'fecha' => 'Entre el 15 y el 30 de noviembre de 2026'],
                    ],
                    'modalidades_pago' => ['Pago único', 'Pago en dos cuotas'],
                    'descuentos' => null,
                    'observaciones' => $data['observaciones']
                        ?? 'De no realizar el pago dentro de los 3 días hábiles posteriores a la publicación de resultados, se pierde la vacante automáticamente.',
                ],
            ]);

            $coordinador = Docente::create([
                'nombres' => $data['coordinador']['nombres'],
                'apellidos' => $data['coordinador']['apellidos'],
                'grado' => 'Dr.',
                'email' => Str::slug($data['coordinador']['nombres'], '') . '.' . Str::slug($data['coordinador']['apellidos'], '') . '@unmsm.edu.pe',
                'estado' => 1,
            ]);

            $programa->docentes()->attach($coordinador->id, [
                'es_coordinador' => true,
                'rol' => 'Coordinador',
                'orden' => 0,
            ]);
        }
    }
}
