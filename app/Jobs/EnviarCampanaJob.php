<?php

namespace App\Jobs;

use App\Mail\BoletinMail;
use App\Models\CampanaContacto;
use App\Models\CampanaEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarCampanaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 60;

    public function __construct(
        public CampanaEmail    $campana,
        public CampanaContacto $contacto,
    ) {}

    public function handle(): void
    {
        // No reenviar si ya fue enviado
        if ($this->contacto->estado === 'enviado') return;

        try {
            Mail::send(new BoletinMail($this->campana, $this->contacto));

            $this->contacto->update([
                'estado'     => 'enviado',
                'enviado_at' => now(),
            ]);

            // Incrementar contador
            $this->campana->increment('total_enviados');

        } catch (\Throwable $e) {
            $this->contacto->update([
                'estado'         => 'error',
                'error_mensaje'  => $e->getMessage(),
            ]);
            $this->campana->increment('total_errores');
        }

        // Marcar campaña como enviada si ya se procesaron todos
        $pendientes = $this->campana->contactos()->where('estado', 'pendiente')->count();
        if ($pendientes === 0) {
            $this->campana->update([
                'estado'     => 'enviada',
                'enviado_at' => now(),
            ]);
        }
    }
}
