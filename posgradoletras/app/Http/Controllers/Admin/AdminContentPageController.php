<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use App\Models\ContentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Edición del contenido de /tramites y /admision.
 */
class AdminContentPageController extends Controller
{
    public function index()
    {
        return view('admin.contenido.index', [
            'paginas' => ContentPage::withCount('secciones')->get()->keyBy('slug'),
        ]);
    }

    public function edit(string $slug)
    {
        abort_unless(array_key_exists($slug, ContentPage::PAGINAS), 404);

        $pagina = ContentPage::with('secciones')->firstOrCreate(
            ['slug' => $slug],
            ['titulo' => ContentPage::PAGINAS[$slug]]
        );

        return view('admin.contenido.edit', [
            'pagina' => $pagina,
            'grupos' => ContentPage::GRUPOS[$slug],
            'tokens' => ContentSection::TOKENS,
        ]);
    }

    public function update(Request $request, string $slug)
    {
        abort_unless(array_key_exists($slug, ContentPage::PAGINAS), 404);

        $pagina = ContentPage::where('slug', $slug)->firstOrFail();
        $gruposValidos = array_keys(ContentPage::GRUPOS[$slug]);

        $validated = $request->validate([
            'titulo' => 'nullable|string|max:255',
            'subtitulo' => 'nullable|string|max:500',
            'secciones' => 'array',
            'secciones.*.id' => 'nullable|integer',
            'secciones.*.titulo' => 'required|string|max:255',
            'secciones.*.numeral' => 'nullable|string|max:10',
            'secciones.*.cuerpo' => 'nullable|string',
            // Debe declararse aunque no tenga restricciones: `validate()` solo
            // devuelve las claves declaradas, y sin esto la visibilidad se
            // perdía y todas las secciones se guardaban ocultas.
            'secciones.*.is_visible' => 'nullable',
            'secciones.*.grupo' => $gruposValidos ? ['nullable', Rule::in($gruposValidos)] : 'nullable',
        ], [
            'secciones.*.titulo.required' => 'Cada sección necesita un título.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $pagina) {
                $pagina->update([
                    'titulo' => $validated['titulo'] ?? null,
                    'subtitulo' => $validated['subtitulo'] ?? null,
                ]);

                $secciones = $validated['secciones'] ?? [];

                // Se envía siempre el conjunto completo: lo que no llega, se borra.
                $idsRecibidos = collect($secciones)->pluck('id')->filter()->all();
                $pagina->secciones()->whereNotIn('id', $idsRecibidos ?: [0])->delete();

                foreach (array_values($secciones) as $orden => $s) {
                    $datos = [
                        'grupo' => $s['grupo'] ?? null,
                        'numeral' => $s['numeral'] ?? null,
                        'titulo' => $s['titulo'],
                        'cuerpo' => $s['cuerpo'] ?? null,
                        'orden' => $orden,
                        // Las casillas sin marcar no viajan en el POST.
                        'is_visible' => (bool) ($s['is_visible'] ?? false),
                    ];

                    $existente = !empty($s['id']) ? $pagina->secciones()->find($s['id']) : null;

                    $existente ? $existente->update($datos) : $pagina->secciones()->create($datos);
                }
            });

            ContentPage::clearCache($slug);

            return redirect()->route('admin.contenido.edit', $slug)
                ->with('success', 'Contenido actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }
}
