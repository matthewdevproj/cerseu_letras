<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Menú de navegación editable desde el panel.
 *
 * Se envía el árbol completo en cada guardado (igual que el editor de
 * contenido): lo que no llega, se borra. Es más simple de razonar que un
 * diálogo por elemento y encaja con el repetidor de Alpine que ya se usa en el
 * resto del panel.
 */
class AdminMenuController extends Controller
{
    public function index()
    {
        // Sin `visibles()`: en el panel hay que ver también lo caducado,
        // que es justo lo que hay que corregir.
        $menu = MenuItem::whereNull('parent_id')
            ->with(['hijos' => fn ($q) => $q->orderBy('orden')])
            ->orderBy('orden')
            ->get();

        $inicial = $menu->map(fn (MenuItem $m) => [
            'id' => $m->id,
            'etiqueta' => $m->etiqueta,
            'route_name' => $m->route_name,
            'url' => $m->url,
            'icono' => $m->icono,
            'nueva_pestana' => (bool) $m->nueva_pestana,
            'is_visible' => (bool) $m->is_visible,
            'vigente_hasta' => $m->vigente_hasta?->format('Y-m-d'),
            'caducado' => $m->caducado,
            'hijos' => $m->hijos->map(fn (MenuItem $h) => [
                'id' => $h->id,
                'etiqueta' => $h->etiqueta,
                'route_name' => $h->route_name,
                'url' => $h->url,
                'route_params' => $h->route_params,
                'icono' => $h->icono,
                'nueva_pestana' => (bool) $h->nueva_pestana,
                'is_visible' => (bool) $h->is_visible,
                'vigente_hasta' => $h->vigente_hasta?->format('Y-m-d'),
                'caducado' => $h->caducado,
            ])->values(),
        ])->values();

        return view('admin.menu.index', [
            'inicial' => $inicial,
            'rutas' => $this->rutasPublicas(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'items' => 'array',
            'items.*.id' => 'nullable|integer',
            'items.*.etiqueta' => 'required|string|max:60',
            'items.*.route_name' => 'nullable|string|max:120',
            'items.*.url' => 'nullable|url|max:500',
            'items.*.icono' => 'nullable|string|max:60',
            // Las casillas sin marcar no viajan en el POST: hay que declararlas
            // igualmente o `validate()` no devolvería la clave y se perderían.
            'items.*.nueva_pestana' => 'nullable',
            'items.*.is_visible' => 'nullable',
            'items.*.vigente_hasta' => 'nullable|date',
            'items.*.hijos' => 'array',
            'items.*.hijos.*.id' => 'nullable|integer',
            'items.*.hijos.*.etiqueta' => 'required|string|max:60',
            'items.*.hijos.*.route_name' => 'nullable|string|max:120',
            'items.*.hijos.*.url' => 'nullable|url|max:500',
            'items.*.hijos.*.route_params' => 'nullable|string|max:255',
            'items.*.hijos.*.icono' => 'nullable|string|max:60',
            'items.*.hijos.*.nueva_pestana' => 'nullable',
            'items.*.hijos.*.is_visible' => 'nullable',
            'items.*.hijos.*.vigente_hasta' => 'nullable|date',
        ], [
            'items.*.etiqueta.required' => 'Cada entrada del menú necesita un texto.',
            'items.*.hijos.*.etiqueta.required' => 'Cada subentrada necesita un texto.',
            'items.*.url.url' => 'La dirección externa debe empezar por http:// o https://',
            'items.*.hijos.*.url.url' => 'La dirección externa debe empezar por http:// o https://',
        ]);

        $items = $validated['items'] ?? [];

        DB::transaction(function () use ($items) {
            $idsRaiz = collect($items)->pluck('id')->filter()->all();
            // Al borrar un padre se van sus hijos por la clave foránea.
            MenuItem::whereNull('parent_id')->whereNotIn('id', $idsRaiz ?: [0])->delete();

            foreach (array_values($items) as $orden => $item) {
                $padre = $this->guardar($item, null, $orden);

                $idsHijos = collect($item['hijos'] ?? [])->pluck('id')->filter()->all();
                MenuItem::where('parent_id', $padre->id)
                    ->whereNotIn('id', $idsHijos ?: [0])->delete();

                foreach (array_values($item['hijos'] ?? []) as $ordenHijo => $hijo) {
                    $this->guardar($hijo, $padre->id, $ordenHijo);
                }
            }
        });

        MenuItem::clearCache();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menú de navegación actualizado.');
    }

    private function guardar(array $datos, ?int $parentId, int $orden): MenuItem
    {
        $campos = [
            'parent_id' => $parentId,
            'etiqueta' => $datos['etiqueta'],
            // Una ruta interna y una URL externa se excluyen: si vienen las
            // dos, manda la ruta (sobrevive a un cambio de dirección).
            'route_name' => $datos['route_name'] ?: null,
            'url' => ($datos['route_name'] ?? null) ? null : ($datos['url'] ?: null),
            'route_params' => $datos['route_params'] ?? null,
            'icono' => $datos['icono'] ?: null,
            'nueva_pestana' => (bool) ($datos['nueva_pestana'] ?? false),
            'is_visible' => (bool) ($datos['is_visible'] ?? false),
            'vigente_hasta' => $datos['vigente_hasta'] ?? null,
            'orden' => $orden,
        ];

        $existente = ! empty($datos['id']) ? MenuItem::find($datos['id']) : null;

        if ($existente) {
            $existente->update($campos);

            return $existente;
        }

        return MenuItem::create($campos);
    }

    /** Rutas públicas con nombre, para ofrecerlas en un desplegable. */
    private function rutasPublicas(): array
    {
        return collect(Route::getRoutes())
            ->filter(fn ($r) => $r->getName()
                && in_array('GET', $r->methods(), true)
                && ! str_starts_with($r->getName(), 'admin.')
                && ! str_contains($r->uri(), '{')
                && ! in_array($r->getName(), ['login', 'logout', 'register', 'password.request']))
            ->map(fn ($r) => $r->getName())
            ->unique()->sort()->values()->all();
    }
}
