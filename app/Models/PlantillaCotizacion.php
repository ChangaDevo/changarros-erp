<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToTenant;

class PlantillaCotizacion extends Model
{
    use BelongsToTenant;
    protected $table = 'plantillas_cotizacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'creado_por',
    ];

    public function items()
    {
        return $this->hasMany(PlantillaCotizacionItem::class, 'plantilla_id')->orderBy('orden');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
