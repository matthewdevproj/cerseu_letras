<?php

namespace App\Http\Controllers;

use App\Models\Programa;

class ProgramaController extends Controller
{
    /**
     * La ficha de un taller o de un curso.
     *
     * El listado ya no vive aquí: cada tipo tiene el suyo en OfertaController,
     * bajo /talleres y /cursos. Este controlador solo sirve la ficha, que es
     * idéntica para los dos.
     */
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
