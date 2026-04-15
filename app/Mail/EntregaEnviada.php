<?php

namespace App\Mail;

use App\Models\Entrega;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntregaEnviada extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Entrega $entrega) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva entrega para revisar: {$this->entrega->titulo}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.entrega-enviada');
    }
}
