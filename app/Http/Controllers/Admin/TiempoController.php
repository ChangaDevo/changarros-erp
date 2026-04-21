<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TiempoRegistro;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TiempoController extends Controller
{
    public function index(Request $request)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        $query = TiempoRegistro::with(['proyecto.cliente', 'user'])
            ->whereHas('proyecto', fn($q) => $q->withoutGlobalScopes()) // visibilidad ya filtrada abajo
            ->join('proyectos', 'tiempo_registros.proyecto_id', '=', 'proyectos.id');

        // Filtro por tenant (admin solo ve sus proyectos)
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('proyectos.creado_por', auth()->id());
        }
        $query->select('tiempo_registros.*');

        // Filtros
        if ($request->filled('proyecto_id')) {
            $query->where('tiempo_registros.proyecto_id', $request->proyecto_id);
        }
        if ($request->filled('tipo')) {
            $query->where('tiempo_registros.tipo', $request->tipo);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('tiempo_registros.fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('tiempo_registros.fecha', '<=', $request->fecha_hasta);
        }

        $registros     = $query->orderByDesc('tiempo_registros.fecha')
                               ->orderByDesc('tiempo_registros.id')
                               ->paginate(25)->withQueryString();

        $totalMinutos  = $query->sum('tiempo_registros.minutos');
        $totalHoy      = TiempoRegistro::whereDate('fecha', today())
                            ->where('user_id', auth()->id())
                            ->sum('minutos');

        // Timer activo del usuario
        $timerActivo = TiempoRegistro::where('user_id', auth()->id())
                            ->whereNotNull('timer_inicio')
                            ->with('proyecto')
                            ->first();

        return view('admin.tiempo.index', compact(
            'registros', 'proyectos', 'totalMinutos', 'totalHoy', 'timerActivo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'tarea'       => 'required|string|max:255',
            'tipo'        => 'required|in:diseño,redaccion,reunion,revision,desarrollo,administracion,otro',
            'horas'       => 'required|integer|min:0|max:23',
            'minutos_entrada' => 'required|integer|min:0|max:59',
            'fecha'       => 'required|date',
            'facturable'  => 'nullable|boolean',
            'notas'       => 'nullable|string|max:1000',
        ]);

        $minutos = ((int)$request->horas * 60) + (int)$request->minutos_entrada;

        if ($minutos <= 0) {
            return back()->withErrors(['minutos_entrada' => 'Debes registrar al menos 1 minuto.'])->withInput();
        }

        TiempoRegistro::create([
            'proyecto_id' => $request->proyecto_id,
            'user_id'     => auth()->id(),
            'tarea'       => $request->tarea,
            'tipo'        => $request->tipo,
            'minutos'     => $minutos,
            'fecha'       => $request->fecha,
            'facturable'  => $request->boolean('facturable', true),
            'notas'       => $request->notas,
        ]);

        return back()->with('success', 'Tiempo registrado correctamente.');
    }

    public function destroy(TiempoRegistro $registro)
    {
        $registro->delete();
        return back()->with('success', 'Registro eliminado.');
    }

    // ── Timer en vivo ─────────────────────────────────────────

    public function iniciarTimer(Request $request)
    {
        $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'tarea'       => 'required|string|max:255',
            'tipo'        => 'required|in:diseño,redaccion,reunion,revision,desarrollo,administracion,otro',
        ]);

        // Si ya hay un timer activo, detenerlo primero
        $activo = TiempoRegistro::where('user_id', auth()->id())
                                ->whereNotNull('timer_inicio')
                                ->first();
        if ($activo) {
            $this->_guardarTimer($activo);
        }

        $registro = TiempoRegistro::create([
            'proyecto_id'  => $request->proyecto_id,
            'user_id'      => auth()->id(),
            'tarea'        => $request->tarea,
            'tipo'         => $request->tipo,
            'minutos'      => 0,
            'fecha'        => today(),
            'facturable'   => true,
            'timer_inicio' => now(),
        ]);

        return response()->json(['ok' => true, 'registro_id' => $registro->id, 'inicio' => now()->toISOString()]);
    }

    public function detenerTimer(Request $request)
    {
        $registro = TiempoRegistro::where('user_id', auth()->id())
                                  ->whereNotNull('timer_inicio')
                                  ->first();

        if (!$registro) {
            return response()->json(['ok' => false, 'message' => 'No hay timer activo.'], 404);
        }

        $minutos = $this->_guardarTimer($registro);

        return response()->json(['ok' => true, 'minutos' => $minutos, 'duracion' => $registro->fresh()->duracion_formateada]);
    }

    private function _guardarTimer(TiempoRegistro $registro): int
    {
        $minutos = (int) now()->diffInMinutes($registro->timer_inicio);
        $minutos = max(1, $minutos);
        $registro->update(['minutos' => $minutos, 'timer_inicio' => null]);
        return $minutos;
    }

    // ── Por proyecto (para la vista show del proyecto) ────────

    public function porProyecto(Proyecto $proyecto)
    {
        $registros = $proyecto->tiempos()
                              ->with('user')
                              ->orderByDesc('fecha')
                              ->get();

        $totalMinutos = $registros->sum('minutos');

        return response()->json([
            'registros'    => $registros,
            'total_minutos'=> $totalMinutos,
            'total_horas'  => round($totalMinutos / 60, 2),
        ]);
    }
}
