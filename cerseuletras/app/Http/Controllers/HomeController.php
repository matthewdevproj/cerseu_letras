<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\TipoOferta;
use App\Models\Docente;
use App\Models\Testimonio;

class HomeController extends Controller
{
    public function index()
    {
        // Agrupada por tipo y en el orden del enum: la portada la recorre sin
        // saber cuántos tipos hay ni cómo se llaman.
        $ofertaPorTipo = [];

        foreach (TipoOferta::cases() as $tipo) {
            $ofertaPorTipo[$tipo->value] = Programa::visibles()
                ->deTipo($tipo)->ordenPublicacion()->get();
        }

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

        // Obtener testimonios publicados recientes.
        // `with('programa')` evita una consulta por testimonio: la sección
        // muestra el programa de cada uno.
        $testimonios = Testimonio::publicados()
            ->recientes()
            ->with('programa')
            ->get();

        return view('home', compact('ofertaPorTipo', 'docentes', 'testimonios'));
    }
}
