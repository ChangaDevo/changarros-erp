<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadLog extends Model
{
    public $timestamps = false;

    protected $table = 'actividad_log';

    protected $fillable = [
        'user_id',
        'accion',
        'modelo_tipo',
        'modelo_id',
        'descripcion',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function registrar($accion, $descripcion, $modeloTipo = null, $modeloId = null)
    {
        static::create([
            'user_id' => auth()->id(),
            'accion' => $accion,
            'modelo_tipo' => $modeloTipo,
            'modelo_id' => $modeloId,
            'descripcion' => $descripcion,
            'ip_address' => request()->ip(),
        ]);
    }
}
