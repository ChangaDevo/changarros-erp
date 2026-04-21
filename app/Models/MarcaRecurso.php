<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MarcaRecurso extends Model
{
    protected $table = 'marca_recursos';

    protected $fillable = [
        'marca_id', 'tipo', 'nombre', 'descripcion',
        'archivo_path', 'archivo_nombre_original', 'archivo_mime', 'archivo_tamanio',
        'color_hex', 'color_nombre', 'variante', 'orden',
    ];

    public function marca() { return $this->belongsTo(Marca::class); }

    public function getUrlAttribute(): ?string
    {
        return $this->archivo_path ? Storage::url($this->archivo_path) : null;
    }

    public function getTamanioFormateadoAttribute(): string
    {
        if (!$this->archivo_tamanio) return '';
        $kb = $this->archivo_tamanio / 1024;
        return $kb > 1024
            ? round($kb / 1024, 1) . ' MB'
            : round($kb, 0) . ' KB';
    }

    public function getEsImagenAttribute(): bool
    {
        return str_starts_with($this->archivo_mime ?? '', 'image/');
    }

    public function getTipoIconoAttribute(): string
    {
        return match($this->tipo) {
            'logo'       => 'image',
            'tipografia' => 'type',
            'color'      => 'palette',
            'template'   => 'file-text',
            default      => 'paperclip',
        };
    }
}
