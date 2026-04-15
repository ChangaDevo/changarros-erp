<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaCotizacionItem extends Model
{
    protected $table = 'plantilla_cotizacion_items';

    protected $fillable = [
        'plantilla_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'orden',
    ];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    public function plantilla()
    {
        return $this->belongsTo(PlantillaCotizacion::class, 'plantilla_id');
    }
}
