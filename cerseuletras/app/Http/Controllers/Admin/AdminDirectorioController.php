<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectorioCerseu;
use Illuminate\Http\Request;

class AdminDirectorioController extends Controller
{
    /**
     * Muestra la lista de personal del directorio
     */
    public function index(Request $request)
    {
        $query = DirectorioCerseu::query();

        // Búsqueda por nombre o cargo
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_persona', 'like', "%{$search}%")
                    ->orWhere('cargo', 'like', "%{$search}%")
                    ->orWhere('correo_persona', 'like', "%{$search}%");
            });
        }

        // Filtro por unidad
        if ($request->filled('unidad')) {
            $query->where('unidad_nombre', $request->unidad);
        }

        $directorio = $query->orderBy('unidad_nombre')
            ->orderBy('orden')
            ->get();

        $unidades = DirectorioCerseu::distinct()->pluck('unidad_nombre');

        return view('admin.directorio.index', compact('directorio', 'unidades'));
    }

    /**
     * Muestra el formulario para crear nuevo personal
     */
    public function create()
    {
        $unidades = DirectorioCerseu::distinct()->pluck('unidad_nombre')->toArray();
        // Agregar opciones por defecto si no hay registros
        if (empty($unidades)) {
            $unidades = ['AUTORIDADES', 'PERSONAL ADMINISTRATIVO'];
        }

        return view('admin.directorio.create', compact('unidades'));
    }

    /**
     * Almacena un nuevo registro de personal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unidad_nombre' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'nombre_persona' => 'required|string|max:255',
            'anexo' => 'nullable|string|max:50',
            'correo_persona' => 'nullable|email|max:255',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Si es una nueva unidad personalizada
        if ($request->filled('nueva_unidad')) {
            $validated['unidad_nombre'] = $request->nueva_unidad;
        }

        $validated['orden'] = $validated['orden'] ?? 0;
        $validated['activo'] = true;

        DirectorioCerseu::create($validated);

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Personal agregado correctamente.');
    }

    /**
     * Muestra el formulario para editar
     */
    public function edit(DirectorioCerseu $directorio)
    {
        $unidades = DirectorioCerseu::distinct()->pluck('unidad_nombre')->toArray();
        if (empty($unidades)) {
            $unidades = ['AUTORIDADES', 'PERSONAL ADMINISTRATIVO'];
        }

        return view('admin.directorio.edit', compact('directorio', 'unidades'));
    }

    /**
     * Actualiza el registro
     */
    public function update(Request $request, DirectorioCerseu $directorio)
    {
        $validated = $request->validate([
            'unidad_nombre' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'nombre_persona' => 'required|string|max:255',
            'anexo' => 'nullable|string|max:50',
            'correo_persona' => 'nullable|email|max:255',
            'orden' => 'nullable|integer|min:0',
        ]);

        // Si es una nueva unidad personalizada
        if ($request->filled('nueva_unidad')) {
            $validated['unidad_nombre'] = $request->nueva_unidad;
        }

        $validated['orden'] = $validated['orden'] ?? 0;

        $directorio->update($validated);

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Personal actualizado correctamente.');
    }

    /**
     * Elimina el registro
     */
    public function destroy(DirectorioCerseu $directorio)
    {
        $directorio->delete();

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Personal eliminado correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo
     */
    public function toggleActive(DirectorioCerseu $directorio)
    {
        $directorio->activo = !$directorio->activo;
        $directorio->save();

        $status = $directorio->activo ? 'activado' : 'desactivado';

        return redirect()->route('admin.directorio.index')
            ->with('success', "Personal {$status} correctamente.");
    }

    /**
     * Mover hacia arriba (menor orden)
     */
    public function moveUp(DirectorioCerseu $directorio)
    {
        // Buscar el elemento anterior en la misma unidad
        $anterior = DirectorioCerseu::where('unidad_nombre', $directorio->unidad_nombre)
            ->where('orden', '<', $directorio->orden)
            ->orderBy('orden', 'desc')
            ->first();

        if ($anterior) {
            // Intercambiar ordenes
            $ordenActual = $directorio->orden;
            $directorio->orden = $anterior->orden;
            $anterior->orden = $ordenActual;
            $directorio->save();
            $anterior->save();
        }

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Orden actualizado correctamente.');
    }

    /**
     * Mover hacia abajo (mayor orden)
     */
    public function moveDown(DirectorioCerseu $directorio)
    {
        // Buscar el elemento siguiente en la misma unidad
        $siguiente = DirectorioCerseu::where('unidad_nombre', $directorio->unidad_nombre)
            ->where('orden', '>', $directorio->orden)
            ->orderBy('orden', 'asc')
            ->first();

        if ($siguiente) {
            // Intercambiar ordenes
            $ordenActual = $directorio->orden;
            $directorio->orden = $siguiente->orden;
            $siguiente->orden = $ordenActual;
            $directorio->save();
            $siguiente->save();
        }

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Orden actualizado correctamente.');
    }
}
