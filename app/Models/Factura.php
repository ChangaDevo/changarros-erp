<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\BelongsToTenant;

class Factura extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'cliente_id', 'proyecto_id', 'creado_por',
        'folio', 'tipo', 'estado',
        'fecha_emision', 'fecha_vencimiento',
        'subtotal', 'descuento', 'impuesto_porcentaje', 'impuesto_monto', 'total',
        'moneda', 'metodo_pago',
        'notas', 'condiciones',
        'enviada_at', 'pagada_at', 'token_publico',
    ];

    protected $casts = [
        'fecha_emision'      => 'date',
        'fecha_vencimiento'  => 'date',
        'enviada_at'         => 'datetime',
        'pagada_at'          => 'datetime',
        'subtotal'           => 'decimal:2',
        'descuento'          => 'decimal:2',
        'impuesto_porcentaje'=> 'decimal:2',
        'impuesto_monto'     => 'decimal:2',
        'total'              => 'decimal:2',
    ];

    // ── Boot ──────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($f) {
            if (empty($f->token_publico)) {
                $f->token_publico = Str::random(40);
            }
            if (empty($f->folio)) {
                $f->folio = static::generarFolio($f->tipo);
            }
        });
    }

    public static function generarFolio(string $tipo): string
    {
        $prefix = $tipo === 'recibo' ? 'REC' : 'FAC';
        $ultimo = static::where('tipo', $tipo)->max('id') ?? 0;
        return $prefix . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    // ── Relaciones ────────────────────────────────────────────

    public function cliente()   { return $this->belongsTo(Cliente::class); }
    public function proyecto()  { return $this->belongsTo(Proyecto::class); }
    public function creadoPor() { return $this->belongsTo(User::class, 'creado_por'); }
    public function items()     { return $this->hasMany(FacturaItem::class)->orderBy('orden'); }

    // ── Accessors ─────────────────────────────────────────────

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'secondary',
            'enviada'   => 'primary',
            'pagada'    => 'success',
            'cancelada' => 'danger',
            default     => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'Borrador',
            'enviada'   => 'Enviada',
            'pagada'    => 'Pagada',
            'cancelada' => 'Cancelada',
            default     => ucfirst($this->estado),
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return $this->tipo === 'recibo' ? 'Recibo' : 'Factura';
    }

    public function getTipoIconoAttribute(): string
    {
        return $this->tipo === 'recibo' ? 'file-check' : 'file-text';
    }

    public function getEstaVencidaAttribute(): bool
    {
        return $this->fecha_vencimiento
            && $this->fecha_vencimiento->isPast()
            && !in_array($this->estado, ['pagada', 'cancelada']);
    }

    // ── Recalcular totales ────────────────────────────────────

    public function recalcularTotales(): void
    {
        $subtotal           = $this->items()->sum(\DB::raw('cantidad * precio_unitario'));
        $descuento          = (float) $this->descuento;
        $base               = $subtotal - $descuento;
        $impuesto           = round($base * ($this->impuesto_porcentaje / 100), 2);
        $total              = round($base + $impuesto, 2);

        $this->update([
            'subtotal'        => $subtotal,
            'impuesto_monto'  => $impuesto,
            'total'           => $total,
        ]);
    }
}
