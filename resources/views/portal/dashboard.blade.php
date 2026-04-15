@extends('portal.layouts.app')

@section('title', 'Mi Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Bienvenido, {{ auth()->user()->name }}</h4>
    @if($cliente)
    <p class="text-muted mb-0">{{ $cliente->nombre_empresa }}</p>
    @endif
  </div>
</div>

@if(!$cliente)
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-body text-center py-5">
        <i data-lucide="alert-circle" style="width:48px;height:48px;" class="text-warning mb-3"></i>
        <h5>Sin empresa asociada</h5>
        <p class="text-muted">Tu cuenta no está vinculada a ninguna empresa. Contacta al administrador.</p>
      </div>
    </div>
  </div>
</div>
@else

<!-- Stats -->
<div class="row">
  <div class="col-12 col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <i data-lucide="briefcase" style="width:32px;height:32px;" class="text-primary mb-2"></i>
        <h3 class="mb-0">{{ $stats['proyectos_activos'] }}</h3>
        <p class="text-muted small mb-0">Proyectos Activos</p>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <i data-lucide="package" style="width:32px;height:32px;" class="text-info mb-2"></i>
        <h3 class="mb-0">{{ $stats['entregas_pendientes'] }}</h3>
        <p class="text-muted small mb-0">Entregas por Revisar</p>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <i data-lucide="credit-card" style="width:32px;height:32px;" class="text-warning mb-2"></i>
        <h3 class="mb-0">{{ $stats['pagos_pendientes'] }}</h3>
        <p class="text-muted small mb-0">Pagos Pendientes</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Projects -->
  <div class="col-lg-8 grid-margin">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="briefcase" style="width:16px;height:16px;" class="me-2"></i>Mis Proyectos
        </h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Avance</th>
                <th>Entrega Estimada</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($proyectos as $proyecto)
              <tr>
                <td>
                  <a href="{{ route('portal.proyectos.show', $proyecto) }}" class="fw-medium text-body">
                    {{ $proyecto->nombre }}
                  </a>
                </td>
                <td>
                  <span class="badge bg-{{ $proyecto->estado_badge }}">{{ $proyecto->estado_label }}</span>
                </td>
                <td>
                  @php
                    $total = $proyecto->entregas->count();
                    $aprobadas = $proyecto->entregas->where('estado', 'aprobado')->count();
                    $pct = $total > 0 ? round(($aprobadas / $total) * 100) : 0;
                  @endphp
                  @if($total > 0)
                  <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px;">
                      <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="small text-muted">{{ $pct }}%</span>
                  </div>
                  @else
                  <span class="text-muted small">Sin entregas</span>
                  @endif
                </td>
                <td>{{ $proyecto->fecha_entrega_estimada ? $proyecto->fecha_entrega_estimada->format('d/m/Y') : '-' }}</td>
                <td>
                  <a href="{{ route('portal.proyectos.show', $proyecto) }}" class="btn btn-sm btn-outline-primary">
                    <i data-lucide="eye" style="width:14px;height:14px;"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">Sin proyectos asignados.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Pending deliveries -->
    @if($entregas_recientes->count() > 0)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="package" style="width:16px;height:16px;" class="me-2"></i>
          Entregas por Revisar
        </h6>
      </div>
      <div class="card-body p-0">
        @foreach($entregas_recientes as $entrega)
        <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between">
          <div>
            <p class="mb-0 small fw-medium">{{ $entrega->titulo }}</p>
            <p class="mb-0 x-small text-muted">{{ $entrega->proyecto->nombre }}</p>
          </div>
          <a href="{{ route('portal.proyectos.show', $entrega->proyecto) }}" class="btn btn-sm btn-primary">
            Revisar
          </a>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Pending payments -->
    @if($pagos_pendientes->count() > 0)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="credit-card" style="width:16px;height:16px;" class="me-2"></i>
          Pagos Pendientes
        </h6>
      </div>
      <div class="card-body p-0">
        @foreach($pagos_pendientes as $pago)
        <div class="px-3 py-2 border-bottom">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-0 small fw-medium">{{ $pago->concepto }}</p>
              <p class="mb-0 x-small text-muted">{{ $pago->proyecto->nombre }}</p>
              @if($pago->fecha_vencimiento)
              <p class="mb-0 x-small {{ $pago->fecha_vencimiento->isPast() ? 'text-danger' : 'text-muted' }}">
                Vence: {{ $pago->fecha_vencimiento->format('d/m/Y') }}
              </p>
              @endif
            </div>
            <span class="fw-bold text-warning">${{ number_format($pago->monto, 2) }}</span>
          </div>
        </div>
        @endforeach
        <div class="px-3 py-2">
          <a href="{{ route('portal.pagos.index') }}" class="btn btn-sm btn-outline-warning w-100">Ver todos los pagos</a>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endif
@endsection
