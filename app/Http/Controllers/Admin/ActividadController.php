<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActividadLog;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = ActividadLog::with('usuario')
            ->latest('created_at')->paginate(30);
        return view('admin.actividad.index', compact('actividades'));
    }
}
