<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminEventoController extends Controller
{
    public function index()
    {
        $eventos = Evento::orderBy('orden')->orderBy('fecha_inicio', 'desc')->paginate(15);
        return view('admin.eventos.index', compact('eventos'));
    }

    public function create()
    {
        return view('admin.eventos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'url' => 'nullable|string|max:500',
            'tipo_url' => 'nullable|in:externo,pdf',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');
        $validated['orden'] = $validated['orden'] ?? 0;

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = \App\Support\OptimizadorImagen::guardar($request->file('imagen'), 'eventos');
        }

        Evento::create($validated);

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento creado correctamente.');
    }

    public function edit(Evento $evento)
    {
        return view('admin.eventos.edit', compact('evento'));
    }

    public function update(Request $request, Evento $evento)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'url' => 'nullable|string|max:500',
            'tipo_url' => 'nullable|in:externo,pdf',
            'orden' => 'nullable|integer',
            'activo' => 'boolean',
        ]);

        $validated['activo'] = $request->has('activo');
        $validated['orden'] = $validated['orden'] ?? 0;

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($evento->imagen) {
                Storage::disk('public')->delete($evento->imagen);
            }
            $validated['imagen'] = \App\Support\OptimizadorImagen::guardar($request->file('imagen'), 'eventos');
        }

        // Limpiar URL si se quita el tipo
        if (empty($validated['tipo_url'])) {
            $validated['url'] = null;
        }

        $evento->update($validated);

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Evento $evento)
    {
        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }

        $evento->delete();

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
