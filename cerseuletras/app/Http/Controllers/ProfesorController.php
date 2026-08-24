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

        $cursos = Programa::activos()->cursos()->orderBy('nombre')->get();
        $talleres = Programa::activos()->talleres()->orderBy('nombre')->get();

        return view('profesores.index', compact('profesores', 'cursos', 'talleres'));
    }

    public function byPrograma($slug)
    {
        $selectedPrograma = Programa::where('slug', $slug)
            ->firstOrFail();

        $profesores = $selectedPrograma->docentes()
            ->orderBy('docente_programa.orden')
            ->get();

        $cursos = Programa::activos()->cursos()->orderBy('nombre')->get();
        $talleres = Programa::activos()->talleres()->orderBy('nombre')->get();

        return view('profesores.index', compact('profesores', 'selectedPrograma', 'cursos', 'talleres'));
    }

    public function show($slug)
    {
        $profesor = Docente::where('slug', $slug)
            ->with('programas')
            ->firstOrFail();
        return view('profesores.show', compact('profesor'));
    }
}
