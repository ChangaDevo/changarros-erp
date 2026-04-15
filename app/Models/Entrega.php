<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillable = [
        'proyecto_id',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'fecha_entrega',
        'notas_cliente',
        'orden',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoEntrega::class);
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable')->orderBy('created_at');
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'secondary',
            'enviado' => 'info',
            'aprobado' => 'success',
            'rechazado' => 'danger',
            'cambios_solicitados' => 'warning',
            default => 'secondary'
        };
    }

    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'enviado' => 'Enviado',
            'aprobado' => 'Aprobado',
            'rechazado' => 'Rechazado',
            'cambios_solicitados' => 'Cambios Solicitados',
            default => $this->estado
        };
    }
}
