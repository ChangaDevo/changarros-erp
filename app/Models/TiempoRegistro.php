<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiempoRegistro extends Model
{
    protected $table = 'tiempo_registros';

    protected $fillable = [
        'proyecto_id', 'user_id', 'tarea', 'tipo',
        'minutos', 'fecha', 'facturable', 'notas', 'timer_inicio',
    ];

    protected $casts = [
        'fecha'        => 'date',
        'facturable'   => 'boolean',
        'timer_inicio' => 'datetime',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function proyecto() { return $this->belongsTo(Proyecto::class); }
    public function user()     { return $this->belongsTo(User::class); }

    // ── Accessors ─────────────────────────────────────────────

    /** Devuelve "1h 30m", "45m", "2h" */
    public function getDuracionFormateadaAttribute(): string
    {
        $h = intdiv($this->minutos, 60);
        $m = $this->minutos % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}m";
        if ($h > 0)            return "{$h}h";
        return "{$m}m";
    }

    /** Horas decimales para cálculos */
    public function getHorasAttribute(): float
    {
        return round($this->minutos / 60, 2);
    }

    public function getTipoColorAttribute(): string
    {
        return match($this->tipo) {
            'diseño'         => 'primary',
            'redaccion'      => 'info',
            'reunion'        => 'warning',
            'revision'       => 'secondary',
            'desarrollo'     => 'success',
            'administracion' => 'dark',
            default          => 'light',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'diseño'         => 'Diseño',
            'redaccion'      => 'Redacción',
            'reunion'        => 'Reunión',
            'revision'       => 'Revisión',
            'desarrollo'     => 'Desarrollo',
            'administracion' => 'Administración',
            default          => 'Otro',
        };
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeDelMes($query, $anio = null, $mes = null)
    {
        $anio = $anio ?? now()->year;
        $mes  = $mes  ?? now()->month;
        return $query->whereYear('fecha', $anio)->whereMonth('fecha', $mes);
    }

    public function scopeFacturables($query)
    {
        return $query->where('facturable', true);
    }
}
