<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('admin.perfil.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('admin.perfil.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono'     => 'nullable|string|max:30',
            'cargo'        => 'nullable|string|max:100',
            'bio'          => 'nullable|string|max:500',
            'foto_perfil'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Subir nueva foto
        if ($request->hasFile('foto_perfil')) {
            // Borrar foto anterior
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $validated['foto_perfil'] = $request->file('foto_perfil')
                ->store('perfiles', 'public');
        } else {
            unset($validated['foto_perfil']);
        }

        $user->update($validated);

        return redirect()->route('admin.perfil.show')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_actual'  => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña cambiada correctamente.');
    }

    public function destroyFoto()
    {
        $user = auth()->user();

        if ($user->foto_perfil) {
            Storage::disk('public')->delete($user->foto_perfil);
            $user->update(['foto_perfil' => null]);
        }

        return back()->with('success', 'Foto de perfil eliminada.');
    }
}
