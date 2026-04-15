<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'proyecto_id',
        'entrega_id',
        'concepto',
        'monto',
        'estado',
        'fecha_vencimiento',
        'fecha_pago',
        'metodo_pago',
        'referencia_codi',
        'qr_codigo_path',
        'comprobante_path',
        'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'datetime',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function entrega()
    {
        return $this->belongsTo(Entrega::class);
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'warning',
            'pagado' => 'success',
            'vencido' => 'danger',
            'cancelado' => 'secondary',
            default => 'secondary'
        };
    }

    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'vencido' => 'Vencido',
            'cancelado' => 'Cancelado',
            default => $this->estado
        };
    }
}
