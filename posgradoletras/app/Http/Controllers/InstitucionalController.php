<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Programa;

class InstitucionalController extends Controller
{
    public function index()
    {
        return view('institucional.index');
    }

    public function autoridades()
    {
        return view('institucional.autoridades');
    }

    public function profesores()
    {
        $profesores = Docente::activos()
            ->with('programas')
            ->ordenados()
            ->get();

        return view('institucional.profesores', compact('profesores'));
    }

    public function showProfesor($id)
    {
        $profesor = Docente::with('programas')->findOrFail($id);
        return view('institucional.profesor', compact('profesor'));
    }
}
