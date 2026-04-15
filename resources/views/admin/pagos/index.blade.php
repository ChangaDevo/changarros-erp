@extends('admin.layouts.app')

@section('title', 'Pagos')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Gestión de Pagos</h4>
  </div>
</div>

<!-- Totals -->
<div class="row">
  <div class="col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Total Pendiente</p>
        <h3 class="text-warning">${{ number_format($totales['pendiente'], 2) }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Total Cobrado</p>
        <h3 class="text-success">${{ number_format($totales['pagado'], 2) }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 grid-margin">
    <div class="card">
      <div class="card-body text-center">
        <p class="text-muted small mb-1">Total Vencido</p>
        <h3 class="text-danger">${{ number_format($totales['vencido'], 2) }}</h3>
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
                <th>#</th>
                <th>Concepto</th>
                <th>Proyecto / Cliente</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Vencimiento</th>
                <th>Método</th>
                <th>Fecha Pago</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pagos as $pago)
              <tr>
                <td>{{ $pago->id }}</td>
                <td>{{ $pago->concepto }}</td>
                <td>
                  <a href="{{ route('admin.proyectos.show', $pago->proyecto) }}" class="text-body fw-medium small">
                    {{ $pago->proyecto->nombre ?? 'N/A' }}
                  </a>
                  <p class="mb-0 text-muted x-small">{{ $pago->proyecto->cliente->nombre_empresa ?? '' }}</p>
                </td>
                <td class="fw-bold">${{ number_format($pago->monto, 2) }}</td>
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
                <td>{{ $pago->metodo_pago ?? '-' }}</td>
                <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y') : '-' }}</td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('admin.proyectos.show', $pago->proyecto) }}" class="btn btn-sm btn-outline-primary" title="Ver proyecto">
                      <i data-lucide="eye" style="width:14px;height:14px;"></i>
                    </a>
                    @if($pago->estado === 'pendiente')
                    <button type="button" class="btn btn-sm btn-success" onclick="marcarPagado({{ $pago->id }})">
                      Pagado
                    </button>
                    @endif
                    @if($pago->referencia_codi)
                    <span class="badge bg-info align-self-center" title="Ref: {{ $pago->referencia_codi }}">
                      CoDi
                    </span>
                    @else
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="generarQR({{ $pago->id }})">
                      QR
                    </button>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">
                  No hay pagos registrados.
                </td>
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

<!-- Mark Paid Modal -->
<div class="modal fade" id="modalMarcarPagado" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="formMarcarPagado" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Registrar Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
            <select class="form-select" name="metodo_pago" required>
              <option value="">Seleccionar...</option>
              <option value="transferencia">Transferencia Bancaria</option>
              <option value="efectivo">Efectivo</option>
              <option value="codi">CoDi</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="cheque">Cheque</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Comprobante (opcional)</label>
            <input type="file" class="form-control" name="comprobante" accept=".pdf,.jpg,.jpeg,.png">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Confirmar Pago</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- QR Modal -->
<div class="modal fade" id="modalQR" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Referencia CoDi Generada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="text-muted">Referencia de pago CoDi:</p>
        <h4 id="qrReferencia" class="font-monospace text-primary mb-3"></h4>
        <p class="text-muted small">Monto: $<span id="qrMonto"></span></p>
        <div class="alert alert-info small">
          Comparte esta referencia con el cliente para que pueda realizar el pago a través de CoDi.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function marcarPagado(pagoId) {
  const form = document.getElementById('formMarcarPagado');
  form.action = '{{ url("admin/pagos") }}/' + pagoId + '/marcar-pagado';
  const modal = new bootstrap.Modal(document.getElementById('modalMarcarPagado'));
  modal.show();
}

function generarQR(pagoId) {
  fetch('{{ url("admin/pagos") }}/' + pagoId + '/generar-qr', {
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('qrReferencia').textContent = data.referencia;
    document.getElementById('qrMonto').textContent = parseFloat(data.monto).toFixed(2);
    const modal = new bootstrap.Modal(document.getElementById('modalQR'));
    modal.show();
  });
}
</script>
@endpush
