<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';

    protected $fillable = [
        'cliente_id',
        'proyecto_id',
        'created_by',
        'red_social',
        'titulo',
        'descripcion',
        'archivo_path',
        'fecha_programada',
        'estado',
        'nota_cliente',
        'audiencia_sugerida',
        'error_publicacion',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRedSocialLabelAttribute(): string
    {
        return match($this->red_social) {
            'instagram' => 'Instagram',
            'facebook'  => 'Facebook',
            'tiktok'    => 'TikTok',
            'twitter'   => 'Twitter / X',
            'linkedin'  => 'LinkedIn',
            'youtube'   => 'YouTube',
            default     => ucfirst($this->red_social),
        };
    }

    public function getRedSocialColorAttribute(): string
    {
        return match($this->red_social) {
            'instagram' => '#E1306C',
            'facebook'  => '#1877F2',
            'tiktok'    => '#010101',
            'twitter'   => '#1DA1F2',
            'linkedin'  => '#0A66C2',
            'youtube'   => '#FF0000',
            default     => '#6c757d',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'Borrador',
            'propuesto' => 'Pendiente de aprobación',
            'aprobado'  => 'Aprobado — en cola',
            'rechazado' => 'Rechazado',
            'publicado' => 'Publicado',
            'error'     => 'Error al publicar',
            default     => ucfirst($this->estado),
        };
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'secondary',
            'propuesto' => 'warning',
            'aprobado'  => 'success',
            'rechazado' => 'danger',
            'publicado' => 'primary',
            'error'     => 'danger',
            default     => 'secondary',
        };
    }

    public function getCalendarColorAttribute(): array
    {
        return match($this->estado) {
            'borrador'  => ['bg' => '#6c757d', 'border' => '#495057'],
            'propuesto' => ['bg' => '#fd7e14', 'border' => '#dc6502'],
            'aprobado'  => ['bg' => '#198754', 'border' => '#157347'],
            'rechazado' => ['bg' => '#dc3545', 'border' => '#b02a37'],
            'publicado' => ['bg' => '#0d6efd', 'border' => '#0a58ca'],
            'error'     => ['bg' => '#dc3545', 'border' => '#b02a37'],
            default     => ['bg' => '#6c757d', 'border' => '#495057'],
        };
    }
}
