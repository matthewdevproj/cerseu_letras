<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CronogramaAdmision;
use App\Models\CronogramaAdmisionPaso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Edición de la sección "Cronograma de Admisión" de la portada (Obs. N.º 2).
 */
class AdminCronogramaAdmisionController extends Controller
{
    public function index()
    {
        $cronograma = CronogramaAdmision::with('pasos')->first();

        if (!$cronograma) {
            // Titulos neutros. Antes decian «Proceso de Admision 2026-I» y
            // «Cronograma de Admision», heredados de Posgrado: el CERSEU no
            // toma examen de admision, solo inscribe.
            $cronograma = CronogramaAdmision::create([
                'titulo' => 'Cómo inscribirte',
                'boton_texto' => 'Ver la oferta',
                'boton_url' => '/cursos',
                'is_visible' => true,
            ]);
            $cronograma->load('pasos');
        }

        return view('admin.cronograma-admision.index', [
            'cronograma' => $cronograma,
            'iconos' => CronogramaAdmision::ICONOS,
        ]);
    }

    public function update(Request $request)
    {
        // firstOrCreate y no firstOrFail: la fila ya no la siembra la
        // migracion, asi que guardar desde el panel debe poder crearla igual
        // que hace index().
        $cronograma = CronogramaAdmision::firstOrCreate([], ['is_visible' => true]);

        $validated = $request->validate([
            'eyebrow' => 'nullable|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'boton_texto' => 'nullable|string|max:100',
            'boton_url' => 'nullable|string|max:500',
            'pasos_payload' => 'nullable|string',
        ], [], [
            'eyebrow' => 'título superior',
            'titulo' => 'título de la sección',
            'boton_texto' => 'texto del botón',
            'boton_url' => 'enlace del botón',
        ]);

        $pasos = json_decode($request->input('pasos_payload', '[]'), true);
        if (!is_array($pasos)) {
            $pasos = [];
        }

        // Se valida aquí y no en el `validate()` porque las etapas llegan como
        // un único JSON desde el repetidor del formulario.
        $iconosValidos = array_keys(CronogramaAdmision::ICONOS);
        foreach ($pasos as $paso) {
            if (trim((string) ($paso['titulo'] ?? '')) === '') {
                return back()
                    ->withInput()
                    ->with('error', 'Cada etapa necesita un nombre. Revisa las etapas del cronograma.');
            }
        }

        try {
            DB::transaction(function () use ($request, $validated, $cronograma, $pasos, $iconosValidos) {
                $cronograma->update([
                    'eyebrow' => $validated['eyebrow'] ?? null,
                    'titulo' => $validated['titulo'] ?? null,
                    'boton_texto' => $validated['boton_texto'] ?? null,
                    'boton_url' => $validated['boton_url'] ?? null,
                    'is_visible' => $request->boolean('is_visible'),
                ]);

                // El repetidor envía siempre el conjunto completo de etapas: las
                // que ya no vienen en el payload se eliminan.
                $idsRecibidos = collect($pasos)->pluck('id')->filter()->map('intval')->all();
                $cronograma->pasos()->whereNotIn('id', $idsRecibidos ?: [0])->delete();

                foreach (array_values($pasos) as $orden => $paso) {
                    $data = [
                        'titulo' => trim((string) $paso['titulo']),
                        'fecha_inicio' => $this->limpiar($paso['fecha_inicio'] ?? null),
                        'fecha_fin' => $this->limpiar($paso['fecha_fin'] ?? null),
                        'detalle' => $this->limpiar($paso['detalle'] ?? null),
                        'publico' => $this->limpiar($paso['publico'] ?? null),
                        'icono' => in_array($paso['icono'] ?? null, $iconosValidos, true)
                            ? $paso['icono']
                            : 'documento',
                        'orden' => $orden,
                        'destacado' => !empty($paso['destacado']),
                        'is_visible' => !empty($paso['is_visible']),
                    ];

                    $existente = !empty($paso['id'])
                        ? $cronograma->pasos()->find($paso['id'])
                        : null;

                    if ($existente) {
                        $existente->update($data);
                    } else {
                        $cronograma->pasos()->create($data);
                    }
                }
            });

            CronogramaAdmision::clearCache();

            return redirect()->route('admin.cronograma-admision.index')
                ->with('success', 'Cronograma de admisión actualizado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    /**
     * Normaliza un campo de texto opcional: cadena vacía → null.
     */
    private function limpiar($valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor === '' ? null : $valor;
    }
}
