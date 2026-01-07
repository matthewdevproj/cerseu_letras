<?php

namespace Database\Seeders;

use App\Models\Cronograma;
use App\Models\CronogramaItem;
use App\Models\Document;
use Illuminate\Database\Seeder;

class CronogramasSeeder extends Seeder
{
    public function run(): void
    {
        // Crear cronograma principal
        $cronograma = Cronograma::create([
            'code' => '2026-I',
            'title' => 'Proceso de Admisión 2026-I',
            'description' => 'Cronograma del proceso de admisión para el primer semestre académico 2026',
            'effective_date' => '2026-01-01',
            'is_active' => true,
        ]);

        // Items del cronograma
        $items = [
            // Inscripción y documentos
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'INSCRIPCIÓN',
                'is_section_heading' => true,
                'actividad' => 'INSCRIPCIÓN',
                'fecha_text' => null,
                'orden' => 1,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'INSCRIPCIÓN',
                'is_section_heading' => false,
                'actividad' => 'Publicación de convocatoria y bases',
                'fecha_text' => '10 de enero de 2026',
                'orden' => 2,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'INSCRIPCIÓN',
                'is_section_heading' => false,
                'actividad' => 'Inscripción y pago de derecho de admisión',
                'fecha_text' => '15 de enero al 15 de febrero de 2026',
                'orden' => 3,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'INSCRIPCIÓN',
                'is_section_heading' => false,
                'actividad' => 'Presentación de expedientes (documentos físicos)',
                'fecha_text' => '15 de enero al 20 de febrero de 2026',
                'orden' => 4,
            ],
            
            // Evaluación
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'EVALUACIÓN',
                'is_section_heading' => true,
                'actividad' => 'EVALUACIÓN',
                'fecha_text' => null,
                'orden' => 5,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'EVALUACIÓN',
                'is_section_heading' => false,
                'actividad' => 'Evaluación de expedientes',
                'fecha_text' => '21 al 28 de febrero de 2026',
                'orden' => 6,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'EVALUACIÓN',
                'is_section_heading' => false,
                'actividad' => 'Publicación de apto para examen de admisión',
                'fecha_text' => '1 de marzo de 2026',
                'orden' => 7,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'EVALUACIÓN',
                'is_section_heading' => false,
                'actividad' => 'Examen de admisión',
                'fecha_text' => '8 de marzo de 2026',
                'orden' => 8,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'EVALUACIÓN',
                'is_section_heading' => false,
                'actividad' => 'Entrevista personal',
                'fecha_text' => '10 al 12 de marzo de 2026',
                'orden' => 9,
            ],
            
            // Resultados
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'RESULTADOS',
                'is_section_heading' => true,
                'actividad' => 'RESULTADOS',
                'fecha_text' => null,
                'orden' => 10,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'RESULTADOS',
                'is_section_heading' => false,
                'actividad' => 'Publicación de resultados finales',
                'fecha_text' => '15 de marzo de 2026',
                'orden' => 11,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'RESULTADOS',
                'is_section_heading' => false,
                'actividad' => 'Matrícula de ingresantes',
                'fecha_text' => '18 al 22 de marzo de 2026',
                'orden' => 12,
            ],
            [
                'cronograma_id' => $cronograma->id,
                'section' => 'RESULTADOS',
                'is_section_heading' => false,
                'actividad' => 'Inicio de clases',
                'fecha_text' => '1 de abril de 2026',
                'orden' => 13,
            ],
        ];

        foreach ($items as $item) {
            CronogramaItem::create($item);
        }

        // Asociar documentos al cronograma (si existen)
        $documentosAdmision = Document::where('type', 'admision')
            ->where('published', true)
            ->take(3)
            ->get();

        if ($documentosAdmision->isNotEmpty()) {
            $position = 1;
            foreach ($documentosAdmision as $doc) {
                $cronograma->documents()->attach($doc->id, ['position' => $position++]);
            }
        }
    }
}
