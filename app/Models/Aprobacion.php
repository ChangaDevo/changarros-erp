<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    protected $table = 'aprobaciones';
    public $timestamps = false;

    protected $fillable = [
        'documento_id',
        'usuario_id',
        'accion',
        'comentario',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
