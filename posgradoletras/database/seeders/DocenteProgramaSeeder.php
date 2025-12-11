<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Docente;
use App\Models\Programa;

class DocenteProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $programas = Programa::all();
        $docentes = Docente::all();

        if ($programas->isEmpty() || $docentes->isEmpty()) {
            return;
        }

        foreach ($programas as $programa) {
            // Asignar 3 docentes aleatorios a cada programa
            $docentesAleatorios = $docentes->random(min(3, $docentes->count()));

            foreach ($docentesAleatorios as $index => $docente) {
                $programa->docentes()->attach($docente->id, [
                    'es_coordinador' => $index === 0, // El primero es coordinador
                    'rol' => $index === 0 ? 'Coordinador' : 'Docente',
                    'orden' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
