<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonio;
use App\Models\Programa;

class TestimonioSeeder extends Seeder
{
    public function run(): void
    {
        $programa = Programa::first();
        $programaId = $programa ? $programa->id : null;

        $testimonios = [
            [
                'nombre' => 'Ana García',
                'photo' => 'testimonios/ana.jpg',
                'programa_id' => $programaId,
                'contenido' => 'La maestría me permitió profundizar mis conocimientos y ampliar mi red profesional. Los docentes son de primer nivel.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Carlos Rodríguez',
                'photo' => 'testimonios/carlos.jpg',
                'programa_id' => $programaId,
                'contenido' => 'Excelente programa académico. La exigencia y la calidad de la enseñanza superaron mis expectativas.',
                'estado' => 1,
            ],
            [
                'nombre' => 'María López',
                'photo' => 'testimonios/maria.jpg',
                'programa_id' => $programaId,
                'contenido' => 'Gracias al doctorado pude desarrollar mi investigación con el apoyo de asesores expertos en el campo.',
                'estado' => 1,
            ],
        ];

        foreach ($testimonios as $testimonio) {
            Testimonio::create($testimonio);
        }
    }
}
