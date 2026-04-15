<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $fillable = [
        'cliente_id',
        'nombre',
        'descripcion',
        'estado',
        'monto_total',
        'fecha_inicio',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'notas',
        'creado_por',
        'carpeta_drive',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_entrega_estimada' => 'date',
        'fecha_entrega_real' => 'date',
        'monto_total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class)->orderBy('orden');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function brief()
    {
        return $this->hasOne(BriefCreativo::class);
    }

    public function usuariosCompartidos()
    {
        return $this->belongsToMany(User::class, 'proyecto_usuarios')
                    ->withPivot('rol')
                    ->withTimestamps();
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'cotizando' => 'secondary',
            'en_desarrollo' => 'primary',
            'en_revision' => 'warning',
            'aprobado' => 'success',
            'finalizado' => 'dark',
            default => 'secondary'
        };
    }

    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'cotizando' => 'Cotizando',
            'en_desarrollo' => 'En Desarrollo',
            'en_revision' => 'En Revisión',
            'aprobado' => 'Aprobado',
            'finalizado' => 'Finalizado',
            default => $this->estado
        };
    }
}
