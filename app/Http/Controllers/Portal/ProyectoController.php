<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Entrega;
use App\Models\Documento;
use App\Models\Aprobacion;
use App\Models\ActividadLog;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function show(Proyecto $proyecto)
    {
        if ($proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }

        $proyecto->load([
            'documentos' => function ($q) {
                $q->where('visible_cliente', true);
            },
            'entregas.archivos',
            'entregas.comentarios.autor',
            'documentos.comentarios.autor',
            'pagos',
            'brief',
        ]);

        return view('portal.proyectos.show', compact('proyecto'));
    }

    public function aprobarEntrega(Request $request, Entrega $entrega)
    {
        if ($entrega->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        if (!in_array($entrega->estado, ['enviado', 'cambios_solicitados'])) {
            return back()->with('error', 'Esta entrega no puede ser aprobada en su estado actual.');
        }

        $entrega->update(['estado' => 'aprobado', 'notas_cliente' => $request->notas]);

        ActividadLog::registrar('aprobar_entrega', "Entrega aprobada: {$entrega->titulo}", 'Entrega', $entrega->id);
        NotificacionService::entregaAprobada($entrega);

        return back()->with('success', 'Entrega aprobada correctamente.');
    }

    public function rechazarEntrega(Request $request, Entrega $entrega)
    {
        if ($entrega->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        $request->validate(['notas' => 'required|string|min:10']);

        $entrega->update(['estado' => 'cambios_solicitados', 'notas_cliente' => $request->notas]);

        ActividadLog::registrar('rechazar_entrega', "Cambios solicitados en: {$entrega->titulo}", 'Entrega', $entrega->id);
        NotificacionService::entregaRechazada($entrega);

        return back()->with('success', 'Solicitud de cambios enviada.');
    }

    public function aprobarDocumento(Request $request, Documento $documento)
    {
        if ($documento->proyecto->cliente_id !== auth()->user()->cliente_id) {
            abort(403);
        }
        if ($documento->es_sellado) {
            return back()->with('error', 'Este documento ya está sellado.');
        }

        Aprobacion::create([
            'documento_id' => $documento->id,
            'usuario_id' => auth()->id(),
            'accion' => 'aprobado',
            'comentario' => $request->comentario,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $documento->update([
            'estado' => 'sellado',
            'es_sellado' => true,
            'sellado_at' => now(),
            'sellado_por' => auth()->id(),
        ]);

        ActividadLog::registrar('aprobar_documento', "Documento aprobado y sellado: {$documento->nombre}", 'Documento', $documento->id);
        NotificacionService::documentoAprobado($documento);

        return back()->with('success', 'Documento aprobado. Ha sido sellado con fecha y hora de aprobación.');
    }
}
