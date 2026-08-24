<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudInformacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        // El asunto nombra el tipo para que quien recibe sepa de qué módulo
        // viene la solicitud sin tener que abrir el correo.
        $modulo = $this->lead->tipo?->plural() ?? 'Oferta académica';

        return new Envelope(
            subject: "Nueva solicitud de información - {$modulo} CERSEU Letras",
            replyTo: [$this->lead->correo],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-informacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
