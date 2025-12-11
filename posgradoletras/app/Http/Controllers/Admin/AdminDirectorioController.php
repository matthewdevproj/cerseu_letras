<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectorioPosgrado;
use Illuminate\Http\Request;

class AdminDirectorioController extends Controller
{
    /**
     * Muestra la lista de personal del directorio
     */
    public function index(Request $request)
    {
        $query = DirectorioPosgrado::query();

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
            ->paginate(15);

        $unidades = DirectorioPosgrado::distinct()->pluck('unidad_nombre');

        return view('admin.directorio.index', compact('directorio', 'unidades'));
    }

    /**
     * Muestra el formulario para crear nuevo personal
     */
    public function create()
    {
        $unidades = DirectorioPosgrado::distinct()->pluck('unidad_nombre')->toArray();
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

        DirectorioPosgrado::create($validated);

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Personal agregado correctamente.');
    }

    /**
     * Muestra el formulario para editar
     */
    public function edit(DirectorioPosgrado $directorio)
    {
        $unidades = DirectorioPosgrado::distinct()->pluck('unidad_nombre')->toArray();
        if (empty($unidades)) {
            $unidades = ['AUTORIDADES', 'PERSONAL ADMINISTRATIVO'];
        }

        return view('admin.directorio.edit', compact('directorio', 'unidades'));
    }

    /**
     * Actualiza el registro
     */
    public function update(Request $request, DirectorioPosgrado $directorio)
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
    public function destroy(DirectorioPosgrado $directorio)
    {
        $directorio->delete();

        return redirect()->route('admin.directorio.index')
            ->with('success', 'Personal eliminado correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo
     */
    public function toggleActive(DirectorioPosgrado $directorio)
    {
        $directorio->activo = !$directorio->activo;
        $directorio->save();

        $status = $directorio->activo ? 'activado' : 'desactivado';

        return redirect()->route('admin.directorio.index')
            ->with('success', "Personal {$status} correctamente.");
    }
}
