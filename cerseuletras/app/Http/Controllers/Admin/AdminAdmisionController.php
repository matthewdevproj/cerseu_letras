<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmisionCronogramaItem;
use App\Models\AdmisionSetting;
use App\Models\TipoOferta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminAdmisionController extends Controller
{
    /**
     * Configuración de la página de admisión de un módulo.
     *
     * Hay un registro por tipo de oferta. La vista es la misma para talleres y
     * cursos; arriba lleva un selector para pasar de uno a otro.
     */
    public function index(TipoOferta $tipoOferta)
    {
        $tipo = $tipoOferta;

        $settings = AdmisionSetting::with('cronogramaItems')->deTipo($tipo)->first();

        if (!$settings) {
            $settings = AdmisionSetting::create([
                'tipo' => $tipo->value,
                'hero_titulo' => 'Convocatoria 2026-I',
                'hero_subtitulo' => 'Sección ' . $tipo->plural() . ' · CERSEU',
            ]);
            $settings->load('cronogramaItems');
        }

        return view('admin.admision.index', compact('settings', 'tipo'));
    }

    /**
     * Actualizar la configuración y sus filas de cronograma.
     */
    public function update(Request $request, TipoOferta $tipoOferta)
    {
        $tipo = $tipoOferta;

        // firstOrCreate y no firstOrFail: la fila de un módulo puede no existir
        // todavía (el tipo se añadió después, o nadie abrió su pantalla), y en ese
        // caso guardar debía funcionar igual en vez de responder 404.
        $settings = AdmisionSetting::firstOrCreate(['tipo' => $tipo->value]);

        $validated = $request->validate([
            'hero_titulo' => 'nullable|string|max:255',
            'hero_subtitulo' => 'nullable|string|max:255',
            'requisitos_email' => 'nullable|email|max:255',
            'requisitos_observaciones' => 'nullable|string',
            'requisitos_notas' => 'nullable|string',
            'pago_costo' => 'nullable|string|max:255',
            'pago_descripcion' => 'nullable|string',
            'pago_link_sanmarket' => 'nullable|url|max:255',
            'pago_observaciones' => 'nullable|string',
            'resultados_texto' => 'nullable|string',
            'resultados_enlace' => 'nullable|url|max:255',
            'resultados_pdf_url' => 'nullable|max:255',
            'contacto_telefono' => 'nullable|string|max:50',
            'contacto_correo' => 'nullable|email|max:255',
            'contacto_direccion' => 'nullable|string',
            'contacto_sitio_web' => 'nullable|url|max:255',
            'contacto_whatsapp' => 'nullable|url|max:255',
            'pasos' => 'nullable',
            'requisitos_lista' => 'nullable',
            'pago_instrucciones' => 'nullable',
            'qr' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'hero_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $settings) {
                foreach (['pasos', 'requisitos_lista', 'pago_instrucciones'] as $jsonField) {
                    if (isset($validated[$jsonField]) && is_string($validated[$jsonField])) {
                        $validated[$jsonField] = json_decode($validated[$jsonField], true) ?: [];
                    }
                }

                $settings->fill($validated);

                if ($request->hasFile('hero_imagen')) {
                    if ($settings->hero_imagen && Storage::disk('public')->exists($settings->hero_imagen)) {
                        Storage::disk('public')->delete($settings->hero_imagen);
                    }
                    $settings->hero_imagen = \App\Support\OptimizadorImagen::guardar($request->file('hero_imagen'), 'admision-' . $tipo->slug());
                } elseif ($request->boolean('remove_hero_imagen')) {
                    if ($settings->hero_imagen && Storage::disk('public')->exists($settings->hero_imagen)) {
                        Storage::disk('public')->delete($settings->hero_imagen);
                    }
                    $settings->hero_imagen = null;
                }

                if ($request->hasFile('qr')) {
                    if ($settings->contacto_qr_path && Storage::disk('public')->exists($settings->contacto_qr_path)) {
                        Storage::disk('public')->delete($settings->contacto_qr_path);
                    }
                    $settings->contacto_qr_path = $request->file('qr')->store('admision-' . $tipo->slug(), 'public');
                } elseif ($request->boolean('remove_qr')) {
                    if ($settings->contacto_qr_path && Storage::disk('public')->exists($settings->contacto_qr_path)) {
                        Storage::disk('public')->delete($settings->contacto_qr_path);
                    }
                    $settings->contacto_qr_path = null;
                }

                $settings->save();

                // Eliminar filas de cronograma marcadas para borrar
                if ($request->has('deleted_cronograma_items')) {
                    $deletedIds = json_decode($request->input('deleted_cronograma_items'), true);
                    if (is_array($deletedIds) && !empty($deletedIds)) {
                        AdmisionCronogramaItem::whereIn('id', $deletedIds)
                            ->where('admision_setting_id', $settings->id)
                            ->delete();
                    }
                }

                // Crear / actualizar filas de cronograma
                $itemsPayload = json_decode($request->input('cronograma_items_payload', '[]'), true);
                if (is_array($itemsPayload)) {
                    foreach ($itemsPayload as $itemData) {
                        $data = [
                            'programa' => $itemData['programa'] ?? '',
                            'convocatoria' => $itemData['convocatoria'] ?? null,
                            'fecha_inscripcion' => $itemData['fecha_inscripcion'] ?? null,
                            'fecha_limite' => $itemData['fecha_limite'] ?? null,
                            'estado' => $itemData['estado'] ?? 'Activo',
                            'orden' => $itemData['orden'] ?? 0,
                        ];

                        if (!empty($itemData['is_new'])) {
                            $settings->cronogramaItems()->create($data);
                        } else {
                            $item = AdmisionCronogramaItem::find($itemData['id']);
                            if ($item && $item->admision_setting_id == $settings->id) {
                                $item->update($data);
                            }
                        }
                    }
                }
            });

            AdmisionSetting::clearCache($tipo);

            return redirect()->route('admin.admision.index', $tipo->slug())
                ->with('success', 'Página de Admisión de ' . $tipo->plural() . ' actualizada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.admision.index', $tipo->slug())
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
