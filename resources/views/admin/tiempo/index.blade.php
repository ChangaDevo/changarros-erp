@extends('admin.layouts.app')

@section('title', 'Registro de Tiempo')

@push('plugin-styles')
<style>
/* ── Timer widget ── */
.timer-widget {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  color: #fff;
  border-radius: 12px;
  padding: 1.25rem 1.5rem;
}
.timer-display {
  font-family: 'Roboto Mono', monospace, monospace;
  font-size: 2.8rem;
  font-weight: 700;
  letter-spacing: .05em;
  line-height: 1;
}
.timer-running { color: #4ade80; }
.timer-stopped { color: rgba(255,255,255,.6); }

/* ── Tipo badges ── */
.badge-tipo { font-size: .7rem; }

/* ── Barra de horas ── */
.bar-horas { height: 6px; border-radius: 3px; }

/* ── Hover on rows ── */
.table tbody tr:hover td { background: rgba(0,0,0,.02); }
</style>
@endpush

@section('content')

{{-- ══ Header ══════════════════════════════════════════════ --}}
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Registro de Tiempo</h4>
    <p class="text-muted mb-0">Controla cuánto tiempo dedicas a cada proyecto.</p>
  </div>
  <div>
    <a href="{{ route('admin.rentabilidad.index') }}" class="btn btn-outline-primary btn-sm">
      <i data-lucide="bar-chart-2" style="width:14px;height:14px;" class="me-1"></i>Ver Rentabilidad
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    <i data-lucide="check-circle" style="width:15px;height:15px;" class="me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-4">

  {{-- ══ Columna izquierda ═══════════════════════════════════ --}}
  <div class="col-lg-4">

    {{-- Timer en vivo --}}
    <div class="timer-widget mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="fw-semibold opacity-75 small text-uppercase tracking-wide">⏱ Timer</span>
        <span id="timer-estado" class="badge bg-secondary">Detenido</span>
      </div>

      <div class="timer-display timer-stopped mb-3" id="timer-display">00:00:00</div>

      <div id="timer-info" class="small opacity-75 mb-3" style="display:none;">
        <span id="timer-proyecto-label">—</span><br>
        <span id="timer-tarea-label" class="opacity-60">—</span>
      </div>

      {{-- Formulario de inicio --}}
      <div id="timer-form-start">
        <select class="form-select form-select-sm mb-2 bg-dark border-0 text-white" id="t-proyecto" style="color:#fff!important;">
          <option value="">Selecciona un proyecto…</option>
          @foreach($proyectos as $p)
            <option value="{{ $p->id }}">{{ $p->nombre }} — {{ $p->cliente->nombre_empresa ?? '' }}</option>
          @endforeach
        </select>
        <input type="text" class="form-control form-control-sm mb-2 bg-dark border-0 text-white"
               id="t-tarea" placeholder="¿En qué estás trabajando?" style="color:#fff!important;">
        <select class="form-select form-select-sm mb-3 bg-dark border-0 text-white" id="t-tipo" style="color:#fff!important;">
          @foreach(['diseño'=>'Diseño','redaccion'=>'Redacción','reunion'=>'Reunión','revision'=>'Revisión','desarrollo'=>'Desarrollo','administracion'=>'Administración','otro'=>'Otro'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
        <button class="btn btn-success w-100 fw-semibold" id="btn-iniciar" onclick="iniciarTimer()">
          <i data-lucide="play" style="width:15px;height:15px;" class="me-1"></i>Iniciar Timer
        </button>
      </div>

      {{-- Botón de detener --}}
      <div id="timer-form-stop" style="display:none;">
        <button class="btn btn-danger w-100 fw-semibold" onclick="detenerTimer()">
          <i data-lucide="square" style="width:15px;height:15px;" class="me-1"></i>Detener y Guardar
        </button>
      </div>
    </div>

    {{-- Stats rápidas --}}
    <div class="row g-3 mb-4">
      <div class="col-6">
        <div class="card text-center">
          <div class="card-body py-3">
            <div class="fw-bold fs-4 text-primary">{{ gmdate('H:i', $totalHoy * 60) }}</div>
            <div class="text-muted small">Hoy</div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card text-center">
          <div class="card-body py-3">
            @php $hTot = intdiv($totalMinutos, 60); $mTot = $totalMinutos % 60; @endphp
            <div class="fw-bold fs-4 text-dark">{{ $hTot }}h{{ $mTot > 0 ? " {$mTot}m" : '' }}</div>
            <div class="text-muted small">Filtrado</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Entrada manual --}}
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">
          <i data-lucide="edit-3" style="width:14px;height:14px;" class="me-1"></i>Entrada Manual
        </h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.tiempo.store') }}">
          @csrf
          <div class="mb-2">
            <label class="form-label small mb-1">Proyecto <span class="text-danger">*</span></label>
            <select class="form-select form-select-sm @error('proyecto_id') is-invalid @enderror" name="proyecto_id" required>
              <option value="">Selecciona…</option>
              @foreach($proyectos as $p)
                <option value="{{ $p->id }}" {{ old('proyecto_id') == $p->id ? 'selected' : '' }}>
                  {{ $p->nombre }}
                </option>
              @endforeach
            </select>
            @error('proyecto_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-2">
            <label class="form-label small mb-1">¿Qué hiciste? <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm @error('tarea') is-invalid @enderror"
                   name="tarea" value="{{ old('tarea') }}" required placeholder="ej. Diseño de portada">
            @error('tarea')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small mb-1">Tipo</label>
              <select class="form-select form-select-sm" name="tipo">
                @foreach(['diseño'=>'Diseño','redaccion'=>'Redacción','reunion'=>'Reunión','revision'=>'Revisión','desarrollo'=>'Desarrollo','administracion'=>'Administración','otro'=>'Otro'] as $k=>$v)
                  <option value="{{ $k }}" {{ old('tipo') === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small mb-1">Fecha</label>
              <input type="date" class="form-control form-control-sm" name="fecha"
                     value="{{ old('fecha', today()->toDateString()) }}" required>
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label small mb-1">Horas</label>
              <input type="number" class="form-control form-control-sm" name="horas"
                     value="{{ old('horas', 0) }}" min="0" max="23">
            </div>
            <div class="col-6">
              <label class="form-label small mb-1">Minutos</label>
              <input type="number" class="form-control form-control-sm @error('minutos_entrada') is-invalid @enderror"
                     name="minutos_entrada" value="{{ old('minutos_entrada', 30) }}" min="0" max="59">
              @error('minutos_entrada')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="facturable" value="1"
                     id="facturable" {{ old('facturable', '1') ? 'checked' : '' }}>
              <label class="form-check-label small" for="facturable">Facturable al cliente</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i data-lucide="save" style="width:13px;height:13px;" class="me-1"></i>Guardar
          </button>
        </form>
      </div>
    </div>

  </div>{{-- /col-lg-4 --}}

  {{-- ══ Columna derecha: historial ════════════════════════════ --}}
  <div class="col-lg-8">

    {{-- Filtros --}}
    <div class="card mb-3">
      <div class="card-body py-2">
        <form method="GET" action="{{ route('admin.tiempo.index') }}" class="row g-2 align-items-end">
          <div class="col-sm-4">
            <label class="form-label small mb-1">Proyecto</label>
            <select class="form-select form-select-sm" name="proyecto_id">
              <option value="">Todos</option>
              @foreach($proyectos as $p)
                <option value="{{ $p->id }}" {{ request('proyecto_id') == $p->id ? 'selected' : '' }}>
                  {{ $p->nombre }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-3">
            <label class="form-label small mb-1">Tipo</label>
            <select class="form-select form-select-sm" name="tipo">
              <option value="">Todos</option>
              @foreach(['diseño'=>'Diseño','redaccion'=>'Redacción','reunion'=>'Reunión','revision'=>'Revisión','desarrollo'=>'Desarrollo','administracion'=>'Administración','otro'=>'Otro'] as $k=>$v)
                <option value="{{ $k }}" {{ request('tipo') === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-2">
            <label class="form-label small mb-1">Desde</label>
            <input type="date" class="form-control form-control-sm" name="fecha_desde" value="{{ request('fecha_desde') }}">
          </div>
          <div class="col-sm-2">
            <label class="form-label small mb-1">Hasta</label>
            <input type="date" class="form-control form-control-sm" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
          </div>
          <div class="col-sm-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
              <i data-lucide="search" style="width:13px;height:13px;"></i>
            </button>
            <a href="{{ route('admin.tiempo.index') }}" class="btn btn-outline-secondary btn-sm">
              <i data-lucide="x" style="width:13px;height:13px;"></i>
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- Tabla de registros --}}
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Fecha</th>
                <th>Proyecto</th>
                <th>Tarea</th>
                <th>Tipo</th>
                <th class="text-center">Tiempo</th>
                <th class="text-center">Fact.</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($registros as $r)
              <tr>
                <td class="ps-3 text-muted small">{{ $r->fecha->format('d/m/y') }}</td>
                <td>
                  <div class="fw-medium small">{{ $r->proyecto->nombre ?? '—' }}</div>
                  <div class="text-muted" style="font-size:.7rem;">{{ $r->proyecto->cliente->nombre_empresa ?? '' }}</div>
                </td>
                <td class="small">{{ $r->tarea }}</td>
                <td>
                  <span class="badge bg-{{ $r->tipo_color }} badge-tipo">{{ $r->tipo_label }}</span>
                </td>
                <td class="text-center fw-semibold small">{{ $r->duracion_formateada }}</td>
                <td class="text-center">
                  @if($r->facturable)
                    <i data-lucide="check" style="width:14px;height:14px;" class="text-success"></i>
                  @else
                    <i data-lucide="minus" style="width:14px;height:14px;" class="text-muted"></i>
                  @endif
                </td>
                <td class="pe-3">
                  <form method="POST" action="{{ route('admin.tiempo.destroy', $r) }}"
                        onsubmit="return confirm('¿Eliminar este registro?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-link btn-sm p-0 text-danger">
                      <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <i data-lucide="clock" style="width:32px;height:32px;" class="d-block mx-auto mb-2 opacity-30"></i>
                  Sin registros. ¡Empieza a trackear tu tiempo!
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($registros->hasPages())
      <div class="card-footer">{{ $registros->links() }}</div>
      @endif
    </div>

  </div>{{-- /col-lg-8 --}}
</div>

@endsection

@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const URL_IN = "{{ route('admin.tiempo.timer.iniciar') }}";
const URL_OUT= "{{ route('admin.tiempo.timer.detener') }}";

let timerInterval = null;
let timerInicio   = null; // Date object

// ── Restaurar timer activo desde servidor ───────────────────
@if($timerActivo)
  timerInicio = new Date("{{ $timerActivo->timer_inicio->toISOString() }}");
  mostrarTimerActivo(
    "{{ addslashes($timerActivo->proyecto->nombre ?? '') }}",
    "{{ addslashes($timerActivo->tarea) }}"
  );
  arrancarReloj();
@endif

// ── Iniciar ─────────────────────────────────────────────────
function iniciarTimer() {
  const proyectoId = document.getElementById('t-proyecto').value;
  const tarea      = document.getElementById('t-tarea').value.trim();
  const tipo       = document.getElementById('t-tipo').value;

  if (!proyectoId) { alert('Selecciona un proyecto.'); return; }
  if (!tarea)      { alert('Describe en qué estás trabajando.'); return; }

  fetch(URL_IN, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ proyecto_id: proyectoId, tarea, tipo })
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      timerInicio = new Date(data.inicio);
      const label = document.querySelector('#t-proyecto option:checked').text.split('—')[0].trim();
      mostrarTimerActivo(label, tarea);
      arrancarReloj();
    }
  })
  .catch(() => alert('Error al iniciar el timer.'));
}

// ── Detener ──────────────────────────────────────────────────
function detenerTimer() {
  fetch(URL_OUT, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({})
  })
  .then(r => r.json())
  .then(data => {
    if (data.ok) {
      clearInterval(timerInterval);
      timerInterval = null;
      timerInicio   = null;
      document.getElementById('timer-display').textContent = '00:00:00';
      document.getElementById('timer-display').className = 'timer-display timer-stopped mb-3';
      document.getElementById('timer-form-start').style.display = '';
      document.getElementById('timer-form-stop').style.display  = 'none';
      document.getElementById('timer-info').style.display       = 'none';
      document.getElementById('timer-estado').textContent = 'Detenido';
      document.getElementById('timer-estado').className   = 'badge bg-secondary';
      // Recargar para ver el nuevo registro
      setTimeout(() => location.reload(), 500);
    }
  })
  .catch(() => alert('Error al detener el timer.'));
}

// ── Helpers ──────────────────────────────────────────────────
function mostrarTimerActivo(proyecto, tarea) {
  document.getElementById('timer-form-start').style.display = 'none';
  document.getElementById('timer-form-stop').style.display  = '';
  document.getElementById('timer-info').style.display       = '';
  document.getElementById('timer-proyecto-label').textContent = proyecto;
  document.getElementById('timer-tarea-label').textContent    = tarea;
  document.getElementById('timer-estado').textContent = 'En curso';
  document.getElementById('timer-estado').className   = 'badge bg-success';
  document.getElementById('timer-display').className  = 'timer-display timer-running mb-3';
}

function arrancarReloj() {
  timerInterval = setInterval(tickReloj, 1000);
  tickReloj();
}

function tickReloj() {
  if (!timerInicio) return;
  const diff = Math.floor((Date.now() - timerInicio.getTime()) / 1000);
  const h = String(Math.floor(diff / 3600)).padStart(2,'0');
  const m = String(Math.floor((diff % 3600) / 60)).padStart(2,'0');
  const s = String(diff % 60).padStart(2,'0');
  document.getElementById('timer-display').textContent = `${h}:${m}:${s}`;
}
</script>
@endpush
