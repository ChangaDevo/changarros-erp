@extends('admin.layouts.app')

@section('title', 'Rentabilidad')

@push('plugin-styles')
<style>
.kpi-card { border-left: 4px solid transparent; }
.kpi-card.verde  { border-color: #22c55e; }
.kpi-card.azul   { border-color: #3b82f6; }
.kpi-card.ambar  { border-color: #f59e0b; }
.kpi-card.roja   { border-color: #ef4444; }
.kpi-value { font-size: 1.8rem; font-weight: 700; line-height: 1.1; }

.margen-badge {
  font-size: .75rem;
  padding: .2rem .5rem;
  border-radius: 20px;
  font-weight: 600;
}
.margen-alto    { background: #dcfce7; color: #16a34a; }
.margen-medio   { background: #fef9c3; color: #a16207; }
.margen-bajo    { background: #fee2e2; color: #dc2626; }
.margen-neutro  { background: #f1f5f9; color: #64748b; }

.tabla-rent td, .tabla-rent th { vertical-align: middle; }
.bar-rent { height: 6px; border-radius: 3px; transition: width .4s; }
</style>
@endpush

@section('content')

{{-- ══ Header ══════════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Rentabilidad</h4>
    <p class="text-muted mb-0">Analiza el valor real de cada proyecto.</p>
  </div>
  <div>
    <a href="{{ route('admin.tiempo.index') }}" class="btn btn-outline-secondary btn-sm">
      <i data-lucide="clock" style="width:14px;height:14px;" class="me-1"></i>Registrar Tiempo
    </a>
  </div>
</div>

{{-- ── Filtros ─────────────────────────────────────────────── --}}
<div class="card mb-4">
  <div class="card-body py-2">
    <form method="GET" action="{{ route('admin.rentabilidad.index') }}"
          class="row g-2 align-items-end">
      <div class="col-sm-3 col-md-2">
        <label class="form-label small mb-1">Año</label>
        <select class="form-select form-select-sm" name="anio">
          @foreach($aniosDisponibles as $a)
            <option value="{{ $a }}" {{ $anio == $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-3 col-md-2">
        <label class="form-label small mb-1">Mes</label>
        <select class="form-select form-select-sm" name="mes">
          <option value="">Todo el año</option>
          @foreach(['1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'] as $n=>$nombre)
            <option value="{{ $n }}" {{ $mes == $n ? 'selected' : '' }}>{{ $nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm">
          <i data-lucide="filter" style="width:13px;height:13px;" class="me-1"></i>Filtrar
        </button>
      </div>
    </form>
  </div>
</div>

{{-- ══ KPIs globales ══════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card kpi-card azul h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Horas totales</div>
        <div class="kpi-value text-primary">{{ number_format($totalHoras, 1) }}<span class="fs-6 fw-normal ms-1">h</span></div>
        <div class="text-muted small mt-1">en el período</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card kpi-card verde h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Ingresos cobrados</div>
        <div class="kpi-value text-success">${{ number_format($totalIngresos, 0) }}</div>
        <div class="text-muted small mt-1">pagos confirmados</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card kpi-card ambar h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Costo de horas</div>
        <div class="kpi-value text-warning">${{ number_format($totalCosto, 0) }}</div>
        <div class="text-muted small mt-1">horas × tarifa</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card kpi-card {{ $totalGanancia >= 0 ? 'verde' : 'roja' }} h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Ganancia estimada</div>
        <div class="kpi-value {{ $totalGanancia >= 0 ? 'text-success' : 'text-danger' }}">
          ${{ number_format(abs($totalGanancia), 0) }}
        </div>
        <div class="mt-1">
          @php
            $cls = $margenGlobal >= 50 ? 'margen-alto' : ($margenGlobal >= 25 ? 'margen-medio' : ($margenGlobal > 0 ? 'margen-bajo' : 'margen-neutro'));
          @endphp
          <span class="margen-badge {{ $cls }}">{{ $margenGlobal }}% margen</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">

  {{-- Gráfico barras: horas por semana --}}
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">Horas por semana</h6>
        <span class="text-muted small">Últimas 12 semanas</span>
      </div>
      <div class="card-body">
        <canvas id="chartSemanas" height="100"></canvas>
      </div>
    </div>
  </div>

  {{-- Dona: tiempo por tipo --}}
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">Tiempo por actividad</h6>
      </div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        @if($tiempoPorTipo->isEmpty())
          <p class="text-muted small text-center">Sin datos en el período.</p>
        @else
          <canvas id="chartTipo" height="180"></canvas>
          <div class="mt-3 w-100">
            @foreach($tiempoPorTipo as $t)
            @php $hh = round($t->total_minutos / 60, 1); @endphp
            <div class="d-flex justify-content-between align-items-center small mb-1">
              <span>{{ ucfirst($t->tipo) }}</span>
              <span class="fw-semibold">{{ $hh }}h</span>
            </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

</div>

{{-- ══ Tabla por proyecto ═══════════════════════════════════ --}}
<div class="card">
  <div class="card-header">
    <h6 class="card-title mb-0 fw-semibold">Rentabilidad por proyecto</h6>
  </div>
  <div class="card-body p-0">
    @if($proyectos->isEmpty())
      <div class="text-center py-5 text-muted">
        <i data-lucide="bar-chart-2" style="width:40px;height:40px;" class="d-block mx-auto mb-2 opacity-30"></i>
        <p>Sin proyectos con tiempo registrado en este período.</p>
        <a href="{{ route('admin.tiempo.index') }}" class="btn btn-primary btn-sm">
          <i data-lucide="clock" style="width:13px;height:13px;" class="me-1"></i>Registrar tiempo
        </a>
      </div>
    @else
    <div class="table-responsive">
      <table class="table tabla-rent mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-3">Proyecto</th>
            <th class="text-center">Horas<br><small class="text-muted fw-normal">usadas / est.</small></th>
            <th class="text-end">Ingresos</th>
            <th class="text-end">Costo hrs.</th>
            <th class="text-end">Ganancia</th>
            <th class="text-center">Margen</th>
            <th class="text-center pe-3">Estado</th>
          </tr>
        </thead>
        <tbody>
          @foreach($proyectos as $p)
          @php
            $margenCls = $p->_margen >= 50
              ? 'margen-alto'
              : ($p->_margen >= 25 ? 'margen-medio' : ($p->_margen > 0 ? 'margen-bajo' : 'margen-neutro'));
            $barW = $p->_pct_horas ? min(100, $p->_pct_horas) : 0;
            $barClr = $barW >= 100 ? '#ef4444' : ($barW >= 75 ? '#f59e0b' : '#3b82f6');
          @endphp
          <tr>
            <td class="ps-3">
              <a href="{{ route('admin.proyectos.show', $p) }}" class="fw-medium text-body text-decoration-none">
                {{ $p->nombre }}
              </a>
              <div class="text-muted small">{{ $p->cliente->nombre_empresa ?? '—' }}</div>
            </td>
            <td class="text-center">
              <div class="fw-semibold">{{ $p->_horas }}h</div>
              @if($p->horas_estimadas)
                <div class="text-muted small">/ {{ $p->horas_estimadas }}h est.</div>
                <div class="bar-rent mt-1 bg-light" style="width:80px;margin:0 auto;">
                  <div class="bar-rent" style="width:{{ $barW }}%;background:{{ $barClr }};"></div>
                </div>
              @else
                <div class="text-muted small">sin estimar</div>
              @endif
            </td>
            <td class="text-end fw-semibold">
              @if($p->_ingresos > 0)
                <span class="text-success">${{ number_format($p->_ingresos, 0) }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-end">
              @if($p->_costo > 0)
                ${{ number_format($p->_costo, 0) }}
              @else
                <span class="text-muted small">sin tarifa</span>
              @endif
            </td>
            <td class="text-end fw-semibold">
              @if($p->_ingresos > 0 && $p->_costo > 0)
                <span class="{{ $p->_ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                  ${{ number_format(abs($p->_ganancia), 0) }}
                  {{ $p->_ganancia < 0 ? '↓' : '' }}
                </span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td class="text-center">
              @if($p->_ingresos > 0 && $p->_costo > 0)
                <span class="margen-badge {{ $margenCls }}">{{ $p->_margen }}%</span>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
            <td class="text-center pe-3">
              <span class="badge bg-{{ $p->estado_badge }}">{{ $p->estado_label }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

{{-- Nota explicativa --}}
<div class="alert alert-light border mt-3 small">
  <i data-lucide="info" style="width:13px;height:13px;" class="me-1"></i>
  <strong>Costo de horas</strong> = horas registradas × tarifa/hora configurada en el proyecto.
  <strong>Ingresos</strong> = pagos marcados como <em>pagado</em> en ese proyecto.
  Para proyectos sin tarifa configurada, edita el proyecto y agrega las horas estimadas y tarifa.
</div>

@endsection

@push('plugin-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
// ── Gráfico barras: horas por semana ─────────────────────────
const semanasData = @json($semanas);
new Chart(document.getElementById('chartSemanas'), {
  type: 'bar',
  data: {
    labels: semanasData.map(s => s.label),
    datasets: [{
      label: 'Horas',
      data: semanasData.map(s => s.horas),
      backgroundColor: 'rgba(59,130,246,.7)',
      borderColor: 'rgba(59,130,246,1)',
      borderWidth: 1,
      borderRadius: 4,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } },
      x: { grid: { display: false } }
    }
  }
});

// ── Dona: tiempo por tipo ─────────────────────────────────────
@if($tiempoPorTipo->isNotEmpty())
const tipoData = @json($tiempoPorTipo);
const colores  = {
  diseño:         '#3b82f6',
  redaccion:      '#06b6d4',
  reunion:        '#f59e0b',
  revision:       '#94a3b8',
  desarrollo:     '#22c55e',
  administracion: '#1e293b',
  otro:           '#e2e8f0',
};
new Chart(document.getElementById('chartTipo'), {
  type: 'doughnut',
  data: {
    labels: tipoData.map(t => t.tipo.charAt(0).toUpperCase() + t.tipo.slice(1)),
    datasets: [{
      data: tipoData.map(t => (t.total_minutos / 60).toFixed(1)),
      backgroundColor: tipoData.map(t => colores[t.tipo] ?? '#e2e8f0'),
      borderWidth: 2,
      borderColor: '#fff',
    }]
  },
  options: {
    cutout: '65%',
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` ${ctx.parsed}h`
        }
      }
    }
  }
});
@endif
</script>
@endpush
