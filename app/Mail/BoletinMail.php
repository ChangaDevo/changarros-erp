<?php

namespace App\Mail;

use App\Models\CampanaEmail;
use App\Models\CampanaContacto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BoletinMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $htmlRendered;

    public function __construct(
        public CampanaEmail    $campana,
        public CampanaContacto $contacto,
    ) {
        $this->htmlRendered = $campana->renderizar($contacto);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->campana->remitente_email, $this->campana->remitente_nombre),
            subject: str_replace(
                ['{nombre}', '{apellido}', '{empresa}'],
                [$this->contacto->nombre, $this->contacto->apellido ?? '', $this->contacto->empresa ?? ''],
                $this->campana->asunto
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.boletin',
        );
    }
}
