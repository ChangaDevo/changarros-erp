<?php

namespace App\Mail;

use App\Models\Cotizacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionEstadoCambiado extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cotizacion $cotizacion,
        public string $estado  // 'aprobada' | 'rechazada'
    ) {}

    public function envelope(): Envelope
    {
        $emoji  = $this->estado === 'aprobada' ? '✅' : '❌';
        $label  = $this->estado === 'aprobada' ? 'aprobada' : 'rechazada';
        return new Envelope(subject: "{$emoji} Cotización {$label}: {$this->cotizacion->nombre}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cotizacion-estado');
    }
}
