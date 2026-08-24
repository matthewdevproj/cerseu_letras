<?php

namespace App\Http\Controllers;

use App\Models\DirectorioCerseu;

class DirectorioController extends Controller
{
    /**
     * Muestra la página pública del directorio del CERSEU
     */
    public function index()
    {
        $grupos = DirectorioCerseu::agrupadosPorUnidad();

        return view('directorio.index', compact('grupos'));
    }
}
