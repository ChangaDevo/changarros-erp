<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\ActividadLog;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class CotizacionPublicaController extends Controller
{
    public function show(string $token)
    {
        $cotizacion = Cotizacion::where('token', $token)
            ->with(['cliente', 'items'])
            ->firstOrFail();

        // Marcar como vista si estaba enviada
        if ($cotizacion->estado === 'enviada') {
            $cotizacion->update(['estado' => 'vista', 'visto_at' => now()]);
        }

        return view('public.cotizacion.show', compact('cotizacion'));
    }

    public function aprobar(Request $request, string $token)
    {
        $cotizacion = Cotizacion::where('token', $token)->firstOrFail();

        if (!in_array($cotizacion->estado, ['enviada', 'vista'])) {
            return back()->with('error', 'Esta cotización no puede ser aprobada en su estado actual.');
        }

        $cotizacion->update([
            'estado'          => 'aprobada',
            'aprobado_at'     => now(),
            'aprobado_ip'     => $request->ip(),
            'aprobado_nombre' => $request->input('nombre', 'Cliente'),
        ]);

        ActividadLog::registrar(
            'aprobar_cotizacion',
            "Cotización aprobada vía portal público: {$cotizacion->nombre}",
            'Cotizacion',
            $cotizacion->id
        );
        NotificacionService::cotizacionAprobada($cotizacion);

        return redirect()->route('cotizacion.publica', $token)
            ->with('success', '¡Cotización aprobada! El equipo de Changarrito se pondrá en contacto contigo pronto.');
    }

    public function rechazar(Request $request, string $token)
    {
        $cotizacion = Cotizacion::where('token', $token)->firstOrFail();

        if (!in_array($cotizacion->estado, ['enviada', 'vista'])) {
            return back()->with('error', 'Esta cotización no puede ser rechazada en su estado actual.');
        }

        $request->validate(['razon' => 'required|string|min:5']);

        $cotizacion->update([
            'estado'        => 'rechazada',
            'rechazado_at'  => now(),
            'razon_rechazo' => $request->razon,
        ]);

        ActividadLog::registrar(
            'rechazar_cotizacion',
            "Cotización rechazada: {$cotizacion->nombre}",
            'Cotizacion',
            $cotizacion->id
        );
        NotificacionService::cotizacionRechazada($cotizacion);

        return redirect()->route('cotizacion.publica', $token)
            ->with('info', 'Cotización rechazada. Hemos notificado al equipo de Changarrito.');
    }
}
