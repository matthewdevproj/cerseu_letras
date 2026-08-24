<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;

class AdmisionController extends Controller
{
    public function index()
    {
        // El diseño (grilla, tarjetas, iconos) vive en la vista; de aquí sale
        // solo el texto. El orden coincide con el de las tarjetas.
        $pagina = ContentPage::porSlug('admision');

        return view('admision.index', [
            'secciones' => $pagina?->secciones->where('is_visible', true)->values() ?? collect(),
        ]);
    }
}
