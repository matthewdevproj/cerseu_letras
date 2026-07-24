<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiplomadoLeadRequest;
use App\Mail\NuevaSolicitudDiplomado;
use App\Models\DiplomadoLead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DiplomadoLeadController extends Controller
{
    public function store(StoreDiplomadoLeadRequest $request)
    {
        $lead = DiplomadoLead::create($request->validated());

        try {
            Mail::to(config('contacts.admision'))->send(new NuevaSolicitudDiplomado($lead));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de solicitud de información de diplomado: ' . $e->getMessage());
        }

        return back()->with('success', '¡Gracias! Tu solicitud fue registrada. Nos pondremos en contacto contigo pronto.');
    }
}
