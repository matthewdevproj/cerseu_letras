<?php

namespace App\Http\Controllers;

use App\Models\DirectorioPosgrado;

class DirectorioController extends Controller
{
    /**
     * Muestra la página pública del directorio de posgrado
     */
    public function index()
    {
        $grupos = DirectorioPosgrado::agrupadosPorUnidad();

        return view('directorio.index', compact('grupos'));
    }
}
