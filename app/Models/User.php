<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'cliente_id', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isActivo()
    {
        return (bool) $this->activo;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'superadmin' => 'Super Admin',
            'admin'      => 'Administrador',
            'client'     => 'Cliente',
            default      => ucfirst($this->role),
        };
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function notificacionesNoLeidas()
    {
        return $this->hasMany(Notificacion::class)->whereNull('leida_at');
    }

    public function actividadLog()
    {
        return $this->hasMany(ActividadLog::class);
    }
}
