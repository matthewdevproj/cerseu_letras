<?php

namespace App\Http\Controllers;

use App\Models\Informativo;

class InformativoController extends Controller
{
    /**
     * Página pública con todos los informativos agrupados
     */
    public function index()
    {
        $informativos = Informativo::ordenado()->get()->groupBy('categoria');

        return view('informativos.index', compact('informativos'));
    }

    /**
     * Obtener informativos para mostrar en home (agrupados por categoría)
     */
    public static function getForHome()
    {
        try {
            return Informativo::ordenado()->get()->groupBy('categoria');
        } catch (\Exception $e) {
            return collect([]);
        }
    }
}
