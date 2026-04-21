<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\TiempoRegistro;
use App\Models\Pago;
use Illuminate\Http\Request;

class RentabilidadController extends Controller
{
    public function index(Request $request)
    {
        $anio = (int) $request->get('anio', now()->year);
        $mes  = $request->get('mes'); // null = todo el año

        // Proyectos con tiempo registrado (ya filtrados por tenant vía GlobalScope)
        $proyectos = Proyecto::with(['cliente', 'tiempos', 'pagos'])
            ->whereHas('tiempos')
            ->when($mes, fn($q) =>
                $q->whereHas('tiempos', fn($t) =>
                    $t->whereYear('fecha', $anio)->whereMonth('fecha', $mes)
                )
            )
            ->when(!$mes, fn($q) =>
                $q->whereHas('tiempos', fn($t) =>
                    $t->whereYear('fecha', $anio)
                )
            )
            ->get()
            ->map(function ($p) use ($anio, $mes) {
                // Horas en el período
                $tiempoQ = $p->tiempos()->whereYear('fecha', $anio);
                if ($mes) $tiempoQ->whereMonth('fecha', $mes);

                $minutos        = $tiempoQ->sum('minutos');
                $horas          = round($minutos / 60, 2);
                $ingresos       = (float) $p->pagos()->where('estado', 'pagado')->sum('monto');
                $costoHoras     = $p->tarifa_hora ? round($horas * $p->tarifa_hora, 2) : 0;
                $ganancia       = round($ingresos - $costoHoras, 2);
                $margen         = $ingresos > 0 ? round(($ganancia / $ingresos) * 100, 1) : 0;
                $horasEst       = (float) $p->horas_estimadas;
                $pctHoras       = ($horasEst > 0) ? round(($horas / $horasEst) * 100, 1) : null;

                $p->_horas      = $horas;
                $p->_minutos    = $minutos;
                $p->_ingresos   = $ingresos;
                $p->_costo      = $costoHoras;
                $p->_ganancia   = $ganancia;
                $p->_margen     = $margen;
                $p->_pct_horas  = $pctHoras;

                return $p;
            })
            ->sortByDesc('_horas');

        // KPIs globales del período
        $totalHoras     = round($proyectos->sum('_minutos') / 60, 2);
        $totalIngresos  = $proyectos->sum('_ingresos');
        $totalCosto     = $proyectos->sum('_costo');
        $totalGanancia  = round($totalIngresos - $totalCosto, 2);
        $margenGlobal   = $totalIngresos > 0 ? round(($totalGanancia / $totalIngresos) * 100, 1) : 0;

        // Tiempo por tipo (para la dona)
        $tiempoQuery = TiempoRegistro::whereHas('proyecto', fn($q) =>
                $q // el GlobalScope se aplica automáticamente
            )
            ->whereYear('fecha', $anio);
        if ($mes) $tiempoQuery->whereMonth('fecha', $mes);
        if (!auth()->user()->isSuperAdmin()) {
            $tiempoQuery->whereHas('proyecto', fn($q) => $q->where('creado_por', auth()->id()));
        }
        $tiempoPorTipo = $tiempoQuery->selectRaw('tipo, SUM(minutos) as total_minutos')
            ->groupBy('tipo')
            ->orderByDesc('total_minutos')
            ->get();

        // Horas por semana (últimas 12 semanas) para el gráfico de barras
        $semanas = collect();
        for ($i = 11; $i >= 0; $i--) {
            $semana  = now()->copy()->subWeeks($i);
            $inicio  = $semana->copy()->startOfWeek()->format('Y-m-d');
            $fin     = $semana->copy()->endOfWeek()->format('Y-m-d');
            $minQ    = TiempoRegistro::whereBetween('fecha', [$inicio, $fin]);
            if (!auth()->user()->isSuperAdmin()) {
                $minQ->whereHas('proyecto', fn($q) => $q->where('creado_por', auth()->id()));
            }
            $semanas->push([
                'label'  => $semana->startOfWeek()->format('d/m'),
                'horas'  => round($minQ->sum('minutos') / 60, 1),
            ]);
        }

        // Años disponibles para el selector (compatible SQLite + MySQL)
        $aniosDisponibles = TiempoRegistro::when(!auth()->user()->isSuperAdmin(), fn($q) =>
                $q->whereHas('proyecto', fn($p) => $p->where('creado_por', auth()->id()))
            )
            ->pluck('fecha')
            ->map(fn($f) => (int) \Carbon\Carbon::parse($f)->format('Y'))
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        if (empty($aniosDisponibles)) $aniosDisponibles = [now()->year];

        return view('admin.rentabilidad.index', compact(
            'proyectos', 'anio', 'mes',
            'totalHoras', 'totalIngresos', 'totalCosto', 'totalGanancia', 'margenGlobal',
            'tiempoPorTipo', 'semanas', 'aniosDisponibles'
        ));
    }
}
