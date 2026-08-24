<?php

namespace Database\Seeders;

use App\Models\AdmisionCronogramaItem;
use App\Models\AdmisionSetting;
use App\Models\Docente;
use App\Models\Programa;
use App\Models\TipoOferta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Oferta real del CERSEU: la programación 2026 que envió la unidad.
 *
 * El cuadro de origen tiene 47 dictados de 39 cursos distintos —«Redacción de
 * Tesis I» se repite cuatro veces a lo largo del año, entre otros—, así que se
 * separa en dos cosas: la ficha del curso, que es una por nombre, y sus
 * convocatorias, que van al cronograma de admisión del módulo de Cursos.
 *
 * Los datos viven en data/oferta-cerseu-2026.json para poder regenerarlos
 * desde la hoja de cálculo sin tocar este archivo.
 */
class OfertaCerseuSeeder extends Seeder
{
    public function run(): void
    {
        $datos = json_decode(
            file_get_contents(database_path('seeders/data/oferta-cerseu-2026.json')),
            true
        );

        if (Programa::deTipo(TipoOferta::Curso)->exists()) {
            $this->command?->warn('Ya hay cursos cargados; no se toca la oferta.');

            return;
        }

        $docentes = $this->docentes($datos['docentes']);
        $cursos = $this->cursos($datos['cursos'], $docentes);
        $this->cronograma($datos['cronograma'], $cursos);

        $this->command?->info(sprintf(
            'CERSEU 2026: %d cursos, %d docentes, %d convocatorias.',
            count($cursos),
            count($docentes),
            count($datos['cronograma'])
        ));
    }

    /** @return array<string, Docente> indexados por «Nombres Apellidos» */
    private function docentes(array $filas): array
    {
        $docentes = [];

        foreach ($filas as $fila) {
            $completo = trim($fila['nombres'] . ' ' . $fila['apellidos']);

            $docentes[$completo] = Docente::firstOrCreate(
                ['slug' => Str::slug($completo)],
                [
                    'nombres' => $fila['nombres'],
                    'apellidos' => $fila['apellidos'],
                    'estado' => 1,
                ]
            );
        }

        return $docentes;
    }

    /** @return array<string, Programa> indexados por nombre del curso */
    private function cursos(array $filas, array $docentes): array
    {
        $cursos = [];

        foreach ($filas as $fila) {
            $curso = Programa::create([
                'grado' => TipoOferta::Curso->grado(),
                'nombre' => $fila['nombre'],
                'slug' => Str::slug($fila['nombre']),
                'modalidad' => $fila['modalidad'],
                'horas_academicas' => $fila['horas_academicas'],
                'sumilla' => $fila['sumilla'],
                'estado' => Programa::ESTADO_PUBLICADO,
                // `grado_otorga` es un par «Rótulo: contenido» de texto libre, no
                // un grado académico (ver Programa::denominacionGradoOtorga). Se
                // aprovecha para la escuela, que no tiene columna propia.
                'grado_otorga_label' => 'Escuela',
                'grado_otorga' => $fila['escuela'],
            ]);

            if ($docente = $docentes[$fila['docente']] ?? null) {
                $curso->docentes()->attach($docente->id, [
                    'es_coordinador' => true,
                    'rol' => 'Responsable',
                    'orden' => 1,
                ]);
            }

            $cursos[$fila['nombre']] = $curso;
        }

        return $cursos;
    }

    private function cronograma(array $filas, array $cursos): void
    {
        $settings = AdmisionSetting::firstOrCreate(
            ['tipo' => TipoOferta::Curso->value],
            [
                'hero_titulo' => 'Programación 2026',
                'hero_subtitulo' => 'Sección Cursos · CERSEU',
            ]
        );

        foreach ($filas as $fila) {
            AdmisionCronogramaItem::create([
                'admision_setting_id' => $settings->id,
                'programa' => $fila['programa'],
                'convocatoria' => $fila['convocatoria'],
                'fecha_inscripcion' => $fila['fecha_inscripcion'],
                'fecha_limite' => $fila['fecha_limite'],
                'estado' => $fila['estado'],
                'orden' => $fila['orden'],
            ]);
        }

        AdmisionSetting::clearCache(TipoOferta::Curso);
    }
}
