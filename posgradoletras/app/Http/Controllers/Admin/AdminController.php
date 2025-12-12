<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Programa;
use App\Models\Docente;
use App\Models\Testimonio;
use App\Models\DirectorioPosgrado;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index()
    {
        $stats = [
            'total_programas' => Programa::count(),
            'programas_activos' => Programa::activos()->count(),
            'total_maestrias' => Programa::maestrias()->count(),
            'total_doctorados' => Programa::doctorados()->count(),
            'total_docentes' => Docente::count(),
            'docentes_activos' => Docente::activos()->count(),
            'total_testimonios' => Testimonio::count(),
            'testimonios_publicados' => Testimonio::publicados()->count(),
            'total_directorio' => DirectorioPosgrado::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
