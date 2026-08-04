<?php

namespace App\Http\Controllers;

use App\Models\AdmisionDiplomadoSetting;
use App\Models\Programa;
use App\Models\SiteSetting;

class DiplomadoController extends Controller
{
    public function index()
    {
        $diplomados = Programa::visibles()->diplomados()->ordenPublicacion()->get();
        $settings = SiteSetting::first();

        return view('diplomados.index', compact('diplomados', 'settings'));
    }

    public function admision()
    {
        $admisionSettings = AdmisionDiplomadoSetting::with('cronogramaItems')->first()
            ?? AdmisionDiplomadoSetting::create([
                'hero_titulo' => 'Convocatoria 2026-I',
                'hero_subtitulo' => 'Sección Diplomados · Unidad de Posgrado',
            ]);

        return view('diplomados.admision', compact('admisionSettings'));
    }
}
