<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Pago;

class PagoController extends Controller
{
    public function index()
    {
        $cliente = auth()->user()->cliente;
        $pagos = Pago::whereHas('proyecto', fn($q) => $q->where('cliente_id', $cliente->id))
            ->with('proyecto')->orderBy('fecha_vencimiento')->paginate(20);
        $totales = [
            'pendiente' => Pago::whereHas('proyecto', fn($q) => $q->where('cliente_id', $cliente->id))->where('estado', 'pendiente')->sum('monto'),
            'pagado' => Pago::whereHas('proyecto', fn($q) => $q->where('cliente_id', $cliente->id))->where('estado', 'pagado')->sum('monto'),
        ];
        return view('portal.pagos.index', compact('pagos', 'totales'));
    }
}
