<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el filtro de tipo desde la URL
        $tipoFiltro = $request->get('tipo', 'todos');

        // Obtener maestrías y doctorados
        $maestrias = Programa::activos()
            ->maestrias()
            ->orderBy('nombre')
            ->get();

        $doctorados = Programa::activos()
            ->doctorados()
            ->orderBy('nombre')
            ->get();

        return view('programas.index', compact('maestrias', 'doctorados', 'tipoFiltro'));
    }

    public function show($slugOrCodigo)
    {
        // Buscar por slug o por código
        $programa = Programa::where('slug', $slugOrCodigo)
            ->orWhere('codigo', $slugOrCodigo)
            ->with([
                'docentes' => function ($query) {
                    $query->orderBy('docente_programa.orden');
                }
            ])
            ->firstOrFail();

        return view('programas.show', [
            'programa' => $programa
        ]);
    }
}
