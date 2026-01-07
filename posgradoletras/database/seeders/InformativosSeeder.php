<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Informativo;

class InformativosSeeder extends Seeder
{
    public function run(): void
    {
        $informativos = [
            // Reglamentos
            ['categoria' => 'Reglamento', 'titulo' => 'Reglamento General de Estudios de Posgrado 2024', 'tipo' => 0, 'url' => '#', 'orden' => 1],
            ['categoria' => 'Reglamento', 'titulo' => 'Reglamento General de Matrícula de Posgrado 2018 – 2023', 'tipo' => 0, 'url' => '#', 'orden' => 2],
            ['categoria' => 'Reglamento', 'titulo' => 'Reglamento de Idiomas de Posgrado', 'tipo' => 0, 'url' => '#', 'orden' => 3],
            ['categoria' => 'Reglamento', 'titulo' => 'Reglamento de Propiedad Intelectual de la UNMSM', 'tipo' => 0, 'url' => '#', 'orden' => 4],
            ['categoria' => 'Reglamento', 'titulo' => 'Actualización de Requisitos para el Otorgamiento de Grado (Doctor / Magíster)', 'tipo' => 0, 'url' => '#', 'orden' => 5],

            // Directivas
            ['categoria' => 'Directiva', 'titulo' => '7a directiva 2025-II de Seminarios de tesis', 'tipo' => 0, 'url' => '#', 'orden' => 1],
            ['categoria' => 'Directiva', 'titulo' => 'Directiva de originalidad y similitud de trabajos académicos, de investigación y producción intelectual', 'tipo' => 0, 'url' => '#', 'orden' => 2],
            ['categoria' => 'Directiva', 'titulo' => 'Pautas para la presentación de las citas y las referencias, de acuerdo con las normas APA 7ma edición', 'tipo' => 0, 'url' => '#', 'orden' => 3],
            ['categoria' => 'Directiva', 'titulo' => 'Directiva para la publicación de artículos UPG Letras', 'tipo' => 0, 'url' => '#', 'orden' => 4],
            ['categoria' => 'Directiva', 'titulo' => 'Directiva de Modelo de Estructura de tesis Maestría y Doctorado', 'tipo' => 0, 'url' => '#', 'orden' => 5],
            ['categoria' => 'Directiva', 'titulo' => 'Código de Ética del Investigador 2023', 'tipo' => 0, 'url' => '#', 'orden' => 6],
            ['categoria' => 'Directiva', 'titulo' => 'Directiva Protocolo de estímulo a la investigación y perfeccionamiento', 'tipo' => 0, 'url' => '#', 'orden' => 7],

            // Información
            ['categoria' => 'Información', 'titulo' => 'Lista de revistas indexadas y acreditadas', 'tipo' => 1, 'url' => '#', 'orden' => 1],
            ['categoria' => 'Información', 'titulo' => 'Cybertesis – Unidad de Posgrado FLCH', 'tipo' => 1, 'url' => 'https://cybertesis.unmsm.edu.pe/', 'orden' => 2],
            ['categoria' => 'Información', 'titulo' => 'Plantilla oficial de Proyecto de Tesis', 'tipo' => 0, 'url' => '#', 'orden' => 3],
        ];

        foreach ($informativos as $informativo) {
            Informativo::create($informativo);
        }
    }
}
