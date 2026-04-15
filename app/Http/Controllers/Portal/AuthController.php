<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ActividadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isClient()) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(array_merge($credentials, ['role' => 'client']), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended'); // evitar redirect a rutas de admin
            ActividadLog::registrar('login_portal', 'Acceso al portal de cliente');
            return redirect()->route('portal.dashboard');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.login');
    }
}
