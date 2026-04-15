<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaToken extends Model
{
    protected $table = 'meta_tokens';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'plataforma',
        'page_id',
        'ig_account_id',
        'access_token',
        'activo',
        'expires_at',
        'ultima_verificacion',
        'estado_verificacion',
    ];

    protected $casts = [
        'activo'              => 'boolean',
        'expires_at'          => 'datetime',
        'ultima_verificacion' => 'datetime',
        'access_token'        => 'encrypted',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function getPlataformaLabelAttribute(): string
    {
        return match($this->plataforma) {
            'facebook'  => 'Facebook',
            'instagram' => 'Instagram',
            default     => ucfirst($this->plataforma),
        };
    }

    public function getPlataformaBadgeAttribute(): string
    {
        return match($this->plataforma) {
            'facebook'  => 'primary',
            'instagram' => 'danger',
            default     => 'secondary',
        };
    }

    public function getEstaVigente(): bool
    {
        if (!$this->expires_at) return true;
        return $this->expires_at->isFuture();
    }
}
