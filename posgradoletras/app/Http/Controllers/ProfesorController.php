<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Programa;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Docente::activos()
            ->with('programas')
            ->ordenados()
            ->get();

        $maestrias = Programa::activos()->maestrias()->orderBy('nombre')->get();
        $doctorados = Programa::activos()->doctorados()->orderBy('nombre')->get();

        return view('profesores.index', compact('profesores', 'maestrias', 'doctorados'));
    }

    public function byPrograma($slug)
    {
        $selectedPrograma = Programa::where('slug', $slug)
            ->firstOrFail();

        $profesores = $selectedPrograma->docentes()
            ->orderBy('docente_programa.orden')
            ->get();

        $maestrias = Programa::activos()->maestrias()->orderBy('nombre')->get();
        $doctorados = Programa::activos()->doctorados()->orderBy('nombre')->get();

        return view('profesores.index', compact('profesores', 'selectedPrograma', 'maestrias', 'doctorados'));
    }

    public function show($id)
    {
        $profesor = Docente::with('programas')->findOrFail($id);
        return view('profesores.show', compact('profesor'));
    }
}
