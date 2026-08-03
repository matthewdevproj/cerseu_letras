<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDiplomadoLeadRequest;
use App\Models\DiplomadoLead;
use App\Services\AvisoDeSolicitud;

class DiplomadoLeadController extends Controller
{
    public function store(StoreDiplomadoLeadRequest $request)
    {
        $lead = DiplomadoLead::create($request->validated());

        // Guardar primero y avisar después, en ese orden: el registro de la
        // solicitud no puede depender de que el correo funcione. Si el envío
        // falla queda anotado en el propio lead y el panel lo señala; el
        // visitante no se entera, que es lo correcto — su solicitud sí está.
        AvisoDeSolicitud::enviar($lead);

        return back()->with('success', '¡Gracias! Tu solicitud fue registrada. Nos pondremos en contacto contigo pronto.');
    }
}
