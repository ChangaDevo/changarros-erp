<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampanaContacto extends Model
{
    protected $table = 'campana_contactos';

    protected $fillable = [
        'campana_id', 'nombre', 'apellido', 'email',
        'empresa', 'datos_extra', 'estado', 'enviado_at', 'error_mensaje',
    ];

    protected $casts = [
        'datos_extra' => 'array',
        'enviado_at'  => 'datetime',
    ];

    public function campana()
    {
        return $this->belongsTo(CampanaEmail::class, 'campana_id');
    }
}
