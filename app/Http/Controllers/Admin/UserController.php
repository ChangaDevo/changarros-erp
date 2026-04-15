<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use App\Models\ActividadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('cliente')->latest()->paginate(20);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.usuarios.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => ['required', Rule::in(['superadmin', 'admin', 'client'])],
            'cliente_id' => 'nullable|exists:clientes,id|required_if:role,client',
            'password'   => 'required|string|min:8|confirmed',
            'activo'     => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'cliente_id' => $validated['role'] === 'client' ? $validated['cliente_id'] : null,
            'password'   => Hash::make($validated['password']),
            'activo'     => $request->boolean('activo', true),
        ]);

        ActividadLog::registrar('crear_usuario', "Usuario creado: {$user->email} (rol: {$user->role})", 'User', $user->id);

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function edit(User $usuario)
    {
        $clientes = Cliente::where('activo', true)->orderBy('nombre_empresa')->get();
        return view('admin.usuarios.edit', compact('usuario', 'clientes'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role'       => ['required', Rule::in(['superadmin', 'admin', 'client'])],
            'cliente_id' => 'nullable|exists:clientes,id|required_if:role,client',
            'activo'     => 'nullable|boolean',
            'password'   => 'nullable|string|min:8|confirmed',
        ]);

        // No permitir quitarle el rol superadmin al único superadmin existente
        if ($usuario->isSuperAdmin() && $validated['role'] !== 'superadmin') {
            $totalSuperadmins = User::where('role', 'superadmin')->count();
            if ($totalSuperadmins <= 1) {
                return back()->withErrors(['role' => 'No puedes cambiar el rol del único Super Administrador.']);
            }
        }

        $data = [
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'cliente_id' => $validated['role'] === 'client' ? $validated['cliente_id'] : null,
            'activo'     => $request->boolean('activo'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $usuario->update($data);

        ActividadLog::registrar('editar_usuario', "Usuario editado: {$usuario->email}", 'User', $usuario->id);

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Usuario {$usuario->name} actualizado correctamente.");
    }

    public function destroy(User $usuario)
    {
        // No borrar el propio usuario
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // No borrar el último superadmin
        if ($usuario->isSuperAdmin() && User::where('role', 'superadmin')->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el único Super Administrador.');
        }

        ActividadLog::registrar('eliminar_usuario', "Usuario eliminado: {$usuario->email}", 'User', $usuario->id);
        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
