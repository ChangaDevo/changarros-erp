@extends('portal.layouts.app')

@section('title', 'Mis Pagos')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Mis Pagos</h4>
  </div>
</div>

<!-- Totals -->
<div class="row">
  <div class="col-md-6 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <i data-lucide="clock" style="width:32px;height:32px;" class="text-warning mb-2"></i>
        <h3 class="text-warning">${{ number_format($totales['pendiente'], 2) }}</h3>
        <p class="text-muted small mb-0">Total Pendiente de Pago</p>
      </div>
    </div>
  </div>
  <div class="col-md-6 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <i data-lucide="check-circle" style="width:32px;height:32px;" class="text-success mb-2"></i>
        <h3 class="text-success">${{ number_format($totales['pagado'], 2) }}</h3>
        <p class="text-muted small mb-0">Total Pagado</p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Concepto</th>
                <th>Proyecto</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Vencimiento</th>
                <th>Referencia</th>
                <th>Fecha de Pago</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pagos as $pago)
              <tr>
                <td class="fw-medium">{{ $pago->concepto }}</td>
                <td>
                  <a href="{{ route('portal.proyectos.show', $pago->proyecto) }}" class="text-muted small">
                    {{ $pago->proyecto->nombre ?? 'N/A' }}
                  </a>
                </td>
                <td class="fw-bold text-{{ $pago->estado === 'pagado' ? 'success' : ($pago->estado === 'vencido' ? 'danger' : 'warning') }}">
                  ${{ number_format($pago->monto, 2) }}
                </td>
                <td>
                  <span class="badge bg-{{ $pago->estado_badge }}">{{ $pago->estado_label }}</span>
                </td>
                <td>
                  @if($pago->fecha_vencimiento)
                    <span class="{{ $pago->fecha_vencimiento->isPast() && $pago->estado !== 'pagado' ? 'text-danger fw-bold' : '' }}">
                      {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                    </span>
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if($pago->referencia_codi)
                    <span class="small font-monospace text-muted">{{ $pago->referencia_codi }}</span>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '-' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Sin pagos registrados.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($pagos->hasPages())
      <div class="card-footer">
        {{ $pagos->links() }}
      </div>
      @endif
    </div>
  </div>
</div>

@if($pagos->where('estado', 'pendiente')->count() > 0)
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">
          <i data-lucide="info" style="width:16px;height:16px;" class="me-2"></i>
          Instrucciones de Pago
        </h6>
        <p class="text-muted mb-2">Para realizar tus pagos pendientes, puedes utilizar cualquiera de los siguientes métodos:</p>
        <ul class="list-unstyled">
          <li class="mb-2"><i data-lucide="building-2" style="width:14px;height:14px;" class="me-2 text-primary"></i><strong>Transferencia bancaria</strong> - Contacta al estudio para obtener los datos bancarios.</li>
          <li class="mb-2"><i data-lucide="smartphone" style="width:14px;height:14px;" class="me-2 text-success"></i><strong>CoDi</strong> - Si tienes referencia CoDi, úsala en tu aplicación bancaria.</li>
          <li class="mb-2"><i data-lucide="mail" style="width:14px;height:14px;" class="me-2 text-info"></i><strong>Contacto</strong> - Escríbenos a <a href="mailto:pagos@changarros.com">pagos@changarros.com</a></li>
        </ul>
        <p class="text-muted small mb-0">Una vez que realices el pago, notifícanos con el comprobante para actualizar tu cuenta.</p>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
