<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BriefCreativo extends Model
{
    protected $table = 'briefs_creativos';

    protected $fillable = [
        'proyecto_id',
        'objetivo_campana',
        'publico_objetivo',
        'tono_voz',
        'colores_marca',
        'competencia',
        'referencias',
        'entregables_esperados',
        'presupuesto_referencial',
        'observaciones',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'presupuesto_referencial' => 'decimal:2',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function camposLlenos(): int
    {
        $campos = [
            'objetivo_campana', 'publico_objetivo', 'tono_voz', 'colores_marca',
            'competencia', 'referencias', 'entregables_esperados', 'observaciones',
        ];
        return collect($campos)->filter(fn($c) => !empty($this->$c))->count();
    }

    public function totalCampos(): int
    {
        return 8;
    }
}
