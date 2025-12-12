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
        $docentes = \App\Models\Docente::where('estado', 1)->orderBy('apellidos')->get();
        return view('admin.programas.create', compact('docentes'));
    }

    /**
     * Store a newly created programa in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        // Generate unique slug from nombre
        $validated['slug'] = $this->generateUniqueSlug($validated['nombre']);

        Programa::create($validated);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa creado exitosamente.');
    }

    /**
     * Generate a unique slug from the program name.
     */
    private function generateUniqueSlug(string $nombre, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($nombre);
        $slug = $baseSlug;
        $counter = 1;

        $query = Programa::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
            $query = Programa::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Show the form for editing the specified programa.
     */
    public function edit(Programa $programa)
    {
        $docentes = \App\Models\Docente::where('estado', 1)->orderBy('apellidos')->get();
        $programa->load('docentes');
        return view('admin.programas.edit', compact('programa', 'docentes'));
    }

    /**
     * Update the specified programa in storage.
     */
    public function update(Request $request, Programa $programa)
    {
        $validated = $request->validate([
            // codigo is auto-generated and cannot be edited
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
            // Docentes validation
            'docentes_asignados' => 'nullable|array',
            'docentes_asignados.*' => 'nullable|exists:docentes,id',
            'docentes_rol' => 'nullable|array',
            'docentes_orden' => 'nullable|array',
            'docentes_coordinador' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('imagen')) {
            // Delete old image
            if ($programa->imagen) {
                Storage::disk('public')->delete($programa->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('programas', 'public');
        }

        // Remove docentes arrays from validated to prevent mass assignment issues
        unset($validated['docentes_asignados']);
        unset($validated['docentes_rol']);
        unset($validated['docentes_orden']);
        unset($validated['docentes_coordinador']);

        $programa->update($validated);

        // Sync docentes with pivot data
        $docentesAsignados = $request->input('docentes_asignados', []);
        $docentesRol = $request->input('docentes_rol', []);
        $docentesOrden = $request->input('docentes_orden', []);
        $docentesCoordinador = $request->input('docentes_coordinador', []);

        $syncData = [];
        foreach ($docentesAsignados as $index => $docenteId) {
            if (!empty($docenteId)) {
                $syncData[$docenteId] = [
                    'rol' => $docentesRol[$index] ?? null,
                    'orden' => !empty($docentesOrden[$index]) ? (int) $docentesOrden[$index] : null,
                    'es_coordinador' => isset($docentesCoordinador[$index]) && $docentesCoordinador[$index] ? 1 : 0,
                ];
            }
        }

        $programa->docentes()->sync($syncData);

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
