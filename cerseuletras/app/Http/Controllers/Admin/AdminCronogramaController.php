<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cronograma;
use App\Models\CronogramaItem;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCronogramaController extends Controller
{
    /**
     * Mostrar el cronograma activo para edición (único cronograma)
     */
    public function index()
    {
        $cronograma = Cronograma::with([
            'items' => function ($q) {
                $q->orderBy('orden')->orderBy('id');
            },
            'documents'
        ])->first();

        // Si no existe, crear uno por defecto
        if (!$cronograma) {
            $cronograma = Cronograma::firstOrCreate(
                ['code' => '2026-I'],
                [
                    'title' => 'Cronograma Académico 2026-I',
                    'description' => 'Cronograma de actividades académicas del semestre 2026-I',
                    'effective_date' => now(),
                    'is_active' => true,
                ]
            );
            // Recargar con relaciones
            $cronograma->load(['items', 'documents']);
        }

        // Documentos disponibles para vincular
        $documents = Document::published()->orderBy('title')->orderBy('original_name')->get();

        return view('admin.cronograma.index', compact('cronograma', 'documents'));
    }

    /**
     * Actualizar el cronograma y sus ítems
     */
    public function update(Request $request)
    {
        $cronograma = Cronograma::firstOrFail();

        $request->validate([
            'code' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $cronograma) {
                // Actualizar cronograma
                $cronograma->update([
                    'code' => strtoupper($request->code),
                    'title' => $request->title,
                    'description' => $request->description,
                ]);

                // Eliminar ítems marcados para borrar
                if ($request->has('deleted_items')) {
                    $deletedIds = json_decode($request->input('deleted_items'), true);
                    if (is_array($deletedIds) && !empty($deletedIds)) {
                        CronogramaItem::whereIn('id', $deletedIds)
                            ->where('cronograma_id', $cronograma->id)
                            ->delete();
                    }
                }

                // Procesar Items (Create / Update)
                $itemsPayload = json_decode($request->input('items_payload'), true);
                if (is_array($itemsPayload)) {
                    foreach ($itemsPayload as $itemData) {
                        // Si fecha_text es vacía, asignamos guion
                        if (isset($itemData['fecha_text']) && $itemData['fecha_text'] === '') {
                            $itemData['fecha_text'] = '—';
                        }

                        if (!empty($itemData['is_new'])) {
                            $cronograma->items()->create([
                                'section' => $itemData['section'] ?? null,
                                'is_section_heading' => !empty($itemData['is_section_heading']),
                                'actividad' => $itemData['actividad'] ?? '',
                                'fecha_text' => $itemData['fecha_text'] ?? '',
                                'orden' => $itemData['orden'] ?? 0,
                            ]);
                        } else {
                            $item = CronogramaItem::find($itemData['id']);
                            if ($item && $item->cronograma_id == $cronograma->id) {
                                $item->update([
                                    'section' => $itemData['section'] ?? null,
                                    'is_section_heading' => !empty($itemData['is_section_heading']),
                                    'actividad' => $itemData['actividad'] ?? '',
                                    'fecha_text' => $itemData['fecha_text'] ?? '',
                                    'orden' => $itemData['orden'] ?? 0,
                                ]);
                            }
                        }
                    }
                }

                // Sincronizar documentos - primero los existentes
                $docSyncData = [];
                if ($request->has('document_ids')) {
                    $docIds = $request->input('document_ids', []);
                    foreach ($docIds as $index => $docId) {
                        $docSyncData[$docId] = ['position' => $index];
                    }
                }

                // Procesar nuevos archivos PDF subidos
                if ($request->hasFile('new_document_files')) {
                    $files = $request->file('new_document_files');
                    $titles = $request->input('new_document_titles', []);

                    foreach ($files as $index => $file) {
                        $title = $titles[$index] ?? $file->getClientOriginalName();
                        $url = asset('storage/' . $file->store('documents', 'public'));

                        $newDoc = Document::create([
                            'type' => 'general',
                            'title' => $title,
                            'original_name' => $file->getClientOriginalName(),
                            'url' => $url,
                            'published' => true,
                        ]);

                        // Agregar al array de sincronización
                        $docSyncData[$newDoc->id] = ['position' => count($docSyncData)];
                    }
                }

                $cronograma->documents()->sync($docSyncData);
            });

            // Limpiar caché
            Cronograma::clearCache();

            return redirect()->route('admin.cronograma.index')
                ->with('success', 'Cronograma actualizado correctamente.');

        } catch (\Exception $e) {
            return redirect()->route('admin.cronograma.index')
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
