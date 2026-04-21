<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\FacturaItem;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Mail\FacturaMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = Factura::with(['cliente', 'proyecto'])
            ->latest();

        if ($request->filled('tipo'))    $query->where('tipo', $request->tipo);
        if ($request->filled('estado'))  $query->where('estado', $request->estado);
        if ($request->filled('cliente_id')) $query->where('cliente_id', $request->cliente_id);

        $facturas  = $query->paginate(20)->withQueryString();
        $clientes  = Cliente::orderBy('nombre_empresa')->get();

        // Stats rápidas
        $stats = [
            'total'     => Factura::count(),
            'pendientes'=> Factura::where('estado', 'enviada')->count(),
            'pagadas'   => Factura::where('estado', 'pagada')->count(),
            'monto_mes' => Factura::where('estado', 'pagada')
                                  ->whereMonth('pagada_at', now()->month)
                                  ->sum('total'),
        ];

        return view('admin.facturas.index', compact('facturas', 'clientes', 'stats'));
    }

    public function create()
    {
        $clientes  = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        $proyectos = Proyecto::orderBy('nombre')->get();
        return view('admin.facturas.create', compact('clientes', 'proyectos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo'                => 'required|in:factura,recibo',
            'cliente_id'          => 'required|exists:clientes,id',
            'proyecto_id'         => 'nullable|exists:proyectos,id',
            'fecha_emision'       => 'required|date',
            'fecha_vencimiento'   => 'nullable|date|after_or_equal:fecha_emision',
            'descuento'           => 'nullable|numeric|min:0',
            'impuesto_porcentaje' => 'nullable|numeric|min:0|max:100',
            'moneda'              => 'nullable|string|max:3',
            'metodo_pago'         => 'nullable|string|max:100',
            'notas'               => 'nullable|string|max:2000',
            'condiciones'         => 'nullable|string|max:2000',
            'items'               => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:500',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.unidad'      => 'nullable|string|max:50',
        ]);

        $factura = Factura::create([
            'cliente_id'          => $request->cliente_id,
            'proyecto_id'         => $request->proyecto_id,
            'creado_por'          => auth()->id(),
            'tipo'                => $request->tipo,
            'estado'              => 'borrador',
            'fecha_emision'       => $request->fecha_emision,
            'fecha_vencimiento'   => $request->fecha_vencimiento,
            'descuento'           => $request->descuento ?? 0,
            'impuesto_porcentaje' => $request->impuesto_porcentaje ?? 0,
            'moneda'              => $request->moneda ?? 'MXN',
            'metodo_pago'         => $request->metodo_pago,
            'notas'               => $request->notas,
            'condiciones'         => $request->condiciones,
        ]);

        foreach ($request->items as $i => $item) {
            FacturaItem::create([
                'factura_id'      => $factura->id,
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'unidad'          => $item['unidad'] ?? 'servicio',
                'precio_unitario' => $item['precio_unitario'],
                'orden'           => $i,
            ]);
        }

        $factura->recalcularTotales();

        return redirect()->route('admin.facturas.show', $factura)
            ->with('success', "{$factura->tipo_label} {$factura->folio} creada correctamente.");
    }

    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'proyecto', 'items', 'creadoPor']);
        return view('admin.facturas.show', compact('factura'));
    }

    public function edit(Factura $factura)
    {
        abort_if($factura->estado === 'pagada', 403, 'No se puede editar una factura pagada.');
        $factura->load(['cliente', 'proyecto', 'items']);
        $clientes  = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        $proyectos = Proyecto::orderBy('nombre')->get();
        return view('admin.facturas.edit', compact('factura', 'clientes', 'proyectos'));
    }

    public function update(Request $request, Factura $factura)
    {
        abort_if($factura->estado === 'pagada', 403);

        $request->validate([
            'cliente_id'          => 'required|exists:clientes,id',
            'proyecto_id'         => 'nullable|exists:proyectos,id',
            'fecha_emision'       => 'required|date',
            'fecha_vencimiento'   => 'nullable|date|after_or_equal:fecha_emision',
            'descuento'           => 'nullable|numeric|min:0',
            'impuesto_porcentaje' => 'nullable|numeric|min:0|max:100',
            'moneda'              => 'nullable|string|max:3',
            'metodo_pago'         => 'nullable|string|max:100',
            'notas'               => 'nullable|string|max:2000',
            'condiciones'         => 'nullable|string|max:2000',
            'items'               => 'required|array|min:1',
            'items.*.descripcion' => 'required|string|max:500',
            'items.*.cantidad'    => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.unidad'      => 'nullable|string|max:50',
        ]);

        $factura->update([
            'cliente_id'          => $request->cliente_id,
            'proyecto_id'         => $request->proyecto_id,
            'fecha_emision'       => $request->fecha_emision,
            'fecha_vencimiento'   => $request->fecha_vencimiento,
            'descuento'           => $request->descuento ?? 0,
            'impuesto_porcentaje' => $request->impuesto_porcentaje ?? 0,
            'moneda'              => $request->moneda ?? 'MXN',
            'metodo_pago'         => $request->metodo_pago,
            'notas'               => $request->notas,
            'condiciones'         => $request->condiciones,
        ]);

        // Reemplazar items
        $factura->items()->delete();
        foreach ($request->items as $i => $item) {
            FacturaItem::create([
                'factura_id'      => $factura->id,
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'unidad'          => $item['unidad'] ?? 'servicio',
                'precio_unitario' => $item['precio_unitario'],
                'orden'           => $i,
            ]);
        }

        $factura->recalcularTotales();

        return redirect()->route('admin.facturas.show', $factura)
            ->with('success', 'Documento actualizado correctamente.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return redirect()->route('admin.facturas.index')
            ->with('success', 'Documento eliminado.');
    }

    // ── PDF ───────────────────────────────────────────────────

    public function pdf(Factura $factura)
    {
        $factura->load(['cliente', 'proyecto', 'items', 'creadoPor']);
        $pdf = Pdf::loadView('admin.facturas.pdf', compact('factura'))
                  ->setPaper('letter', 'portrait');

        return $pdf->download($factura->folio . '.pdf');
    }

    // ── Enviar por email ──────────────────────────────────────

    public function enviar(Request $request, Factura $factura)
    {
        $request->validate([
            'email_destino'    => 'required|email',
            'mensaje_personal' => 'nullable|string|max:1000',
        ]);

        $factura->load(['cliente', 'proyecto', 'items', 'creadoPor']);

        try {
            Mail::to($request->email_destino)->send(
                new FacturaMail($factura, $request->mensaje_personal ?? '')
            );

            $factura->update([
                'estado'     => 'enviada',
                'enviada_at' => now(),
            ]);

            return back()->with('success', "📧 {$factura->tipo_label} enviada a {$request->email_destino} correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al enviar: ' . $e->getMessage());
        }
    }

    // ── Marcar pagada ─────────────────────────────────────────

    public function marcarPagada(Request $request, Factura $factura)
    {
        $factura->update([
            'estado'    => 'pagada',
            'pagada_at' => now(),
        ]);

        return back()->with('success', "{$factura->tipo_label} marcada como pagada. ✅");
    }

    // ── Cancelar ──────────────────────────────────────────────

    public function cancelar(Factura $factura)
    {
        $factura->update(['estado' => 'cancelada']);
        return back()->with('success', 'Documento cancelado.');
    }

    // ── Duplicar ──────────────────────────────────────────────

    public function duplicar(Factura $factura)
    {
        $factura->load('items');

        $nueva = $factura->replicate(['folio', 'estado', 'enviada_at', 'pagada_at', 'token_publico']);
        $nueva->estado         = 'borrador';
        $nueva->fecha_emision  = today();
        $nueva->fecha_vencimiento = null;
        $nueva->save(); // folio y token se generan en booted()

        foreach ($factura->items as $item) {
            $nueva->items()->create($item->only([
                'descripcion', 'cantidad', 'unidad', 'precio_unitario', 'subtotal', 'orden'
            ]));
        }

        $nueva->recalcularTotales();

        return redirect()->route('admin.facturas.show', $nueva)
            ->with('success', "Duplicada como {$nueva->folio}. Revisa y edita antes de enviar.");
    }
}
