<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActividadLog;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\Entrega;
use App\Models\Pago;
use App\Models\Proyecto;
use App\Models\Publicacion;

class DashboardController extends Controller
{
    public function index()
    {
        // IDs de proyectos del tenant actual (respeta el scope automáticamente)
        $proyectoIds = Proyecto::pluck('id');

        // ---- Stats ----
        $stats = [
            'clientes_activos'       => Cliente::where('activo', true)->count(),
            'proyectos_activos'      => Proyecto::whereNotIn('estado', ['finalizado', 'cancelado'])->count(),
            'pagos_pendientes'       => Pago::whereIn('proyecto_id', $proyectoIds)->where('estado', 'pendiente')->count(),
            'entregas_por_aprobar'   => Entrega::whereIn('proyecto_id', $proyectoIds)->where('estado', 'enviado')->count(),
            'cotizaciones_en_espera' => Cotizacion::whereIn('estado', ['enviada', 'vista'])->count(),
            'posts_por_aprobar'      => Publicacion::where('estado', 'propuesto')->count(),
            'posts_con_error'        => Publicacion::where('estado', 'error')->count(),
            'posts_en_cola'          => Publicacion::where('estado', 'aprobado')
                                            ->where('fecha_programada', '>', now())->count(),
        ];

        // ---- Proyectos agrupados por cliente ----
        $proyectos_por_cliente = Cliente::where('activo', true)
            ->with(['proyectos' => function ($q) {
                $q->withCount(['entregas', 'pagos'])
                  ->with('cotizaciones')
                  ->whereNotIn('estado', ['finalizado'])
                  ->latest();
            }])
            ->whereHas('proyectos', fn($q) => $q->whereNotIn('estado', ['finalizado']))
            ->orderBy('nombre_empresa')
            ->get();

        // ---- Tablas ----
        $proyectos_recientes = Proyecto::with('cliente')
            ->latest()->limit(5)->get();

        $pagos_pendientes = Pago::with('proyecto.cliente')
            ->whereIn('proyecto_id', $proyectoIds)
            ->where('estado', 'pendiente')
            ->orderBy('fecha_vencimiento')
            ->limit(5)->get();

        $entregas_pendientes = Entrega::with('proyecto.cliente')
            ->whereIn('proyecto_id', $proyectoIds)
            ->where('estado', 'enviado')
            ->latest()->limit(5)->get();

        $cotizaciones_recientes = Cotizacion::with('cliente')
            ->latest()->limit(5)->get();

        $posts_pendientes = Publicacion::with('cliente')
            ->where('estado', 'propuesto')
            ->orderBy('fecha_programada')
            ->limit(5)->get();

        $posts_con_error = Publicacion::with('cliente')
            ->where('estado', 'error')
            ->latest()->limit(5)->get();

        $actividad_reciente = ActividadLog::with('usuario')
            ->latest()->limit(8)->get();

        return view('admin.dashboard', compact(
            'stats',
            'proyectos_por_cliente',
            'proyectos_recientes',
            'pagos_pendientes',
            'entregas_pendientes',
            'cotizaciones_recientes',
            'posts_pendientes',
            'posts_con_error',
            'actividad_reciente'
        ));
    }
}
