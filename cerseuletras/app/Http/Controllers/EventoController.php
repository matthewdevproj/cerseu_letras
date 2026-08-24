<?php

namespace App\Http\Controllers;

use App\Models\Evento;

class EventoController extends Controller
{
    /**
     * Página pública de eventos
     */
    public function index()
    {
        $eventos = Evento::activos()
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(12);

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Obtener eventos para el home (próximos/activos)
     */
    public static function getForHome($limit = 6)
    {
        return Evento::activos()
            ->where(function ($query) {
                $query->where('fecha_inicio', '>=', now()->startOfDay())
                    ->orWhere('fecha_fin', '>=', now()->startOfDay());
            })
            ->orderBy('orden')
            ->orderBy('fecha_inicio')
            ->limit($limit)
            ->get();
    }
}
