<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $fillable = [
        'proyecto_id',
        'nombre',
        'tipo',
        'archivo_path',
        'archivo_nombre_original',
        'archivo_mime',
        'archivo_tamanio',
        'estado',
        'es_sellado',
        'sellado_at',
        'sellado_por',
        'subido_por',
        'visible_cliente',
        'notas',
    ];

    protected $casts = [
        'es_sellado' => 'boolean',
        'visible_cliente' => 'boolean',
        'sellado_at' => 'datetime',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function aprobaciones()
    {
        return $this->hasMany(Aprobacion::class);
    }

    public function subidoPor()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }

    public function selladoPor()
    {
        return $this->belongsTo(User::class, 'sellado_por');
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable')->orderBy('created_at');
    }

    public function getEsPdfAttribute()
    {
        return str_contains($this->archivo_mime ?? '', 'pdf');
    }

    public function getEsImagenAttribute()
    {
        return str_contains($this->archivo_mime ?? '', 'image');
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'borrador' => 'secondary',
            'enviado' => 'info',
            'aprobado' => 'success',
            'sellado' => 'dark',
            default => 'secondary'
        };
    }
}
