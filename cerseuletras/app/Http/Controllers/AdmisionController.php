<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\TipoOferta;

class AdmisionController extends Controller
{
    public function index()
    {
        // Esta página no describe un proceso: reparte hacia el de cada tipo de
        // oferta, que es donde vive el de verdad (cronograma, requisitos y
        // formulario propios, todos administrables desde el panel).
        //
        // Hasta agosto de 2026 detallaba aquí el proceso de maestrías y
        // doctorados heredado de la Unidad de Posgrado, con sus fechas y sus
        // enlaces al sistema de admisión de posgrado.unmsm.edu.pe. El CERSEU no
        // ofrece ninguno de los dos.
        $pagina = ContentPage::porSlug('admision');

        return view('admision.index', [
            'intro' => $pagina?->secciones->where('is_visible', true)->first(),
            'tipos' => TipoOferta::cases(),
        ]);
    }
}
