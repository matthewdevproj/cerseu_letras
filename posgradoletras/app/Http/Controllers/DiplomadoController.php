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
        $settings = SiteSetting::get();

        return view('diplomados.index', compact('diplomados', 'settings'));
    }

    public function admision()
    {
        $admisionSettings = AdmisionDiplomadoSetting::get();

        return view('diplomados.admision', compact('admisionSettings'));
    }
}
