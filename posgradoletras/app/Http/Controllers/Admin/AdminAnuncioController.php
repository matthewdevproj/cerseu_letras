<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anuncio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Anuncios del popup de la portada.
 */
class AdminAnuncioController extends Controller
{
    public function index()
    {
        return view('admin.anuncios.index', [
            'anuncios' => Anuncio::orderBy('orden')->orderBy('id')->paginate(20),
            'enPapelera' => Anuncio::onlyTrashed()->count(),
            'ajustes' => \App\Models\SiteSetting::first() ?? new \App\Models\SiteSetting(),
        ]);
    }

    /**
     * Ajustes de comportamiento del popup.
     *
     * Ruta propia y no `admin.settings.update`: aquel controlador asigna toda
     * la configuración con `?? null`, así que enviar solo estos tres campos
     * habría vaciado correo, teléfono, dirección y redes.
     */
    public function ajustes(Request $request)
    {
        $datos = $request->validate([
            'popup_retardo_ms' => 'nullable|integer|min:0|max:20000',
            'popup_frecuencia' => 'nullable|in:sesion,dia,siempre',
            'popup_auto_avance' => 'nullable',
        ], [
            'popup_frecuencia.in' => 'Elige una de las opciones de la lista.',
            'popup_retardo_ms.max' => 'Más de 20 segundos no tiene sentido: nadie sigue ahí.',
        ]);

        $ajustes = \App\Models\SiteSetting::first() ?: \App\Models\SiteSetting::create(['site_name' => config('app.name')]);

        $ajustes->update([
            'popup_retardo_ms' => $datos['popup_retardo_ms'] ?? null,
            'popup_frecuencia' => $datos['popup_frecuencia'] ?? 'sesion',
            'popup_auto_avance' => $request->boolean('popup_auto_avance'),
        ]);

        return back()->with('success', 'Ajustes del popup guardados.');
    }

    public function create()
    {
        return view('admin.anuncios.form', ['anuncio' => new Anuncio(['is_visible' => true])]);
    }

    public function edit(Anuncio $anuncio)
    {
        return view('admin.anuncios.form', compact('anuncio'));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request, true);

        Anuncio::create($datos);

        return redirect()->route('admin.anuncios.index')
            ->with('success', 'Anuncio creado.');
    }

    public function update(Request $request, Anuncio $anuncio)
    {
        $datos = $this->validar($request, false);

        // Sin imagen nueva se conserva la que había.
        if (! array_key_exists('imagen', $datos)) {
            unset($datos['imagen']);
        }

        $anuncio->update($datos);

        return redirect()->route('admin.anuncios.index')
            ->with('success', 'Anuncio actualizado.');
    }

    public function destroy(Anuncio $anuncio)
    {
        // Borrado suave: queda en la papelera y se puede restaurar.
        $anuncio->delete();

        return redirect()->route('admin.anuncios.index')
            ->with('success', 'Anuncio enviado a la papelera. Puedes restaurarlo desde ahí.');
    }

    public function toggle(Anuncio $anuncio)
    {
        $anuncio->update(['is_visible' => ! $anuncio->is_visible]);

        return back()->with('success', $anuncio->is_visible ? 'Anuncio visible.' : 'Anuncio oculto.');
    }

    public function papelera()
    {
        return view('admin.anuncios.papelera', [
            'anuncios' => Anuncio::onlyTrashed()->orderByDesc('deleted_at')->paginate(20),
        ]);
    }

    public function restaurar(int $id)
    {
        $anuncio = Anuncio::onlyTrashed()->findOrFail($id);
        $anuncio->restore();

        return redirect()->route('admin.anuncios.index')
            ->with('success', 'Anuncio restaurado.');
    }

    private function validar(Request $request, bool $creando): array
    {
        $datos = $request->validate([
            'titulo' => 'required|string|max:120',
            'imagen' => ($creando ? 'required' : 'nullable') . '|image|mimes:jpeg,png,jpg,webp|max:4096',
            'alt' => 'nullable|string|max:255',
            'link' => 'nullable|url|max:500',
            'link_texto' => 'nullable|string|max:60',
            'visible_desde' => 'nullable|date',
            'visible_hasta' => 'nullable|date|after_or_equal:visible_desde',
            'orden' => 'nullable|integer|min:0|max:999',
            'is_visible' => 'nullable',
        ], [
            'titulo.required' => 'Ponle un nombre para reconocerlo en esta lista.',
            'imagen.required' => 'Sube la imagen del anuncio.',
            'imagen.image' => 'El archivo debe ser una imagen (JPG, PNG o WebP).',
            'link.url' => 'El enlace debe empezar por http:// o https://',
            'visible_hasta.after_or_equal' => 'La fecha de fin no puede ser anterior a la de inicio.',
        ]);

        if ($request->hasFile('imagen')) {
            // Se redimensiona y convierte a WebP: una imagen recién exportada
            // por diseño puede pasar del megabyte, y esta va en la portada con
            // prioridad alta.
            $datos['imagen'] = \App\Support\OptimizadorImagen::guardar(
                $request->file('imagen'), 'anuncios'
            );

            // Las medidas se leen del archivo ya guardado: con las reales el
            // componente reserva el hueco exacto y la ventana no salta.
            [$ancho, $alto] = \App\Support\OptimizadorImagen::medidas($datos['imagen']);
            $datos['imagen_ancho'] = $ancho;
            $datos['imagen_alto'] = $alto;
        } else {
            unset($datos['imagen']);
        }

        $datos['is_visible'] = (bool) $request->boolean('is_visible');
        $datos['orden'] = $datos['orden'] ?? 0;

        return $datos;
    }
}
