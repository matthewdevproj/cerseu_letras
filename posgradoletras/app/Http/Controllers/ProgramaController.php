<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el filtro de tipo desde la URL
        $tipoFiltro = $request->get('tipo', 'todos');

        // `estado` es necesario para que la tarjeta sepa marcar «Próximamente».
        $campos = ['id', 'nombre', 'mencion', 'slug', 'grado', 'vacantes', 'duracion', 'creditos', 'sumilla', 'imagen', 'modalidad', 'horas_academicas', 'brochure_url', 'estado'];

        $maestrias = Programa::visibles()->maestrias()->select($campos)->ordenPublicacion()->get();
        $doctorados = Programa::visibles()->doctorados()->select($campos)->ordenPublicacion()->get();

        return view('programas.index', compact('maestrias', 'doctorados', 'tipoFiltro'));
    }

    public function show($slug)
    {
        // Los borradores no existen de cara al público: se descartan en la
        // propia consulta, así que `firstOrFail()` responde 404 igual que ante
        // un slug inventado, sin revelar que el programa existe.
        $programa = Programa::where('slug', $slug)
            ->visibles()
            ->with([
                'docentes' => function ($query) {
                    $query->select([
                        'docentes.id',
                        'docentes.slug',
                        'docentes.nombres',
                        'docentes.apellidos',
                        'docentes.grado',
                        'docentes.email',
                        'docentes.orcid',
                        'docentes.cti_vitae',
                        'docentes.linkedin',
                        'docentes.estado'
                    ])
                        ->where('estado', 1)
                        ->orderBy('docente_programa.orden');
                }
            ])
            ->select([
                'id',
                'grado',
                'nombre',
                'mencion',
                'modalidad',
                'vacantes',
                'duracion',
                'creditos',
                'grado_otorga',
                // Rótulo de la denominación: sin él la portada mostraría solo
                // el contenido, sin el «Otorga:» que lo precede.
                'grado_otorga_label',
                'objetivos_academicos',
                'perfil_ingresante',
                'perfil_graduado',
                'plan_url',
                'horario_url',
                'brochure_url',
                'admision_pdf_url',
                'horas_academicas',
                'fecha_limite_inscripcion',
                'inversion_economica',
                // Necesarias para calcular la inversión total en la ficha.
                'costo_por_credito',
                'semestres_inversion',
                'por_que_text',
                'sumilla',
                'plan_estudios',
                'is_active',
                'estado',
                'slug',
                'imagen'
            ])
            ->firstOrFail();

        // Anunciado pero sin detalle todavía: se muestra el aviso en lugar de
        // una ficha a medio llenar.
        if ($programa->es_proximamente) {
            return response()->view('programas.proximamente', [
                'programa' => $programa,
            ]);
        }

        return view('programas.show', [
            'programa' => $programa
        ]);
    }
}
