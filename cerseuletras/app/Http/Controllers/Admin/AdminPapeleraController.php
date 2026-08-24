<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Papelera única del panel.
 *
 * Antes un borrado era definitivo en todos los modelos. Ahora salen de la vista
 * pública pero quedan aquí, en un solo sitio: buscar en siete papeleras
 * distintas —una por sección— sería peor que no tener ninguna.
 */
class AdminPapeleraController extends Controller
{
    /**
     * Qué se puede recuperar, y cómo se llama cada cosa en la lista.
     *
     * `titulo` es el campo (o la unión de campos) que identifica el registro
     * para una persona.
     */
    private const RECUPERABLES = [
        'programas' => ['modelo' => \App\Models\Programa::class, 'etiqueta' => 'Programa', 'titulo' => 'nombre', 'icono' => 'fas-graduation-cap'],
        'docentes' => ['modelo' => \App\Models\Docente::class, 'etiqueta' => 'Docente', 'titulo' => ['nombres', 'apellidos'], 'icono' => 'fas-chalkboard-user'],
        'eventos' => ['modelo' => \App\Models\Evento::class, 'etiqueta' => 'Evento', 'titulo' => 'titulo', 'icono' => 'fas-calendar-day'],
        'informativos' => ['modelo' => \App\Models\Informativo::class, 'etiqueta' => 'Informativo', 'titulo' => 'titulo', 'icono' => 'fas-file-lines'],
        'documentos' => ['modelo' => \App\Models\Document::class, 'etiqueta' => 'Documento', 'titulo' => 'original_name', 'icono' => 'fas-file-arrow-down'],
        'testimonios' => ['modelo' => \App\Models\Testimonio::class, 'etiqueta' => 'Testimonio', 'titulo' => 'nombre', 'icono' => 'fas-comment'],
        'directorio' => ['modelo' => \App\Models\DirectorioCerseu::class, 'etiqueta' => 'Directorio', 'titulo' => 'nombre_persona', 'icono' => 'fas-address-book'],
        'anuncios' => ['modelo' => \App\Models\Anuncio::class, 'etiqueta' => 'Anuncio', 'titulo' => 'titulo', 'icono' => 'fas-bullhorn'],
    ];

    public function index(Request $request)
    {
        $tipo = $request->get('tipo');

        $elementos = collect(self::RECUPERABLES)
            ->when($tipo, fn ($c) => $c->only([$tipo]))
            ->flatMap(function (array $config, string $clave) {
                return $config['modelo']::onlyTrashed()
                    ->orderByDesc('deleted_at')
                    ->limit(100)
                    ->get()
                    ->map(fn (Model $registro) => [
                        'tipo' => $clave,
                        'etiqueta' => $config['etiqueta'],
                        'icono' => $config['icono'],
                        'id' => $registro->getKey(),
                        'titulo' => $this->titulo($registro, $config['titulo']),
                        'borrado' => $registro->deleted_at,
                    ]);
            })
            ->sortByDesc('borrado')
            ->values();

        return view('admin.papelera.index', [
            'elementos' => $elementos,
            'tipos' => collect(self::RECUPERABLES)->map(fn ($c) => $c['etiqueta']),
            'tipoActivo' => $tipo,
        ]);
    }

    public function restaurar(string $tipo, int $id)
    {
        $config = self::RECUPERABLES[$tipo] ?? abort(404);

        $registro = $config['modelo']::onlyTrashed()->findOrFail($id);
        $registro->restore();

        return redirect()->route('admin.papelera.index')
            ->with('success', $config['etiqueta'] . ' restaurado. Ya vuelve a estar en el sitio.');
    }

    private function titulo(Model $registro, string|array $campos): string
    {
        $partes = collect((array) $campos)
            ->map(fn ($campo) => trim((string) $registro->{$campo}))
            ->filter();

        return $partes->isEmpty()
            ? '(sin nombre) #' . $registro->getKey()
            : $partes->implode(' ');
    }
}
