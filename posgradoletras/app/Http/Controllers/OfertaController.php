<?php

namespace App\Http\Controllers;

use App\Models\AdmisionSetting;
use App\Models\Programa;
use App\Models\SiteSetting;
use App\Models\TipoOferta;

/**
 * Sirve los dos módulos de oferta del CERSEU: talleres y cursos.
 *
 * Ambos tienen la misma estructura —listado, admisión y formulario de
 * solicitud— y se diferencian solo en la duración, así que comparten
 * controlador y plantillas. El tipo llega resuelto desde la ruta.
 */
class OfertaController extends Controller
{
    public function index(TipoOferta $tipoOferta)
    {
        $tipo = $tipoOferta;

        $programas = Programa::visibles()->deTipo($tipo)->ordenPublicacion()->get();
        $settings = SiteSetting::first();

        return view('oferta.index', compact('tipo', 'programas', 'settings'));
    }

    public function admision(TipoOferta $tipoOferta)
    {
        $tipo = $tipoOferta;

        // Si el módulo aún no tiene ajustes, se crean vacíos en vez de romper:
        // la página existe desde que existe la ruta, y el panel la completa.
        $admisionSettings = AdmisionSetting::with('cronogramaItems')->deTipo($tipo)->first()
            ?? AdmisionSetting::create([
                'tipo' => $tipo->value,
                'hero_titulo' => 'Convocatoria 2026-I',
                'hero_subtitulo' => 'Sección ' . $tipo->plural() . ' · CERSEU',
            ]);

        return view('oferta.admision', compact('tipo', 'admisionSettings'));
    }
}
