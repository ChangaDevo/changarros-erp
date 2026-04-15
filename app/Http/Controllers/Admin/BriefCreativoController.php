<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BriefCreativo;
use App\Models\Proyecto;
use App\Models\ActividadLog;
use Illuminate\Http\Request;

class BriefCreativoController extends Controller
{
    public function edit(Proyecto $proyecto)
    {
        $brief = $proyecto->brief ?? new BriefCreativo(['proyecto_id' => $proyecto->id]);
        return view('admin.briefs.edit', compact('proyecto', 'brief'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'objetivo_campana'       => 'nullable|string|max:2000',
            'publico_objetivo'       => 'nullable|string|max:2000',
            'tono_voz'               => 'nullable|string|max:255',
            'colores_marca'          => 'nullable|string|max:255',
            'competencia'            => 'nullable|string|max:2000',
            'referencias'            => 'nullable|string|max:2000',
            'entregables_esperados'  => 'nullable|string|max:2000',
            'presupuesto_referencial'=> 'nullable|numeric|min:0',
            'observaciones'          => 'nullable|string|max:2000',
        ]);

        $esNuevo = is_null($proyecto->brief);

        BriefCreativo::updateOrCreate(
            ['proyecto_id' => $proyecto->id],
            array_merge($validated, [
                'actualizado_por' => auth()->id(),
                'creado_por'      => $esNuevo ? auth()->id() : ($proyecto->brief->creado_por ?? auth()->id()),
            ])
        );

        ActividadLog::registrar(
            $esNuevo ? 'crear_brief' : 'editar_brief',
            "Brief creativo " . ($esNuevo ? 'creado' : 'actualizado') . " para: {$proyecto->nombre}",
            'Proyecto',
            $proyecto->id
        );

        return redirect()->route('admin.proyectos.show', $proyecto)
            ->with('success', 'Brief creativo guardado correctamente.');
    }
}
