<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminProgramaController extends Controller
{
    /**
     * Display a listing of programs.
     */
    public function index()
    {
        $programas = Programa::orderBy('nombre')->get();
        return view('admin.programas.index', compact('programas'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        $docentes = Docente::orderBy('apellidos')->get();
        return view('admin.programas.create', compact('docentes'));
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'grado' => 'required|in:Maestría,Doctorado',
            'mencion' => 'nullable|max:255',
            'modalidad' => 'nullable|max:100',
            'duracion' => 'nullable|integer',
            'vacantes' => 'nullable|integer',
            'creditos' => 'nullable|integer',
            'objetivos_academicos' => 'nullable', // JSON array string
            'perfil_ingresante' => 'nullable',    // JSON array string
            'perfil_graduado' => 'nullable',      // JSON array string
            'plan_url' => 'nullable|max:255',
            'horario_url' => 'nullable|max:255',
            'plan_estudios' => 'nullable',         // JSON array string
            'slug' => 'nullable|unique:programas,slug',
            'imagen_url' => 'nullable|max:255',
            'sumilla' => 'nullable|string',
            'por_que_text' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $validated;

        // Decode JSON fields
        if (isset($data['objetivos_academicos']) && is_string($data['objetivos_academicos'])) {
            $data['objetivos_academicos'] = json_decode($data['objetivos_academicos'], true) ?: [];
        }
        if (isset($data['perfil_ingresante']) && is_string($data['perfil_ingresante'])) {
            $data['perfil_ingresante'] = json_decode($data['perfil_ingresante'], true) ?: [];
        }
        if (isset($data['perfil_graduado']) && is_string($data['perfil_graduado'])) {
            $data['perfil_graduado'] = json_decode($data['perfil_graduado'], true) ?: [];
        }
        if (isset($data['plan_estudios']) && is_string($data['plan_estudios'])) {
            $data['plan_estudios'] = json_decode($data['plan_estudios'], true) ?: [];
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nombre']);

            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $count = 1;
            while (Programa::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->has('imagen_url')) {
            $data['imagen'] = $validated['imagen_url'];
            unset($data['imagen_url']);
        }

        $programa = Programa::create($data);

        // Docentes sync (using parallel arrays from form)
        if ($request->has('docentes_asignados')) {
            $docentesData = [];
            foreach ($request->docentes_asignados as $index => $docenteId) {
                if ($docenteId) {
                    $docentesData[$docenteId] = [
                        'es_coordinador' => ($request->docentes_coordinador[$index] ?? '0') == '1',
                        'rol' => $request->docentes_rol[$index] ?? null,
                        'orden' => $request->docentes_orden[$index] ?? 0
                    ];
                }
            }
            $programa->docentes()->sync($docentesData);
        }

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa creado exitosamente.');
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Programa $programa)
    {
        $docentes = Docente::orderBy('apellidos')->get();
        return view('admin.programas.edit', compact('programa', 'docentes'));
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, Programa $programa)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'grado' => 'required|in:Maestría,Doctorado',
            'mencion' => 'nullable|max:255',
            'modalidad' => 'nullable|max:100',
            'duracion' => 'nullable|integer',
            'vacantes' => 'nullable|integer',
            'creditos' => 'nullable|integer',
            'grado_otorga' => 'nullable|max:255',
            'objetivos_academicos' => 'nullable', // JSON array string
            'perfil_ingresante' => 'nullable',    // JSON array string
            'perfil_graduado' => 'nullable',      // JSON array string
            'plan_url' => 'nullable|max:255',
            'horario_url' => 'nullable|max:255',
            'plan_estudios' => 'nullable',         // JSON array string
            'slug' => 'nullable|unique:programas,slug,' . $programa->id, // Nullable to avoid validation failure if missing from form
            'imagen_url' => 'nullable|max:255',
            'sumilla' => 'nullable|string',
            'por_que_text' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $validated;

        // Decode JSON fields
        if (isset($data['objetivos_academicos']) && is_string($data['objetivos_academicos'])) {
            $data['objetivos_academicos'] = json_decode($data['objetivos_academicos'], true) ?: [];
        }
        if (isset($data['perfil_ingresante']) && is_string($data['perfil_ingresante'])) {
            $data['perfil_ingresante'] = json_decode($data['perfil_ingresante'], true) ?: [];
        }
        if (isset($data['perfil_graduado']) && is_string($data['perfil_graduado'])) {
            $data['perfil_graduado'] = json_decode($data['perfil_graduado'], true) ?: [];
        }
        if (isset($data['plan_estudios']) && is_string($data['plan_estudios'])) {
            $data['plan_estudios'] = json_decode($data['plan_estudios'], true) ?: [];
        }

        // Update slug if nombre changed and slug not manually provided
        if (empty($data['slug']) && $programa->nombre !== $data['nombre']) {
            $data['slug'] = Str::slug($data['nombre']);

            // Ensure uniqueness
            $originalSlug = $data['slug'];
            $count = 1;
            while (Programa::where('slug', $data['slug'])->where('id', '!=', $programa->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $count++;
            }
        }

        if ($request->has('imagen_url')) {
            $data['imagen'] = $validated['imagen_url'];
            unset($data['imagen_url']);
        }

        $programa->update($data);

        // Docentes sync (using parallel arrays from form)
        if ($request->has('docentes_asignados')) {
            $docentesData = [];
            foreach ($request->docentes_asignados as $index => $docenteId) {
                if ($docenteId) {
                    $docentesData[$docenteId] = [
                        'es_coordinador' => ($request->docentes_coordinador[$index] ?? '0') == '1',
                        'rol' => $request->docentes_rol[$index] ?? null,
                        'orden' => $request->docentes_orden[$index] ?? 0
                    ];
                }
            }
            $programa->docentes()->sync($docentesData);
        } else {
            $programa->docentes()->detach();
        }

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa actualizado exitosamente.');
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Programa $programa)
    {
        // Delete related image if exists and starts with programs/
        if ($programa->imagen && str_contains($programa->imagen, 'programas/')) {
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
