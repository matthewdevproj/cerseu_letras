<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Docente;

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

        // Obtener docentes activos limitados a 6
        $docentes = Docente::activos()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->limit(6)
            ->get();

        return view('home', compact('maestrias', 'doctorados', 'docentes'));
    }
}
