<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProgramaController extends Controller
{
    /**
     * Display a listing of programas.
     */
    public function index(Request $request)
    {
        $query = Programa::query();

        // Search by name
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filter by type (grado)
        if ($request->filled('tipo')) {
            $tipoMap = [
                'maestria' => 'Maestría',
                'doctorado' => 'Doctorado',
            ];
            if (isset($tipoMap[$request->tipo])) {
                $query->where('grado', $tipoMap[$request->tipo]);
            }
        }

        $programas = $query->orderBy('grado')->orderBy('nombre')->paginate(15)->withQueryString();

        return view('admin.programas.index', compact('programas'));
    }

    /**
     * Show the form for creating a new programa.
     */
    public function create()
    {
        return view('admin.programas.create');
    }

    /**
     * Store a newly created programa in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:programas',
            'grado' => 'required|in:Maestría,Doctorado',
            'nombre' => 'required|string|max:255',
            'mencion' => 'nullable|string|max:255',
            'modalidad' => 'nullable|string|max:100',
            'vacantes' => 'nullable|integer|min:0',
            'duracion' => 'nullable|integer|min:1',
            'creditos' => 'nullable|integer|min:0',
            'grado_otorga' => 'nullable|string|max:255',
            'plan_url' => 'nullable|url',
            'por_que_text' => 'nullable|string',
            'presentacion' => 'nullable|string',
            'perfil_egresado' => 'nullable|string',
            'plan_estudios' => 'nullable|json',
            'sumilla' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('programas', 'public');
        }

        // Generate slug from codigo
        $validated['slug'] = Str::slug($validated['codigo']);

        Programa::create($validated);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa creado exitosamente.');
    }

    /**
     * Show the form for editing the specified programa.
     */
    public function edit(Programa $programa)
    {
        return view('admin.programas.edit', compact('programa'));
    }

    /**
     * Update the specified programa in storage.
     */
    public function update(Request $request, Programa $programa)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:programas,codigo,' . $programa->id,
            'grado' => 'required|in:Maestría,Doctorado',
            'nombre' => 'required|string|max:255',
            'mencion' => 'nullable|string|max:255',
            'modalidad' => 'nullable|string|max:100',
            'vacantes' => 'nullable|integer|min:0',
            'duracion' => 'nullable|integer|min:1',
            'creditos' => 'nullable|integer|min:0',
            'grado_otorga' => 'nullable|string|max:255',
            'plan_url' => 'nullable|url',
            'por_que_text' => 'nullable|string',
            'presentacion' => 'nullable|string',
            'perfil_egresado' => 'nullable|string',
            'plan_estudios' => 'nullable|json',
            'sumilla' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('imagen')) {
            // Delete old image
            if ($programa->imagen) {
                Storage::disk('public')->delete($programa->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('programas', 'public');
        }

        // Update slug if codigo changed
        if ($validated['codigo'] !== $programa->codigo) {
            $validated['slug'] = Str::slug($validated['codigo']);
        }

        $programa->update($validated);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa actualizado exitosamente.');
    }

    /**
     * Remove the specified programa from storage.
     */
    public function destroy(Programa $programa)
    {
        // Delete image if exists
        if ($programa->imagen) {
            Storage::disk('public')->delete($programa->imagen);
        }

        $programa->delete();

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa eliminado exitosamente.');
    }

    /**
     * Toggle the active status of a programa.
     */
    public function toggleActive(Programa $programa)
    {
        $programa->update(['is_active' => !$programa->is_active]);

        $status = $programa->is_active ? 'activado' : 'desactivado';
        return redirect()->route('admin.programas.index')
            ->with('success', "Programa {$status} exitosamente.");
    }
}
