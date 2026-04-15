<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoEntrega extends Model
{
    protected $table = 'archivos_entrega';

    protected $fillable = [
        'entrega_id',
        'nombre',
        'archivo_path',
        'archivo_nombre_original',
        'tipo_archivo',
        'video_url',
        'archivo_tamanio',
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }

    public function getEsVideoUrlAttribute()
    {
        return $this->tipo_archivo === 'video_url';
    }

    public function getEsImagenAttribute()
    {
        return $this->tipo_archivo === 'imagen';
    }

    public function getEsPdfAttribute()
    {
        return $this->tipo_archivo === 'pdf';
    }
}
