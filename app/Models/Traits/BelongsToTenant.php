<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * Registra el scope global al arrancar el modelo.
     */
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        // Al crear, asignar automáticamente el usuario actual como dueño
        static::creating(function ($model) {
            $col = $model->tenantColumn ?? 'creado_por';
            if (empty($model->{$col}) && !app()->runningInConsole() && auth()->check()) {
                $model->{$col} = auth()->id();
            }
        });
    }

    /**
     * Omitir el filtro de tenant (útil para el superadmin o relaciones internas).
     */
    public static function sinFiltroTenant(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}
