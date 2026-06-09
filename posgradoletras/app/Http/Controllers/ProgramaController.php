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

        $campos = ['id', 'nombre', 'mencion', 'slug', 'grado', 'vacantes', 'duracion', 'creditos', 'sumilla', 'imagen', 'modalidad'];

        $maestrias = Programa::activos()->maestrias()->select($campos)->orderBy('nombre')->get();
        $doctorados = Programa::activos()->doctorados()->select($campos)->orderBy('nombre')->get();
        $diplomados = Programa::activos()->diplomados()->select($campos)->orderBy('nombre')->get();

        return view('programas.index', compact('maestrias', 'doctorados', 'diplomados', 'tipoFiltro'));
    }

    public function show($slug)
    {
        // Buscar por slug con eager loading optimizado
        $programa = Programa::where('slug', $slug)
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
                'objetivos_academicos',
                'perfil_ingresante',
                'perfil_graduado',
                'plan_url',
                'horario_url',
                'por_que_text',
                'sumilla',
                'plan_estudios',
                'is_active',
                'slug',
                'imagen'
            ])
            ->firstOrFail();

        return view('programas.show', [
            'programa' => $programa
        ]);
    }
}
