<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Traits\BelongsToTenant;

class Marca extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'cliente_id', 'creado_por', 'nombre', 'descripcion',
        'tagline', 'sitio_web', 'industria',
        'acceso_cliente', 'token_publico',
    ];

    protected $casts = [
        'acceso_cliente' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($marca) {
            if (empty($marca->token_publico)) {
                $marca->token_publico = Str::random(40);
            }
        });
    }

    public function cliente()   { return $this->belongsTo(Cliente::class); }
    public function creadoPor() { return $this->belongsTo(User::class, 'creado_por'); }
    public function recursos()  { return $this->hasMany(MarcaRecurso::class)->orderBy('orden'); }

    // Shortcuts por tipo
    public function logos()       { return $this->recursos()->where('tipo', 'logo'); }
    public function tipografias() { return $this->recursos()->where('tipo', 'tipografia'); }
    public function colores()     { return $this->recursos()->where('tipo', 'color'); }
    public function templates()   { return $this->recursos()->where('tipo', 'template'); }
    public function otros()       { return $this->recursos()->where('tipo', 'otro'); }

    public function getLinkPublicoAttribute(): string
    {
        return route('marca.publica', $this->token_publico);
    }

    // Primer logo para thumbnail
    public function getLogoThumbAttribute(): ?string
    {
        $logo = $this->logos()->whereNotNull('archivo_path')->first();
        return $logo ? \Storage::url($logo->archivo_path) : null;
    }
}
