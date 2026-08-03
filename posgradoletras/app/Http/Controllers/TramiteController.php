<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;

class TramiteController extends Controller
{
    public function index()
    {
        // El diseño de la página vive en la vista; de aquí sale solo el texto.
        // El orden de las secciones es el mismo que el de las tarjetas.
        $pagina = ContentPage::porSlug('tramites');

        return view('tramites.index', [
            'secciones' => $pagina?->secciones->where('is_visible', true)->values() ?? collect(),
        ]);
    }
}
