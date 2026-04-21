<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class Proyecto extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'cliente_id',
        'nombre',
        'descripcion',
        'estado',
        'monto_total',
        'horas_estimadas',
        'tarifa_hora',
        'fecha_inicio',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'notas',
        'creado_por',
        'carpeta_drive',
    ];

    protected $casts = [
        'fecha_inicio'           => 'date',
        'fecha_entrega_estimada' => 'date',
        'fecha_entrega_real'     => 'date',
        'monto_total'            => 'decimal:2',
        'horas_estimadas'        => 'decimal:2',
        'tarifa_hora'            => 'decimal:2',
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

    public function tiempos()
    {
        return $this->hasMany(TiempoRegistro::class);
    }

    // ── Rentabilidad ──────────────────────────────────────────

    /** Total de minutos registrados en el proyecto */
    public function getTotalMinutosAttribute(): int
    {
        return $this->tiempos()->sum('minutos');
    }

    /** Total de horas (decimal) */
    public function getTotalHorasAttribute(): float
    {
        return round($this->total_minutos / 60, 2);
    }

    /** Ingresos reales (pagos marcados como pagados) */
    public function getIngresosRealesAttribute(): float
    {
        return (float) $this->pagos()->where('estado', 'pagado')->sum('monto');
    }

    /** Costo de las horas trabajadas (horas × tarifa) */
    public function getCostoHorasAttribute(): float
    {
        if (!$this->tarifa_hora) return 0;
        return round($this->total_horas * $this->tarifa_hora, 2);
    }

    /** Ganancia = ingresos reales - costo horas */
    public function getGananciaAttribute(): float
    {
        return round($this->ingresos_reales - $this->costo_horas, 2);
    }

    /** Margen de rentabilidad % */
    public function getMargenAttribute(): float
    {
        if ($this->ingresos_reales <= 0) return 0;
        return round(($this->ganancia / $this->ingresos_reales) * 100, 1);
    }

    /** Porcentaje de horas usadas vs estimadas */
    public function getPorcentajeHorasAttribute(): float
    {
        if (!$this->horas_estimadas || $this->horas_estimadas <= 0) return 0;
        return round(($this->total_horas / $this->horas_estimadas) * 100, 1);
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
