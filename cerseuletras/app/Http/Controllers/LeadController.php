<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Models\TipoOferta;
use App\Jobs\EnviarAvisoDeSolicitud;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request, TipoOferta $tipoOferta)
    {
        $lead = Lead::create($request->validated() + ['tipo' => $tipoOferta->value]);

        // Guardar primero y avisar después, en ese orden: el registro de la
        // solicitud no puede depender de que el correo funcione. Si el envío
        // falla queda anotado en el propio lead y el panel lo señala; el
        // visitante no se entera, que es lo correcto — su solicitud sí está.
        //
        // A la cola y no en la peticion: el visitante no tiene por que esperar
        // a que responda un servidor de correo, y un fallo pasajero se
        // reintenta en vez de perderse.
        // El try no sobra aunque el trabajo vaya a una cola: con
        // QUEUE_CONNECTION=sync —las pruebas, o un despliegue pequeno— el
        // trabajo se ejecuta aqui mismo, y el relanzado que hace posible el
        // reintento llegaria hasta el visitante como un 500. La solicitud ya
        // esta guardada; que el correo falle no puede romper el formulario.
        try {
            EnviarAvisoDeSolicitud::dispatch($lead);
        } catch (\Throwable $e) {
            Log::error('No se pudo encolar el aviso de la solicitud #' . $lead->id . ': ' . $e->getMessage());
        }

        return back()->with('success', '¡Gracias! Tu solicitud fue registrada. Nos pondremos en contacto contigo pronto.');
    }
}
