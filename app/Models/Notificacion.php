<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'tipo',
        'titulo',
        'mensaje',
        'url',
        'leida_at',
        'notificable_type',
        'notificable_id',
    ];

    protected $casts = [
        'leida_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function notificable()
    {
        return $this->morphTo();
    }

    public function estaLeida(): bool
    {
        return !is_null($this->leida_at);
    }

    public function marcarLeida(): void
    {
        if (!$this->estaLeida()) {
            $this->update(['leida_at' => now()]);
        }
    }

    public static function paraAdmin(User $user, string $tipo, string $titulo, string $url, $notificable, string $mensaje = null): self
    {
        return static::create([
            'user_id'          => $user->id,
            'tipo'             => $tipo,
            'titulo'           => $titulo,
            'mensaje'          => $mensaje,
            'url'              => $url,
            'notificable_type' => get_class($notificable),
            'notificable_id'   => $notificable->id,
        ]);
    }

    public static function paraCliente(Cliente $cliente, string $tipo, string $titulo, string $url, $notificable, string $mensaje = null): self
    {
        return static::create([
            'cliente_id'       => $cliente->id,
            'tipo'             => $tipo,
            'titulo'           => $titulo,
            'mensaje'          => $mensaje,
            'url'              => $url,
            'notificable_type' => get_class($notificable),
            'notificable_id'   => $notificable->id,
        ]);
    }

    public function getTipoIconoAttribute(): string
    {
        return match($this->tipo) {
            'entrega_enviada'        => 'package',
            'entrega_aprobada'       => 'check-circle',
            'entrega_rechazada'      => 'x-circle',
            'documento_enviado'      => 'file-text',
            'documento_aprobado'     => 'file-check',
            'cotizacion_aprobada'    => 'receipt',
            'cotizacion_rechazada'   => 'receipt',
            'publicacion_aprobada'   => 'image',
            'publicacion_rechazada'  => 'image',
            default                  => 'bell',
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'entrega_aprobada', 'documento_aprobado', 'cotizacion_aprobada', 'publicacion_aprobada' => 'success',
            'entrega_rechazada', 'cotizacion_rechazada', 'publicacion_rechazada'                     => 'danger',
            'entrega_enviada', 'documento_enviado'                                                   => 'info',
            default                                                                                  => 'secondary',
        };
    }
}
