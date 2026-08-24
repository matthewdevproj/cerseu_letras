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
            // Excluir docentes ya asignados (p. ej. coordinadores de Diplomado ya vinculados por DiplomadoSeeder)
            $yaAsignados = $programa->docentes()->pluck('docentes.id');
            $disponibles = $docentes->whereNotIn('id', $yaAsignados);

            if ($disponibles->isEmpty()) {
                continue;
            }

            // Asignar hasta 3 docentes aleatorios adicionales a cada programa
            $docentesAleatorios = $disponibles->random(min(3, $disponibles->count()));
            $yaTieneCoordinador = $programa->docentes()->wherePivot('es_coordinador', true)->exists();

            foreach ($docentesAleatorios as $index => $docente) {
                $esCoordinador = $index === 0 && ! $yaTieneCoordinador;
                $programa->docentes()->attach($docente->id, [
                    'es_coordinador' => $esCoordinador,
                    'rol' => $esCoordinador ? 'Coordinador' : 'Docente',
                    'orden' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
