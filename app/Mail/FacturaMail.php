<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Factura $factura,
        public string  $mensajePersonal = ''
    ) {}

    public function envelope(): Envelope
    {
        $tipo  = $factura->tipo_label ?? ucfirst($this->factura->tipo);
        $folio = $this->factura->folio;
        $empresa = $this->factura->creadoPor->name ?? config('app.name');

        return new Envelope(
            subject: "{$tipo} {$folio} — {$empresa}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.factura',
        );
    }

    public function attachments(): array
    {
        // Generar PDF en memoria y adjuntar
        $pdf = Pdf::loadView('admin.facturas.pdf', ['factura' => $this->factura])
                  ->setPaper('letter', 'portrait');

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                $this->factura->folio . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
