<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Filtra automáticamente los registros por el usuario autenticado.
     * El superadmin ve todo sin filtro.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Sin sesión activa (comandos artisan, seeders) → sin filtro
        if (!app()->runningInConsole() && auth()->check()) {
            $user = auth()->user();

            // Superadmin ve todo
            if ($user->isSuperAdmin()) {
                return;
            }

            // Columna de ownership (por defecto "creado_por")
            $column = $model->getTable() . '.' . ($model->tenantColumn ?? 'creado_por');
            $builder->where($column, $user->id);
        }
    }
}
