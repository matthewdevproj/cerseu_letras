<?php

namespace Database\Seeders;

use App\Models\Testimonio;
use App\Models\Programa;
use Illuminate\Database\Seeder;

class TestimoniosSeeder extends Seeder
{
    public function run(): void
    {
        $programas = Programa::all();
        
        if ($programas->isEmpty()) {
            $this->command->warn('No hay programas en la base de datos. Ejecuta ProgramasSeeder primero.');
            return;
        }

        $testimonios = [
            [
                'nombre' => 'Pedro Ramírez',
                'programa_id' => $programas->where('nombre', 'Literatura Peruana y Latinoamericana')->where('grado', 'Maestría')->first()?->id,
                'contenido' => 'La maestría me permitió desarrollar mi capacidad crítica y analítica. Los docentes son excelentes y el ambiente académico es estimulante. Recomiendo totalmente este programa.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Carmen Vargas',
                'programa_id' => $programas->where('nombre', 'Lingüística')->first()?->id,
                'contenido' => 'Excelente programa que combina teoría y práctica. Me dio las herramientas necesarias para investigar sobre lenguas andinas y ahora trabajo en un proyecto importante de revitalización lingüística.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Luis Mendoza',
                'programa_id' => $programas->where('nombre', 'Literatura Peruana y Latinoamericana')->where('grado', 'Maestría')->first()?->id,
                'contenido' => 'Estudiar aquí fue una de las mejores decisiones de mi vida académica. El nivel de los seminarios y la calidad de los profesores es incomparable.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Sofía Huamán',
                'programa_id' => $programas->where('nombre', 'Literatura Peruana y Latinoamericana')->where('grado', 'Doctorado')->first()?->id,
                'contenido' => 'El doctorado me brindó un espacio de reflexión profunda y rigor académico. La orientación de los docentes fue fundamental para el desarrollo de mi tesis.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Roberto Castillo',
                'programa_id' => $programas->where('nombre', 'Lingüística')->first()?->id,
                'contenido' => 'Un programa de alta calidad con docentes investigadores reconocidos. Las líneas de investigación son variadas y permiten explorar diferentes áreas de la lingüística.',
                'estado' => 1,
            ],
        ];

        foreach ($testimonios as $testimonio) {
            if ($testimonio['programa_id']) {
                Testimonio::create($testimonio);
            }
        }
    }
}
