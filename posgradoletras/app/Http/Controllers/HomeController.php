<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Docente;
use App\Models\Testimonio;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener maestrías activas ordenadas por nombre
        $maestrias = Programa::activos()
            ->maestrias()
            ->orderBy('nombre')
            ->get();

        // Obtener doctorados activos ordenados por nombre
        $doctorados = Programa::activos()
            ->doctorados()
            ->orderBy('nombre')
            ->get();

        // Obtener docentes que son coordinadores de programa
        $docentes = Docente::activos()
            ->whereHas('programas', function ($query) {
                $query->where('docente_programa.es_coordinador', 1);
            })
            ->with([
                'programas' => function ($query) {
                    $query->where('docente_programa.es_coordinador', 1);
                }
            ])
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->limit(6)
            ->get();

        // Obtener testimonios publicados recientes
        $testimonios = Testimonio::publicados()
            ->recientes()
            ->get();

        return view('home', compact('maestrias', 'doctorados', 'docentes', 'testimonios'));
    }
}
