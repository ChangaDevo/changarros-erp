<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlantillaCotizacion;
use App\Models\PlantillaCotizacionItem;
use Illuminate\Http\Request;

class PlantillaCotizacionController extends Controller
{
    public function index()
    {
        $plantillas = PlantillaCotizacion::withCount('items')->with('creadoPor')->latest()->paginate(20);
        return view('admin.plantillas_cotizacion.index', compact('plantillas'));
    }

    public function create()
    {
        $plantilla = new PlantillaCotizacion();
        return view('admin.plantillas_cotizacion.create', compact('plantilla'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'items'       => 'nullable|array',
            'items.*.descripcion'     => 'required_with:items|string|max:500',
            'items.*.cantidad'        => 'required_with:items|numeric|min:0.01',
            'items.*.precio_unitario' => 'required_with:items|numeric|min:0',
        ]);

        $plantilla = PlantillaCotizacion::create([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'creado_por'  => auth()->id(),
        ]);

        foreach ($request->input('items', []) as $i => $item) {
            PlantillaCotizacionItem::create([
                'plantilla_id'    => $plantilla->id,
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'orden'           => $i,
            ]);
        }

        return redirect()->route('admin.plantillas-cotizacion.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    public function show(PlantillaCotizacion $plantilla)
    {
        return redirect()->route('admin.plantillas-cotizacion.edit', $plantilla);
    }

    public function edit(PlantillaCotizacion $plantilla)
    {
        $plantilla->load('items');
        return view('admin.plantillas_cotizacion.edit', compact('plantilla'));
    }

    public function update(Request $request, PlantillaCotizacion $plantilla)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'items'       => 'nullable|array',
            'items.*.descripcion'     => 'required_with:items|string|max:500',
            'items.*.cantidad'        => 'required_with:items|numeric|min:0.01',
            'items.*.precio_unitario' => 'required_with:items|numeric|min:0',
        ]);

        $plantilla->update([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // Reemplazar todos los ítems
        $plantilla->items()->delete();
        foreach ($request->input('items', []) as $i => $item) {
            PlantillaCotizacionItem::create([
                'plantilla_id'    => $plantilla->id,
                'descripcion'     => $item['descripcion'],
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'orden'           => $i,
            ]);
        }

        return redirect()->route('admin.plantillas-cotizacion.index')
            ->with('success', 'Plantilla actualizada.');
    }

    public function destroy(PlantillaCotizacion $plantilla)
    {
        $plantilla->delete();
        return redirect()->route('admin.plantillas-cotizacion.index')
            ->with('success', 'Plantilla eliminada.');
    }

    // AJAX: retorna los ítems para pre-cargar en el editor de cotizaciones
    public function items(PlantillaCotizacion $plantilla)
    {
        return response()->json($plantilla->items->map(fn($i) => [
            'descripcion'     => $i->descripcion,
            'cantidad'        => (float) $i->cantidad,
            'precio_unitario' => (float) $i->precio_unitario,
        ]));
    }
}
