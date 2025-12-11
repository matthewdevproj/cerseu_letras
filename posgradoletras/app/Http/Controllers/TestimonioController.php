<?php

namespace App\Http\Controllers;

use App\Models\Testimonio;

class TestimonioController extends Controller
{
    public function index()
    {
        $testimonios = Testimonio::publicados()
            ->with('programa')
            ->recientes()
            ->paginate(12);

        return view('testimonios.index', compact('testimonios'));
    }

    public function recientes($limit = 6)
    {
        $testimonios = Testimonio::publicados()
            ->with('programa')
            ->recientes()
            ->take($limit)
            ->get();

        return response()->json($testimonios);
    }
}
