<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table = 'cotizacion_items';

    protected $fillable = ['cotizacion_id', 'descripcion', 'cantidad', 'precio_unitario', 'total', 'orden'];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function cotizacion() { return $this->belongsTo(Cotizacion::class); }
}
