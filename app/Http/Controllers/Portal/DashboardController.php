<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Entrega;
use App\Models\Pago;

class DashboardController extends Controller
{
    public function manual()
    {
        return view('portal.manual');
    }

    public function index()
    {
        $cliente = auth()->user()->cliente;

        if (!$cliente) {
            return view('portal.dashboard', ['cliente' => null, 'proyectos' => collect(), 'stats' => [], 'entregas_recientes' => collect(), 'pagos_pendientes' => collect()]);
        }

        $proyectos = $cliente->proyectos()->with(['entregas', 'documentos', 'pagos'])->get();

        $stats = [
            'proyectos_activos' => $proyectos->whereNotIn('estado', ['finalizado'])->count(),
            'entregas_pendientes' => Entrega::whereIn('proyecto_id', $proyectos->pluck('id'))->where('estado', 'enviado')->count(),
            'pagos_pendientes' => Pago::whereIn('proyecto_id', $proyectos->pluck('id'))->where('estado', 'pendiente')->count(),
        ];

        $entregas_recientes = Entrega::whereIn('proyecto_id', $proyectos->pluck('id'))
            ->with('proyecto')->where('estado', 'enviado')->latest()->limit(5)->get();

        $pagos_pendientes = Pago::whereIn('proyecto_id', $proyectos->pluck('id'))
            ->with('proyecto')->where('estado', 'pendiente')->orderBy('fecha_vencimiento')->limit(5)->get();

        return view('portal.dashboard', compact('cliente', 'proyectos', 'stats', 'entregas_recientes', 'pagos_pendientes'));
    }
}
