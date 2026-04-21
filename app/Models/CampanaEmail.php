<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class CampanaEmail extends Model
{
    use BelongsToTenant;

    protected $table = 'campanas_email';

    protected $fillable = [
        'cliente_id', 'creado_por', 'titulo', 'asunto',
        'remitente_nombre', 'remitente_email',
        'cuerpo_html', 'estado',
        'total_contactos', 'total_enviados', 'total_errores',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function contactos()
    {
        return $this->hasMany(CampanaContacto::class, 'campana_id');
    }

    /** Renderiza el HTML sustituyendo variables para un contacto */
    public function renderizar(CampanaContacto $contacto): string
    {
        $vars = [
            '{nombre}'   => $contacto->nombre,
            '{apellido}' => $contacto->apellido ?? '',
            '{empresa}'  => $contacto->empresa ?? '',
            '{email}'    => $contacto->email,
            '{nombre_completo}' => trim($contacto->nombre . ' ' . ($contacto->apellido ?? '')),
        ];

        // Variables extra del CSV
        if ($contacto->datos_extra) {
            foreach ($contacto->datos_extra as $key => $value) {
                $vars['{' . $key . '}'] = $value;
            }
        }

        return str_replace(array_keys($vars), array_values($vars), $this->cuerpo_html);
    }

    public function getPorcentajeEnviadoAttribute(): int
    {
        if ($this->total_contactos === 0) return 0;
        return (int) round(($this->total_enviados / $this->total_contactos) * 100);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'secondary',
            'enviando'  => 'warning',
            'enviada'   => 'success',
            'pausada'   => 'danger',
            default     => 'secondary',
        };
    }
}
