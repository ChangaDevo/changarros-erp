@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1 fw-bold">Dashboard</h4>
    <p class="text-muted small mb-0">{{ now()->isoFormat('dddd D [de] MMMM, YYYY') }}</p>
  </div>
</div>

{{-- ===== FILA 1: Stats principales ===== --}}
<div class="row g-3 mb-3">
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Clientes Activos</p>
            <h3 class="mb-0 fw-bold">{{ $stats['clientes_activos'] }}</h3>
          </div>
          <div class="icon-box icon-box-primary rounded-circle p-3">
            <i data-lucide="users" class="text-primary"></i>
          </div>
        </div>
        <a href="{{ route('admin.clientes.index') }}" class="text-primary small mt-2 d-inline-block">Ver clientes →</a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Proyectos Activos</p>
            <h3 class="mb-0 fw-bold">{{ $stats['proyectos_activos'] }}</h3>
          </div>
          <div class="icon-box icon-box-success rounded-circle p-3">
            <i data-lucide="briefcase" class="text-success"></i>
          </div>
        </div>
        <a href="{{ route('admin.proyectos.index') }}" class="text-success small mt-2 d-inline-block">Ver proyectos →</a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Pagos Pendientes</p>
            <h3 class="mb-0 fw-bold">{{ $stats['pagos_pendientes'] }}</h3>
          </div>
          <div class="icon-box icon-box-warning rounded-circle p-3">
            <i data-lucide="credit-card" class="text-warning"></i>
          </div>
        </div>
        <a href="{{ route('admin.pagos.index') }}" class="text-warning small mt-2 d-inline-block">Ver pagos →</a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Entregas por Aprobar</p>
            <h3 class="mb-0 fw-bold">{{ $stats['entregas_por_aprobar'] }}</h3>
          </div>
          <div class="icon-box icon-box-info rounded-circle p-3">
            <i data-lucide="package" class="text-info"></i>
          </div>
        </div>
        <span class="text-info small mt-2 d-inline-block">Esperando respuesta del cliente</span>
      </div>
    </div>
  </div>
</div>

{{-- ===== FILA 2: Stats Social Media + Cotizaciones ===== --}}
<div class="row g-3 mb-3">
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Cotizaciones en Espera</p>
            <h3 class="mb-0 fw-bold">{{ $stats['cotizaciones_en_espera'] }}</h3>
          </div>
          <div class="icon-box icon-box-primary rounded-circle p-3" style="background:rgba(91,71,251,.15)">
            <i data-lucide="receipt" style="color:#5b47fb"></i>
          </div>
        </div>
        <a href="{{ route('admin.cotizaciones.index') }}" class="small mt-2 d-inline-block" style="color:#5b47fb">Ver cotizaciones →</a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Posts por Aprobar</p>
            <h3 class="mb-0 fw-bold">{{ $stats['posts_por_aprobar'] }}</h3>
          </div>
          <div class="icon-box rounded-circle p-3" style="background:rgba(253,126,20,.15)">
            <i data-lucide="calendar-clock" style="color:#fd7e14"></i>
          </div>
        </div>
        <a href="{{ route('admin.publicaciones.index') }}" class="small mt-2 d-inline-block" style="color:#fd7e14">Ver calendario →</a>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Posts en Cola</p>
            <h3 class="mb-0 fw-bold">{{ $stats['posts_en_cola'] }}</h3>
          </div>
          <div class="icon-box icon-box-success rounded-circle p-3">
            <i data-lucide="send" class="text-success"></i>
          </div>
        </div>
        <span class="text-success small mt-2 d-inline-block">Aprobados, esperando fecha</span>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <p class="text-muted small mb-1">Posts con Error</p>
            <h3 class="mb-0 fw-bold {{ $stats['posts_con_error'] > 0 ? 'text-danger' : '' }}">
              {{ $stats['posts_con_error'] }}
            </h3>
          </div>
          <div class="icon-box icon-box-danger rounded-circle p-3">
            <i data-lucide="alert-triangle" class="text-danger"></i>
          </div>
        </div>
        @if($stats['posts_con_error'] > 0)
          <a href="{{ route('admin.publicaciones.index') }}" class="text-danger small mt-2 d-inline-block">Revisar errores →</a>
        @else
          <span class="text-muted small mt-2 d-inline-block">Sin errores</span>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- ===== FILA 3: Proyectos por cliente + Actividad ===== --}}
<div class="row g-3 mb-3">
  <div class="col-lg-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">
          <i data-lucide="briefcase" style="width:15px;height:15px;" class="me-1"></i>
          Proyectos Activos por Cliente
        </h6>
        <a href="{{ route('admin.proyectos.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
      </div>
      <div class="card-body p-0" style="max-height:380px;overflow-y:auto;">
        @forelse($proyectos_por_cliente as $clienteGrupo)
        <div class="border-bottom">
          {{-- Cabecera del cliente --}}
          <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light-subtle">
            <a href="{{ route('admin.clientes.show', $clienteGrupo) }}" class="fw-semibold small text-body text-decoration-none">
              <i data-lucide="building-2" style="width:13px;height:13px;" class="me-1 text-muted"></i>
              {{ $clienteGrupo->nombre_empresa }}
            </a>
            @if($clienteGrupo->es_cliente_interno)
              <span class="badge bg-info" style="font-size:9px;">Interno</span>
            @endif
            <span class="badge bg-secondary ms-auto" style="font-size:10px;">
              {{ $clienteGrupo->proyectos->count() }} {{ Str::plural('proyecto', $clienteGrupo->proyectos->count()) }}
            </span>
          </div>
          {{-- Proyectos de este cliente --}}
          @foreach($clienteGrupo->proyectos as $proyecto)
          <div class="d-flex align-items-center gap-2 px-3 py-2 border-top" style="padding-left:2rem!important;">
            <div class="flex-grow-1 overflow-hidden">
              <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="small fw-medium text-body text-truncate d-block">
                {{ $proyecto->nombre }}
              </a>
            </div>
            <span class="badge bg-{{ $proyecto->estado_badge }} flex-shrink-0" style="font-size:10px;">
              {{ $proyecto->estado_label }}
            </span>
            @if($proyecto->cotizaciones->isNotEmpty())
              <span title="{{ $proyecto->cotizaciones->count() }} cotización(es)" class="text-muted flex-shrink-0" style="font-size:11px;">
                <i data-lucide="receipt" style="width:12px;height:12px;"></i> {{ $proyecto->cotizaciones->count() }}
              </span>
            @endif
            <span class="text-muted x-small flex-shrink-0">
              {{ $proyecto->fecha_entrega_estimada?->format('d/m/Y') ?? '—' }}
            </span>
            <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="btn btn-xs btn-outline-secondary flex-shrink-0 py-0 px-1">
              <i data-lucide="eye" style="width:12px;height:12px;"></i>
            </a>
          </div>
          @endforeach
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i data-lucide="inbox" style="width:24px;height:24px;" class="mb-2"></i>
          <p class="small mb-0">No hay proyectos activos.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">Actividad Reciente</h6>
        <a href="{{ route('admin.actividad.index') }}" class="btn btn-sm btn-outline-primary">Ver todo</a>
      </div>
      <div class="card-body" style="max-height:320px;overflow-y:auto;">
        @forelse($actividad_reciente as $actividad)
        <div class="d-flex align-items-start mb-3">
          <div class="me-2 flex-shrink-0">
            <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary-subtle rounded-circle">
              <i data-lucide="zap" style="width:13px;height:13px;" class="text-primary"></i>
            </div>
          </div>
          <div class="overflow-hidden">
            <p class="mb-0 small fw-medium text-truncate">{{ $actividad->accion }}</p>
            <p class="mb-0 small text-muted text-truncate">{{ $actividad->descripcion }}</p>
            <p class="mb-0 x-small text-muted">
              {{ $actividad->created_at?->diffForHumans() }}
              @if($actividad->usuario) · {{ $actividad->usuario->name }} @endif
            </p>
          </div>
        </div>
        @empty
        <p class="text-muted text-center mb-0 small">Sin actividad reciente.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ===== FILA 4: Pagos + Cotizaciones recientes ===== --}}
<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">Pagos Pendientes</h6>
        <a href="{{ route('admin.pagos.index') }}" class="btn btn-sm btn-outline-warning">Ver todos</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Concepto</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th class="pe-3">Vence</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pagos_pendientes as $pago)
              <tr>
                <td class="ps-3 small">{{ Str::limit($pago->concepto, 22) }}</td>
                <td class="small text-muted">{{ Str::limit($pago->proyecto->cliente->nombre_empresa ?? '—', 18) }}</td>
                <td class="small fw-semibold">${{ number_format($pago->monto, 2) }}</td>
                <td class="pe-3 small">
                  @if($pago->fecha_vencimiento)
                    <span class="{{ $pago->fecha_vencimiento->isPast() ? 'text-danger fw-semibold' : 'text-muted' }}">
                      {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">Sin pagos pendientes.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">Cotizaciones Recientes</h6>
        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Cotizacion</th>
                <th>Cliente</th>
                <th>Total</th>
                <th class="pe-3">Estado</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cotizaciones_recientes as $cot)
              <tr>
                <td class="ps-3 small fw-medium">
                  <a href="{{ route('admin.cotizaciones.show', $cot) }}" class="text-body">
                    {{ Str::limit($cot->nombre, 22) }}
                  </a>
                </td>
                <td class="small text-muted">{{ Str::limit($cot->cliente->nombre_empresa ?? '—', 16) }}</td>
                <td class="small fw-semibold">${{ number_format($cot->total, 2) }}</td>
                <td class="pe-3">
                  <span class="badge bg-{{ $cot->estado_badge }}">{{ ucfirst($cot->estado) }}</span>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">Sin cotizaciones.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== FILA 5: Entregas pendientes + Posts pendientes de aprobacion ===== --}}
<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">Entregas Pendientes de Respuesta</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Entrega</th>
                <th>Proyecto</th>
                <th>Cliente</th>
                <th class="pe-3"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($entregas_pendientes as $entrega)
              <tr>
                <td class="ps-3 small">{{ Str::limit($entrega->titulo, 22) }}</td>
                <td class="small text-muted">{{ Str::limit($entrega->proyecto->nombre ?? '—', 18) }}</td>
                <td class="small text-muted">{{ $entrega->proyecto->cliente->nombre_empresa ?? '—' }}</td>
                <td class="pe-3">
                  <a href="{{ route('admin.proyectos.show', $entrega->proyecto) }}" class="btn btn-sm btn-outline-info py-0">
                    <i data-lucide="eye" style="width:13px;height:13px;"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-4">Sin entregas pendientes.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold">
          Publicaciones por Aprobar
          @if($stats['posts_con_error'] > 0)
            <span class="badge bg-danger ms-2">{{ $stats['posts_con_error'] }} error{{ $stats['posts_con_error'] > 1 ? 'es' : '' }}</span>
          @endif
        </h6>
        <a href="{{ route('admin.publicaciones.index') }}" class="btn btn-sm btn-outline-primary">Ver calendario</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Post</th>
                <th>Cliente</th>
                <th>Red</th>
                <th class="pe-3">Fecha</th>
              </tr>
            </thead>
            <tbody>
              @forelse($posts_pendientes as $post)
              <tr>
                <td class="ps-3 small fw-medium">{{ Str::limit($post->titulo, 20) }}</td>
                <td class="small text-muted">{{ Str::limit($post->cliente->nombre_empresa ?? '—', 16) }}</td>
                <td class="small">
                  @php
                    $iconos = ['instagram'=>'📸','facebook'=>'📘','tiktok'=>'🎵','twitter'=>'🐦','linkedin'=>'💼','youtube'=>'▶️'];
                  @endphp
                  {{ $iconos[$post->red_social] ?? '📣' }}
                </td>
                <td class="pe-3 small text-muted">
                  {{ $post->fecha_programada->format('d/m H:i') }}
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center text-muted py-4">
                  @if($posts_con_error->isNotEmpty())
                    <span class="text-danger">
                      <i data-lucide="alert-triangle" style="width:14px;height:14px;" class="me-1"></i>
                      {{ $posts_con_error->count() }} publicaciones con error
                    </span>
                  @else
                    Sin publicaciones pendientes de aprobacion.
                  @endif
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{-- Posts con error al final si los hay --}}
        @if($posts_con_error->isNotEmpty())
        <div class="border-top px-3 py-2 bg-danger-subtle">
          <p class="small fw-semibold text-danger mb-1">
            <i data-lucide="alert-triangle" style="width:13px;height:13px;" class="me-1"></i>
            Publicaciones con error:
          </p>
          @foreach($posts_con_error as $ep)
          <p class="small mb-1 text-truncate text-danger">
            {{ $iconos[$ep->red_social] ?? '📣' }} {{ $ep->titulo }} — {{ $ep->cliente->nombre_empresa }}
            <span class="text-muted">({{ Str::limit($ep->error_publicacion, 50) }})</span>
          </p>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection
