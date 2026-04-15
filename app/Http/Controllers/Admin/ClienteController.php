<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use App\Models\ActividadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('proyectos')->latest()->paginate(15);
        return view('admin.clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_empresa'          => 'required|string|max:255',
            'nombre_contacto'         => 'required|string|max:255',
            'email'                   => 'required|email|unique:clientes',
            'telefono'                => 'nullable|string|max:20',
            'rfc'                     => 'nullable|string|max:13',
            'direccion'               => 'nullable|string',
            'notas'                   => 'nullable|string',
            'crear_acceso'            => 'nullable|boolean',
            'password'                => 'required_if:crear_acceso,1|nullable|min:8|confirmed',
            'es_cliente_interno'      => 'nullable|boolean',
            'dias_minimos_publicacion'=> 'nullable|integer|min:0|max:30',
        ]);

        $validated['es_cliente_interno']       = $request->boolean('es_cliente_interno');
        $validated['dias_minimos_publicacion']  = $validated['dias_minimos_publicacion'] ?? 2;

        $cliente = Cliente::create($validated);

        if ($request->boolean('crear_acceso')) {
            User::create([
                'name' => $cliente->nombre_contacto,
                'email' => $cliente->email,
                'password' => Hash::make($request->password),
                'role' => 'client',
                'cliente_id' => $cliente->id,
            ]);
        }

        ActividadLog::registrar('crear_cliente', "Cliente creado: {$cliente->nombre_empresa}", 'Cliente', $cliente->id);

        return redirect()->route('admin.clientes.show', $cliente)
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['proyectos.documentos', 'proyectos.pagos', 'proyectos.entregas', 'usuarios']);
        return view('admin.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('admin.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre_empresa'           => 'required|string|max:255',
            'nombre_contacto'          => 'required|string|max:255',
            'email'                    => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefono'                 => 'nullable|string|max:20',
            'rfc'                      => 'nullable|string|max:13',
            'direccion'                => 'nullable|string',
            'notas'                    => 'nullable|string',
            'activo'                   => 'nullable|boolean',
            'es_cliente_interno'       => 'nullable|boolean',
            'dias_minimos_publicacion' => 'nullable|integer|min:0|max:30',
        ]);

        $validated['activo']                  = $request->boolean('activo');
        $validated['es_cliente_interno']      = $request->boolean('es_cliente_interno');
        $validated['dias_minimos_publicacion'] = $validated['dias_minimos_publicacion'] ?? 2;

        $cliente->update($validated);
        ActividadLog::registrar('editar_cliente', "Cliente editado: {$cliente->nombre_empresa}", 'Cliente', $cliente->id);

        return redirect()->route('admin.clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        ActividadLog::registrar('eliminar_cliente', "Cliente eliminado: {$cliente->nombre_empresa}");
        $cliente->delete();
        return redirect()->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado.');
    }
}
