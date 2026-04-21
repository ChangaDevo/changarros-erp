<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToTenant;

class Cliente extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'nombre_empresa',
        'nombre_contacto',
        'email',
        'telefono',
        'rfc',
        'direccion',
        'notas',
        'activo',
        'es_cliente_interno',
        'dias_minimos_publicacion',
        'creado_por',
    ];

    protected $casts = [
        'activo'              => 'boolean',
        'es_cliente_interno'  => 'boolean',
        'dias_minimos_publicacion' => 'integer',
    ];

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function notificacionesNoLeidas()
    {
        return $this->hasMany(Notificacion::class)->whereNull('leida_at');
    }

    public function proyectosActivos()
    {
        return $this->hasMany(Proyecto::class)->whereNotIn('estado', ['finalizado']);
    }
}
