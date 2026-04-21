<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Cliente;
use App\Models\ActividadLog;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        $clientes_con_proyectos = Cliente::with(['proyectos' => function ($q) {
            $q->with(['cotizaciones', 'usuariosCompartidos'])
              ->withCount(['entregas', 'pagos'])
              ->latest();
        }])
        ->whereHas('proyectos')
        ->orderBy('nombre_empresa')
        ->get();

        return view('admin.proyectos.index', compact('clientes_con_proyectos'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.proyectos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'             => 'required|exists:clientes,id',
            'nombre'                 => 'required|string|max:255',
            'descripcion'            => 'nullable|string',
            'estado'                 => 'required|in:cotizando,en_desarrollo,en_revision,aprobado,finalizado',
            'monto_total'            => 'nullable|numeric|min:0',
            'horas_estimadas'        => 'nullable|numeric|min:0',
            'tarifa_hora'            => 'nullable|numeric|min:0',
            'fecha_inicio'           => 'nullable|date',
            'fecha_entrega_estimada' => 'nullable|date',
            'notas'                  => 'nullable|string',
        ]);

        $validated['creado_por'] = auth()->id();
        $proyecto = Proyecto::create($validated);

        ActividadLog::registrar('crear_proyecto', "Proyecto creado: {$proyecto->nombre}", 'Proyecto', $proyecto->id);

        return redirect()->route('admin.proyectos.show', $proyecto)
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto)
    {
        $proyecto->load([
            'cliente',
            'documentos.subidoPor',
            'documentos.comentarios.autor',
            'entregas.archivos',
            'entregas.comentarios.autor',
            'pagos',
            'creadoPor',
            'cotizaciones.items',
            'usuariosCompartidos',
            'brief.actualizadoPor',
        ]);

        // Usuarios admin disponibles para compartir (excluye los ya compartidos y el creador)
        $admins_disponibles = \App\Models\User::whereIn('role', ['admin', 'superadmin'])
            ->where('activo', 1)
            ->whereNotIn('id', $proyecto->usuariosCompartidos->pluck('id'))
            ->where('id', '!=', $proyecto->creado_por)
            ->orderBy('name')
            ->get();

        return view('admin.proyectos.show', compact('proyecto', 'admins_disponibles'));
    }

    public function compartirUsuario(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rol'     => 'required|in:colaborador,editor',
        ]);

        $proyecto->usuariosCompartidos()->syncWithoutDetaching([
            $request->user_id => ['rol' => $request->rol],
        ]);

        ActividadLog::registrar('compartir_proyecto', "Proyecto compartido con usuario ID {$request->user_id}", 'Proyecto', $proyecto->id);

        return back()->with('success', 'Acceso otorgado correctamente.');
    }

    public function quitarUsuario(Proyecto $proyecto, \App\Models\User $usuario)
    {
        $proyecto->usuariosCompartidos()->detach($usuario->id);
        ActividadLog::registrar('quitar_acceso_proyecto', "Acceso removido al usuario {$usuario->name}", 'Proyecto', $proyecto->id);
        return back()->with('success', 'Acceso removido.');
    }

    public function edit(Proyecto $proyecto)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.proyectos.edit', compact('proyecto', 'clientes'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $validated = $request->validate([
            'cliente_id'             => 'required|exists:clientes,id',
            'nombre'                 => 'required|string|max:255',
            'descripcion'            => 'nullable|string',
            'estado'                 => 'required|in:cotizando,en_desarrollo,en_revision,aprobado,finalizado',
            'monto_total'            => 'nullable|numeric|min:0',
            'horas_estimadas'        => 'nullable|numeric|min:0',
            'tarifa_hora'            => 'nullable|numeric|min:0',
            'fecha_inicio'           => 'nullable|date',
            'fecha_entrega_estimada' => 'nullable|date',
            'fecha_entrega_real'     => 'nullable|date',
            'notas'                  => 'nullable|string',
            'carpeta_drive'          => 'nullable|url|max:500',
        ]);

        $proyecto->update($validated);
        ActividadLog::registrar('editar_proyecto', "Proyecto editado: {$proyecto->nombre}", 'Proyecto', $proyecto->id);

        return redirect()->route('admin.proyectos.show', $proyecto)
            ->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Proyecto $proyecto)
    {
        ActividadLog::registrar('eliminar_proyecto', "Proyecto eliminado: {$proyecto->nombre}");
        $proyecto->delete();
        return redirect()->route('admin.proyectos.index')
            ->with('success', 'Proyecto eliminado.');
    }
}
