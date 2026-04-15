<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActividadLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin() && Auth::user()->isActivo()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar login como superadmin primero, luego como admin
        $loginedAsSuperadmin = Auth::attempt(array_merge($credentials, ['role' => 'superadmin', 'activo' => 1]), $request->boolean('remember'));
        if (!$loginedAsSuperadmin) {
            Auth::attempt(array_merge($credentials, ['role' => 'admin', 'activo' => 1]), $request->boolean('remember'));
        }
        if (Auth::check() && Auth::user()->isAdmin()) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended');
            ActividadLog::registrar('login', 'Inicio de sesión en panel admin');
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        ActividadLog::registrar('logout', 'Cierre de sesión');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
