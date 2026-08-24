<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            // Documentos de Admisión
            [
                'type' => 'admision',
                'title' => 'Reglamento de Admisión 2026',
                'original_name' => 'reglamento-admision-2026.pdf',
                'url' => '/documents/reglamento-admision-2026.pdf',
                'published' => true,
            ],
            [
                'type' => 'admision',
                'title' => 'Formato de Solicitud de Admisión',
                'original_name' => 'formato-solicitud-admision.pdf',
                'url' => '/documents/formato-solicitud-admision.pdf',
                'published' => true,
            ],
            [
                'type' => 'admision',
                'title' => 'Guía del Postulante',
                'original_name' => 'guia-postulante-2026.pdf',
                'url' => '/documents/guia-postulante-2026.pdf',
                'published' => true,
            ],
            
            // Documentos Académicos
            [
                'type' => 'academico',
                'title' => 'Reglamento de Estudios de Posgrado',
                'original_name' => 'reglamento-estudios-posgrado.pdf',
                'url' => '/documents/reglamento-estudios-posgrado.pdf',
                'published' => true,
            ],
            [
                'type' => 'academico',
                'title' => 'Reglamento de Grados y Títulos',
                'original_name' => 'reglamento-grados-titulos.pdf',
                'url' => '/documents/reglamento-grados-titulos.pdf',
                'published' => true,
            ],
            [
                'type' => 'academico',
                'title' => 'Formato de Proyecto de Tesis',
                'original_name' => 'formato-proyecto-tesis.docx',
                'url' => '/documents/formato-proyecto-tesis.docx',
                'published' => true,
            ],
            
            // Documentos de Investigación
            [
                'type' => 'investigacion',
                'title' => 'Líneas de Investigación 2026',
                'original_name' => 'lineas-investigacion-2026.pdf',
                'url' => '/documents/lineas-investigacion-2026.pdf',
                'published' => true,
            ],
            [
                'type' => 'investigacion',
                'title' => 'Manual de Estilo para Tesis',
                'original_name' => 'manual-estilo-tesis.pdf',
                'url' => '/documents/manual-estilo-tesis.pdf',
                'published' => true,
            ],
            
            // Otros Documentos
            [
                'type' => 'otro',
                'title' => 'Calendario Académico 2026',
                'original_name' => 'calendario-academico-2026.pdf',
                'url' => '/documents/calendario-academico-2026.pdf',
                'published' => true,
            ],
            [
                'type' => 'otro',
                'title' => 'Directorio de Docentes',
                'original_name' => 'directorio-docentes.pdf',
                'url' => '/documents/directorio-docentes.pdf',
                'published' => true,
            ],
        ];

        foreach ($documents as $document) {
            Document::create($document);
        }
    }
}
