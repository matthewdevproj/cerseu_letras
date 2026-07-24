<?php

namespace App\Mail;

use App\Models\DiplomadoLead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudDiplomado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DiplomadoLead $lead)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de información - Diplomados Posgrado Letras',
            replyTo: [$this->lead->correo],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.diplomado-lead',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
