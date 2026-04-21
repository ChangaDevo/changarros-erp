<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaItem extends Model
{
    protected $table = 'factura_items';

    protected $fillable = [
        'factura_id', 'descripcion', 'cantidad',
        'unidad', 'precio_unitario', 'subtotal', 'orden',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:2',
        'precio_unitario'=> 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function factura() { return $this->belongsTo(Factura::class); }

    protected static function booted(): void
    {
        // Recalcular subtotal del item al guardar
        static::saving(function ($item) {
            $item->subtotal = round($item->cantidad * $item->precio_unitario, 2);
        });
    }
}
