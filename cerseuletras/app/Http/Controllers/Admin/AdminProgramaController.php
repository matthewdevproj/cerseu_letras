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
    public function index(Request $request)
    {
        $query = Programa::orderBy('nombre');

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tipo')) {
            if ($tipo = \App\Models\TipoOferta::tryFrom($request->tipo)) {
                $query->where('grado', $tipo->grado());
            }
        }

        $programas = $query->paginate(25)->withQueryString();
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
            'grado' => ['required', \Illuminate\Validation\Rule::in(\App\Models\TipoOferta::grados())],
            'mencion' => 'nullable|max:255',
            'modalidad' => 'nullable|max:100',
            'duracion' => 'nullable|integer',
            'vacantes' => 'nullable|integer',
            'creditos' => 'nullable|integer',
            'grado_otorga' => 'nullable|max:255',
            'grado_otorga_label' => 'nullable|max:100',
            'objetivos_academicos' => 'nullable', // JSON array string
            'perfil_ingresante' => 'nullable',    // JSON array string
            'perfil_graduado' => 'nullable',      // JSON array string
            'plan_url' => 'nullable|max:255',
            'horario_url' => 'nullable|max:255',
            'brochure_url' => 'nullable|max:255',
            'admision_pdf_url' => 'nullable|max:255',
            'horas_academicas' => 'nullable|integer',
            'sesiones' => 'nullable|integer',
            'modulos' => 'nullable|integer',
            'fecha_limite_inscripcion' => 'nullable|max:255',
            'inversion_economica' => 'nullable',   // JSON object string
            'inversion_modalidades' => 'nullable|string', // JSON array string (modalidades de pago)
            'inversion_condiciones' => 'nullable|string',  // JSON array string (condiciones de pago)
            'plan_estudios' => 'nullable',         // JSON array string
            'slug' => 'nullable|unique:programas,slug',
            'imagen_url' => 'nullable|max:255',
            'sumilla' => 'nullable|string',
            'por_que_text' => 'nullable|string',
            'estado' => 'nullable|in:publicado,proximamente,borrador',
            'costo_por_credito' => 'nullable|integer|min:0',
            'semestres_inversion' => 'nullable',   // JSON array string
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
        if (isset($data['semestres_inversion']) && is_string($data['semestres_inversion'])) {
            $data['semestres_inversion'] = json_decode($data['semestres_inversion'], true) ?: [];
        }
        if (isset($data['plan_estudios']) && is_string($data['plan_estudios'])) {
            $data['plan_estudios'] = json_decode($data['plan_estudios'], true) ?: [];
        }
        if (isset($data['inversion_economica']) && is_string($data['inversion_economica'])) {
            $data['inversion_economica'] = $data['inversion_economica'] !== ''
                ? json_decode($data['inversion_economica'], true)
                : null;
        }

        // Viajan en campos aparte del formulario, pero se guardan dentro del
        // mismo JSON; no son columnas, así que no deben llegar al modelo.
        $data['inversion_economica'] = $this->conModalidadesDePago(
            $data['inversion_economica'] ?? null,
            $data['inversion_modalidades'] ?? null
        );
        $data['inversion_economica'] = $this->conCondicionesDePago(
            $data['inversion_economica'],
            $data['inversion_condiciones'] ?? null
        );
        unset($data['inversion_modalidades'], $data['inversion_condiciones']);

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $baseName = $data['nombre'];
            if (!empty($data['mencion'])) {
                $baseName .= ' ' . $data['mencion'];
            }
            $data['slug'] = Str::slug($baseName);

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

        $this->sincronizarDocentes($request, $programa);

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
            'grado' => ['required', \Illuminate\Validation\Rule::in(\App\Models\TipoOferta::grados())],
            'mencion' => 'nullable|max:255',
            'modalidad' => 'nullable|max:100',
            'duracion' => 'nullable|integer',
            'vacantes' => 'nullable|integer',
            'creditos' => 'nullable|integer',
            'grado_otorga' => 'nullable|max:255',
            'grado_otorga_label' => 'nullable|max:100',
            'objetivos_academicos' => 'nullable', // JSON array string
            'perfil_ingresante' => 'nullable',    // JSON array string
            'perfil_graduado' => 'nullable',      // JSON array string
            'plan_url' => 'nullable|max:255',
            'horario_url' => 'nullable|max:255',
            'brochure_url' => 'nullable|max:255',
            'admision_pdf_url' => 'nullable|max:255',
            'horas_academicas' => 'nullable|integer',
            'sesiones' => 'nullable|integer',
            'modulos' => 'nullable|integer',
            'fecha_limite_inscripcion' => 'nullable|max:255',
            'inversion_economica' => 'nullable',   // JSON object string
            'inversion_modalidades' => 'nullable|string', // JSON array string (modalidades de pago)
            'inversion_condiciones' => 'nullable|string',  // JSON array string (condiciones de pago)
            'plan_estudios' => 'nullable',         // JSON array string
            'slug' => 'nullable|unique:programas,slug,' . $programa->id, // Nullable to avoid validation failure if missing from form
            'imagen_url' => 'nullable|max:255',
            'sumilla' => 'nullable|string',
            'por_que_text' => 'nullable|string',
            'estado' => 'nullable|in:publicado,proximamente,borrador',
            'costo_por_credito' => 'nullable|integer|min:0',
            'semestres_inversion' => 'nullable',   // JSON array string
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
        if (isset($data['semestres_inversion']) && is_string($data['semestres_inversion'])) {
            $data['semestres_inversion'] = json_decode($data['semestres_inversion'], true) ?: [];
        }
        if (isset($data['plan_estudios']) && is_string($data['plan_estudios'])) {
            $data['plan_estudios'] = json_decode($data['plan_estudios'], true) ?: [];
        }
        if (isset($data['inversion_economica']) && is_string($data['inversion_economica'])) {
            $data['inversion_economica'] = $data['inversion_economica'] !== ''
                ? json_decode($data['inversion_economica'], true)
                : null;
        }

        // Viajan en campos aparte del formulario, pero se guardan dentro del
        // mismo JSON; no son columnas, así que no deben llegar al modelo.
        $data['inversion_economica'] = $this->conModalidadesDePago(
            $data['inversion_economica'] ?? null,
            $data['inversion_modalidades'] ?? null
        );
        $data['inversion_economica'] = $this->conCondicionesDePago(
            $data['inversion_economica'],
            $data['inversion_condiciones'] ?? null
        );
        unset($data['inversion_modalidades'], $data['inversion_condiciones']);

        // Update slug if nombre changed and slug not manually provided
        if (empty($data['slug']) && ($programa->nombre !== $data['nombre'] || $programa->mencion !== ($data['mencion'] ?? null))) {
            $baseName = $data['nombre'];
            if (!empty($data['mencion'])) {
                $baseName .= ' ' . $data['mencion'];
            }
            $data['slug'] = Str::slug($baseName);

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

        $this->sincronizarDocentes($request, $programa);

        return redirect()->route('admin.programas.index')
            ->with('success', 'Programa actualizado exitosamente.');
    }

    /**
     * Funde las modalidades de pago del repetidor con el resto de la inversión
     * económica, que el formulario envía como dos campos distintos (Obs. N.º 2).
     *
     * Solo se descartan las modalidades sin cuotas utilizables; si no queda
     * ninguna, la clave se omite para no dejar un array vacío en el JSON.
     *
     * @param  array<string, mixed>|null  $inversion
     */
    private function conModalidadesDePago(?array $inversion, ?string $modalidadesJson): ?array
    {
        $modalidades = [];

        foreach ((array) json_decode((string) $modalidadesJson, true) as $modalidad) {
            if (!is_array($modalidad) || empty($modalidad['cuotas'])) {
                continue;
            }

            $modalidades[] = [
                'nombre' => trim((string) ($modalidad['nombre'] ?? '')) ?: null,
                'cuotas' => array_values(array_map(fn ($cuota) => [
                    'etiqueta' => trim((string) ($cuota['etiqueta'] ?? '')) ?: null,
                    'monto' => is_numeric($cuota['monto'] ?? null) ? (float) $cuota['monto'] : null,
                    'fecha' => trim((string) ($cuota['fecha'] ?? '')) ?: null,
                ], (array) $modalidad['cuotas'])),
            ];
        }

        if ($modalidades === []) {
            // Sin modalidades no se toca lo que hubiera: `modalidades` se
            // elimina solo si el JSON llegó y quedó vacío tras limpiarlo.
            if ($inversion !== null) {
                unset($inversion['modalidades']);
            }

            return $inversion;
        }

        $inversion ??= [];
        $inversion['modalidades'] = $modalidades;

        return $inversion;
    }

    /**
     * Funde la lista de condiciones de pago con el resto de la inversión
     * económica; el formulario las envía en su propio campo.
     *
     * Sin condiciones se elimina la clave en lugar de dejar un array vacío,
     * para que el modelo pueda recurrir a los campos antiguos como respaldo.
     *
     * @param  array<string, mixed>|null  $inversion
     */
    private function conCondicionesDePago(?array $inversion, ?string $condicionesJson): ?array
    {
        $condiciones = [];

        foreach ((array) json_decode((string) $condicionesJson, true) as $condicion) {
            $texto = trim((string) (is_array($condicion) ? ($condicion['texto'] ?? '') : $condicion));

            if ($texto !== '') {
                $condiciones[] = $texto;
            }
        }

        if ($condiciones === []) {
            if ($inversion !== null) {
                unset($inversion['condiciones']);
            }

            return $inversion;
        }

        $inversion ??= [];
        $inversion['condiciones'] = $condiciones;

        return $inversion;
    }

    /**
     * Guarda la plana docente que envía el formulario como arrays paralelos.
     *
     * Estaba duplicada en `store` y `update`, con la única diferencia de que
     * una desasignaba al no recibir filas y la otra no; sincronizar con un
     * array vacío hace lo mismo en ambos casos.
     *
     * `coordinador_denominacion` guarda «Coordinador» o «Coordinadora» según lo
     * elegido para ese programa (Obs. N.º 1); cualquier otro valor se descarta.
     */
    private function sincronizarDocentes(Request $request, Programa $programa): void
    {
        $asignados = (array) $request->input('docentes_asignados', []);
        $coordinadores = (array) $request->input('docentes_coordinador', []);
        $denominaciones = (array) $request->input('docentes_coordinador_denominacion', []);
        $roles = (array) $request->input('docentes_rol', []);
        $ordenes = (array) $request->input('docentes_orden', []);

        $docentesData = [];

        foreach ($asignados as $index => $docenteId) {
            if (!$docenteId) {
                continue;
            }

            $esCoordinador = ($coordinadores[$index] ?? '0') == '1';
            $denominacion = trim((string) ($denominaciones[$index] ?? ''));

            $docentesData[$docenteId] = [
                'es_coordinador' => $esCoordinador,
                // Solo tiene sentido en quien coordina; en el resto se limpia
                // para que no quede un valor huérfano si se desmarca la casilla.
                'coordinador_denominacion' => $esCoordinador && in_array($denominacion, Programa::DENOMINACIONES_COORDINADOR, true)
                    ? $denominacion
                    : null,
                'rol' => $roles[$index] ?? null,
                'orden' => $ordenes[$index] ?? 0,
            ];
        }

        $programa->docentes()->sync($docentesData);
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
     * Alterna entre publicado y borrador desde el listado (atajo rápido).
     * El estado «Próximamente» se elige en el formulario del programa.
     */
    public function toggleActive(Programa $programa)
    {
        $nuevo = $programa->es_publicado
            ? Programa::ESTADO_BORRADOR
            : Programa::ESTADO_PUBLICADO;

        $programa->update(['estado' => $nuevo]);

        $status = $nuevo === Programa::ESTADO_PUBLICADO ? 'publicado' : 'pasado a borrador';

        return redirect()->route('admin.programas.index')
            ->with('success', "Programa {$status} exitosamente.");
    }
}
