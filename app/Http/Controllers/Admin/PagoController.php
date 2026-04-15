<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Proyecto;
use App\Models\ActividadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('proyecto.cliente')
            ->latest()->paginate(20);
        $totales = [
            'pendiente' => Pago::where('estado', 'pendiente')->sum('monto'),
            'pagado' => Pago::where('estado', 'pagado')->sum('monto'),
            'vencido' => Pago::where('estado', 'vencido')->sum('monto'),
        ];
        return view('admin.pagos.index', compact('pagos', 'totales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'notas' => 'nullable|string',
        ]);

        $pago = Pago::create($request->only('proyecto_id', 'entrega_id', 'concepto', 'monto', 'fecha_vencimiento', 'notas'));

        ActividadLog::registrar('crear_pago', "Pago generado: {$pago->concepto} - \${$pago->monto}", 'Pago', $pago->id);

        return redirect()->back()->with('success', 'Cobro generado correctamente.');
    }

    public function marcarPagado(Request $request, Pago $pago)
    {
        $request->validate([
            'metodo_pago' => 'required|string',
            'comprobante' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $data = [
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'metodo_pago' => $request->metodo_pago,
        ];

        if ($request->hasFile('comprobante')) {
            $data['comprobante_path'] = $request->file('comprobante')->store("proyectos/{$pago->proyecto_id}/comprobantes", 'local');
        }

        $pago->update($data);
        ActividadLog::registrar('confirmar_pago', "Pago confirmado: {$pago->concepto}", 'Pago', $pago->id);

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    public function generarQR(Pago $pago)
    {
        $referencia = 'CODI-' . str_pad($pago->id, 8, '0', STR_PAD_LEFT) . '-' . strtoupper(substr(md5($pago->id . $pago->monto), 0, 6));
        $pago->update(['referencia_codi' => $referencia]);

        ActividadLog::registrar('generar_qr', "QR CoDi generado para pago: {$pago->concepto}", 'Pago', $pago->id);

        return response()->json(['referencia' => $referencia, 'monto' => $pago->monto]);
    }
}
