<?php

namespace App\Mail;

use App\Models\Documento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentoEnviado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Documento $documento) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "📄 Nuevo documento para revisar: {$this->documento->nombre}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.documento-enviado');
    }
}
