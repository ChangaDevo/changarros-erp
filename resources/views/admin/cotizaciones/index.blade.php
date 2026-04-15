@extends('admin.layouts.app')

@section('title', 'Cotizaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Cotizaciones</h4>
  </div>
  <div>
    <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i>
      Nueva Cotización
    </a>
  </div>
</div>

<!-- Stats -->
<div class="row">
  <div class="col-6 col-md-3 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Borrador</p>
        <h3 class="text-secondary">{{ $totales['borrador'] }}</h3>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Enviadas</p>
        <h3 class="text-primary">{{ $totales['enviada'] }}</h3>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Aprobadas</p>
        <h3 class="text-success">{{ $totales['aprobada'] }}</h3>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Rechazadas</p>
        <h3 class="text-danger">{{ $totales['rechazada'] }}</h3>
      </div>
    </div>
  </div>
</div>

<!-- Table -->
<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Vencimiento</th>
                <th>Creada</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cotizaciones as $cot)
              <tr>
                <td class="text-muted small">{{ $cot->id }}</td>
                <td>
                  <a href="{{ route('admin.cotizaciones.edit', $cot) }}" class="fw-medium text-body">
                    {{ $cot->nombre }}
                  </a>
                </td>
                <td>
                  <span class="small">{{ $cot->cliente->nombre_empresa ?? '—' }}</span>
                </td>
                <td class="fw-bold">${{ number_format($cot->total, 2) }}</td>
                <td>
                  <span class="badge bg-{{ $cot->estado_badge }}">{{ ucfirst($cot->estado) }}</span>
                </td>
                <td>
                  @if($cot->fecha_vencimiento)
                    <span class="{{ $cot->fecha_vencimiento->isPast() && !in_array($cot->estado, ['aprobada','rechazada']) ? 'text-danger fw-bold' : '' }}">
                      {{ $cot->fecha_vencimiento->format('d/m/Y') }}
                    </span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="small text-muted">{{ $cot->created_at->format('d/m/Y') }}</td>
                <td>
                  <div class="d-flex gap-1 flex-wrap">
                    <a href="{{ route('admin.cotizaciones.edit', $cot) }}"
                       class="btn btn-sm btn-outline-primary" title="Editar">
                      <i data-lucide="edit-2" style="width:13px;height:13px;"></i>
                    </a>
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            title="Copiar enlace público"
                            onclick="copiarLink('{{ $cot->public_url }}', this)">
                      <i data-lucide="link" style="width:13px;height:13px;"></i>
                    </button>
                    <a href="{{ $cot->public_url }}" target="_blank"
                       class="btn btn-sm btn-outline-info" title="Ver vista cliente">
                      <i data-lucide="external-link" style="width:13px;height:13px;"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.cotizaciones.destroy', $cot) }}"
                          onsubmit="return confirm('¿Eliminar esta cotización? Esta acción no se puede deshacer.')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                        <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-5">
                  <i data-lucide="receipt" style="width:40px;height:40px;opacity:.3;" class="d-block mx-auto mb-2"></i>
                  No hay cotizaciones todavía.
                  <a href="{{ route('admin.cotizaciones.create') }}" class="ms-1">Crea la primera</a>.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($cotizaciones->hasPages())
      <div class="card-footer">
        {{ $cotizaciones->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function copiarLink(url, btn) {
  navigator.clipboard.writeText(url).then(() => {
    const icon = btn.querySelector('[data-lucide]');
    const prev = icon.getAttribute('data-lucide');
    icon.setAttribute('data-lucide', 'check');
    lucide.createIcons();
    setTimeout(() => {
      icon.setAttribute('data-lucide', prev);
      lucide.createIcons();
    }, 1800);
  });
}
</script>
@endpush
