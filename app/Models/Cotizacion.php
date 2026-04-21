<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Traits\BelongsToTenant;

class Cotizacion extends Model
{
    use BelongsToTenant;
    protected $table = 'cotizaciones';

    protected $fillable = [
        'cliente_id', 'proyecto_id', 'nombre', 'estado',
        'iva_porcentaje', 'subtotal', 'iva_monto', 'total',
        'notas', 'token', 'fecha_vencimiento',
        'visto_at', 'aprobado_at', 'aprobado_ip', 'aprobado_nombre',
        'rechazado_at', 'razon_rechazo', 'creado_por',
    ];

    protected $casts = [
        'subtotal'         => 'decimal:2',
        'iva_monto'        => 'decimal:2',
        'total'            => 'decimal:2',
        'iva_porcentaje'   => 'decimal:2',
        'fecha_vencimiento'=> 'date',
        'visto_at'         => 'datetime',
        'aprobado_at'      => 'datetime',
        'rechazado_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = Str::random(32);
            }
        });
    }

    public function cliente()   { return $this->belongsTo(Cliente::class); }
    public function proyecto()  { return $this->belongsTo(Proyecto::class); }
    public function items()     { return $this->hasMany(CotizacionItem::class)->orderBy('orden'); }
    public function creadoPor() { return $this->belongsTo(User::class, 'creado_por'); }

    public function recalcular(): void
    {
        $subtotal = $this->items()->sum(DB::raw('cantidad * precio_unitario'));
        $iva      = round($subtotal * ($this->iva_porcentaje / 100), 2);
        $this->update([
            'subtotal'  => $subtotal,
            'iva_monto' => $iva,
            'total'     => $subtotal + $iva,
        ]);
    }

    public function getPublicUrlAttribute(): string
    {
        if (empty($this->token)) return '#';
        return route('cotizacion.publica', $this->token);
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match($this->estado) {
            'borrador'  => 'secondary',
            'enviada'   => 'primary',
            'vista'     => 'info',
            'aprobada'  => 'success',
            'rechazada' => 'danger',
            'vencida'   => 'warning',
            default     => 'secondary',
        };
    }

    public function getWhatsappUrlAttribute(): string
    {
        $msg = urlencode("Hola, te comparto tu cotización \"{$this->nombre}\" de Changarrito Estudio Creativo:\n\n" . $this->public_url);
        return "https://wa.me/?text={$msg}";
    }

    public function getEmailUrlAttribute(): string
    {
        $subject = urlencode("Cotización: {$this->nombre}");
        $body    = urlencode("Te compartimos tu cotización \"{$this->nombre}\".\n\nPuedes consultarla aquí:\n" . $this->public_url);
        return "mailto:{$this->cliente->email}?subject={$subject}&body={$body}";
    }
}
