<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    protected $fillable = [
        'comentable_type',
        'comentable_id',
        'user_id',
        'contenido',
    ];

    public function comentable()
    {
        return $this->morphTo();
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
