<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\ActividadLog;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    public function index()
    {
        $cotizaciones = Cotizacion::with('cliente')
            ->latest()->paginate(20);

        $totales = [
            'borrador'  => Cotizacion::where('estado', 'borrador')->count(),
            'enviada'   => Cotizacion::where('estado', 'enviada')->count(),
            'aprobada'  => Cotizacion::where('estado', 'aprobada')->count(),
            'rechazada' => Cotizacion::where('estado', 'rechazada')->count(),
        ];

        return view('admin.cotizaciones.index', compact('cotizaciones', 'totales'));
    }

    public function create()
    {
        $clientes    = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        $preCliente  = request('cliente_id');
        $preProyecto = request('proyecto_id');
        $proyectos   = $preCliente
            ? Proyecto::where('cliente_id', $preCliente)->get()
            : collect();
        return view('admin.cotizaciones.create', compact('clientes', 'proyectos', 'preCliente', 'preProyecto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'proyecto_id'       => 'nullable|exists:proyectos,id',
            'nombre'            => 'required|string|max:255',
            'iva_porcentaje'    => 'required|numeric|min:0|max:100',
            'notas'             => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $validated['creado_por'] = auth()->id();
        $validated['estado']     = 'borrador';

        $cotizacion = Cotizacion::create($validated);

        ActividadLog::registrar('crear_cotizacion', "Cotización creada: {$cotizacion->nombre}", 'Cotizacion', $cotizacion->id);

        return redirect()->route('admin.cotizaciones.edit', $cotizacion)
            ->with('success', 'Cotización creada. Agrega los servicios.');
    }

    public function show(Cotizacion $cotizacion)
    {
        return redirect()->route('admin.cotizaciones.edit', $cotizacion);
    }

    public function edit(Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'proyecto', 'items']);
        $clientes   = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        $proyectos  = $cotizacion->cliente_id
            ? Proyecto::where('cliente_id', $cotizacion->cliente_id)->get()
            : collect();
        $plantillas = \App\Models\PlantillaCotizacion::orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.cotizaciones.editor', compact('cotizacion', 'clientes', 'proyectos', 'plantillas'));
    }

    public function update(Request $request, Cotizacion $cotizacion)
    {
        $validated = $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'proyecto_id'       => 'nullable|exists:proyectos,id',
            'nombre'            => 'required|string|max:255',
            'estado'            => 'required|in:borrador,enviada,vista,aprobada,rechazada,vencida',
            'iva_porcentaje'    => 'required|numeric|min:0|max:100',
            'notas'             => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
        ]);

        $cotizacion->update($validated);
        $cotizacion->recalcular();

        ActividadLog::registrar('editar_cotizacion', "Cotización editada: {$cotizacion->nombre}", 'Cotizacion', $cotizacion->id);

        return redirect()->route('admin.cotizaciones.edit', $cotizacion)
            ->with('success', 'Cotización guardada.');
    }

    public function destroy(Cotizacion $cotizacion)
    {
        ActividadLog::registrar('eliminar_cotizacion', "Cotización eliminada: {$cotizacion->nombre}");
        $cotizacion->delete();
        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización eliminada.');
    }

    // ── Items (AJAX) ──────────────────────────────────────────────────

    public function storeItem(Request $request, Cotizacion $cotizacion)
    {
        $data = $request->validate([
            'descripcion'     => 'required|string|max:255',
            'cantidad'        => 'required|numeric|min:0.01',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $data['total'] = $data['cantidad'] * $data['precio_unitario'];
        $data['orden'] = $cotizacion->items()->max('orden') + 1;

        $item = $cotizacion->items()->create($data);
        $cotizacion->recalcular();
        $cotizacion->refresh();

        return response()->json([
            'item'      => $item,
            'subtotal'  => $cotizacion->subtotal,
            'iva_monto' => $cotizacion->iva_monto,
            'total'     => $cotizacion->total,
        ]);
    }

    public function destroyItem(Cotizacion $cotizacion, CotizacionItem $item)
    {
        abort_if($item->cotizacion_id !== $cotizacion->id, 403);
        $item->delete();
        $cotizacion->recalcular();
        $cotizacion->refresh();

        return response()->json([
            'subtotal'  => $cotizacion->subtotal,
            'iva_monto' => $cotizacion->iva_monto,
            'total'     => $cotizacion->total,
        ]);
    }

    public function proyectosPorCliente(Cliente $cliente)
    {
        return response()->json($cliente->proyectos()->select('id', 'nombre')->get());
    }
}
