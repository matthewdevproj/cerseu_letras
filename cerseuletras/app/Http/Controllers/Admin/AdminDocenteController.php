<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDocenteController extends Controller
{
    /**
     * Display a listing of docentes.
     */
    public function index(Request $request)
    {
        $query = Docente::with('programas');

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', '%' . $search . '%')
                    ->orWhere('apellidos', 'like', '%' . $search . '%');
            });
        }

        // Filter by grado
        if ($request->filled('grado')) {
            $query->where('grado', 'like', $request->grado . '%');
        }

        $docentes = $query->ordenados()->paginate(25)->withQueryString();

        return view('admin.docentes.index', compact('docentes'));
    }

    /**
     * Show the form for creating a new docente.
     */
    public function create()
    {
        $programas = Programa::activos()->orderBy('nombre')->get();
        return view('admin.docentes.create', compact('programas'));
    }

    /**
     * Store a newly created docente in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'grado' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'orcid' => 'nullable|string|max:255',
            'cti_vitae' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'biografia' => 'nullable|string',
            'lineas_investigacion' => 'nullable|string',
            'grupo_investigacion' => 'nullable|array',
            'grupo_investigacion.nombre' => 'nullable|string|max:255',
            'grupo_investigacion.link' => 'nullable|url|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'estado' => 'boolean',
            'programas' => 'nullable|array',
            'programas.*' => 'exists:programas,id',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $validated['foto'] = \App\Support\OptimizadorImagen::guardar($request->file('foto'), 'docentes', 'public', 800);
        }

        // Transformar lineas_investigacion de string (textarea) a array
        if (!empty($validated['lineas_investigacion'])) {
            $validated['lineas_investigacion'] = array_values(array_filter(array_map('trim', explode("\n", $validated['lineas_investigacion']))));
        }

        $docente = Docente::create($validated);

        // Attach programs if provided
        if ($request->has('programas')) {
            $docente->programas()->attach($request->programas);
        }

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente creado exitosamente.');
    }

    /**
     * Show the form for editing the specified docente.
     */
    public function edit(Docente $docente)
    {
        $programas = Programa::activos()->orderBy('nombre')->get();
        $docente->load('programas');
        return view('admin.docentes.edit', compact('docente', 'programas'));
    }

    /**
     * Update the specified docente in storage.
     */
    public function update(Request $request, Docente $docente)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'grado' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'orcid' => 'nullable|string|max:255',
            'cti_vitae' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'biografia' => 'nullable|string',
            'lineas_investigacion' => 'nullable|string',
            'grupo_investigacion' => 'nullable|array',
            'grupo_investigacion.nombre' => 'nullable|string|max:255',
            'grupo_investigacion.link' => 'nullable|url|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'estado' => 'boolean',
            // Nota: La asignación de programas se gestiona desde la vista de edición de programas
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($docente->foto) {
                Storage::disk('public')->delete($docente->foto);
            }
            $validated['foto'] = \App\Support\OptimizadorImagen::guardar($request->file('foto'), 'docentes', 'public', 800);
        }

        // Transformar lineas_investigacion de string (textarea) a array
        if (isset($validated['lineas_investigacion'])) {
            $validated['lineas_investigacion'] = array_values(array_filter(array_map('trim', explode("\n", $validated['lineas_investigacion']))));
        }

        $docente->update($validated);

        // Nota: Ya no sincronizamos programas aquí.
        // La asignación de docentes a programas se gestiona desde la vista de edición de cada programa.

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente actualizado exitosamente.');
    }

    /**
     * Remove the specified docente from storage.
     */
    public function destroy(Docente $docente)
    {
        // Delete photo if exists
        if ($docente->foto) {
            Storage::disk('public')->delete($docente->foto);
        }

        // Detach all programs
        $docente->programas()->detach();

        $docente->delete();

        return redirect()->route('admin.docentes.index')
            ->with('success', 'Docente eliminado exitosamente.');
    }

    /**
     * Toggle the active status of a docente.
     */
    public function toggleActive(Docente $docente)
    {
        $docente->update(['estado' => !$docente->estado]);

        $status = $docente->estado ? 'activado' : 'desactivado';
        return redirect()->route('admin.docentes.index')
            ->with('success', "Docente {$status} exitosamente.");
    }
}
