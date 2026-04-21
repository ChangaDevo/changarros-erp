@extends('admin.layouts.app')
@section('title', 'Facturas & Recibos')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Facturas & Recibos</h4>
    <p class="text-muted mb-0">Crea, gestiona y envía documentos de cobro.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.facturas.create') }}?tipo=recibo" class="btn btn-outline-primary">
      <i data-lucide="file-check" style="width:15px;height:15px;" class="me-1"></i>Nuevo Recibo
    </a>
    <a href="{{ route('admin.facturas.create') }}" class="btn btn-primary">
      <i data-lucide="file-text" style="width:15px;height:15px;" class="me-1"></i>Nueva Factura
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- KPIs --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
        <div class="text-muted small">Total documentos</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fw-bold fs-4 text-primary">{{ $stats['pendientes'] }}</div>
        <div class="text-muted small">Enviadas / pendientes</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fw-bold fs-4 text-success">{{ $stats['pagadas'] }}</div>
        <div class="text-muted small">Pagadas</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fw-bold fs-4 text-success">${{ number_format($stats['monto_mes'], 0) }}</div>
        <div class="text-muted small">Cobrado este mes</div>
      </div>
    </div>
  </div>
</div>

{{-- Filtros --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-3">
        <label class="form-label small mb-1">Tipo</label>
        <select class="form-select form-select-sm" name="tipo">
          <option value="">Todos</option>
          <option value="factura" {{ request('tipo') === 'factura' ? 'selected' : '' }}>Factura</option>
          <option value="recibo"  {{ request('tipo') === 'recibo'  ? 'selected' : '' }}>Recibo</option>
        </select>
      </div>
      <div class="col-sm-3">
        <label class="form-label small mb-1">Estado</label>
        <select class="form-select form-select-sm" name="estado">
          <option value="">Todos</option>
          <option value="borrador"  {{ request('estado') === 'borrador'  ? 'selected' : '' }}>Borrador</option>
          <option value="enviada"   {{ request('estado') === 'enviada'   ? 'selected' : '' }}>Enviada</option>
          <option value="pagada"    {{ request('estado') === 'pagada'    ? 'selected' : '' }}>Pagada</option>
          <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
        </select>
      </div>
      <div class="col-sm-4">
        <label class="form-label small mb-1">Cliente</label>
        <select class="form-select form-select-sm" name="cliente_id">
          <option value="">Todos</option>
          @foreach($clientes as $c)
            <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre_empresa }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-2 d-flex gap-1">
        <button type="submit" class="btn btn-primary btn-sm flex-fill">
          <i data-lucide="search" style="width:13px;height:13px;"></i>
        </button>
        <a href="{{ route('admin.facturas.index') }}" class="btn btn-outline-secondary btn-sm">
          <i data-lucide="x" style="width:13px;height:13px;"></i>
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Tabla --}}
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-3">Folio</th>
            <th>Cliente</th>
            <th>Proyecto</th>
            <th class="text-center">Emisión</th>
            <th class="text-center">Vencimiento</th>
            <th class="text-end">Total</th>
            <th class="text-center">Estado</th>
            <th class="pe-3"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($facturas as $f)
          <tr>
            <td class="ps-3">
              <div class="d-flex align-items-center gap-2">
                <i data-lucide="{{ $f->tipo_icono }}" style="width:15px;height:15px;" class="text-muted"></i>
                <div>
                  <a href="{{ route('admin.facturas.show', $f) }}" class="fw-semibold text-body text-decoration-none">
                    {{ $f->folio }}
                  </a>
                  <div class="text-muted small">{{ $f->tipo_label }}</div>
                </div>
              </div>
            </td>
            <td>{{ $f->cliente->nombre_empresa ?? '—' }}</td>
            <td class="text-muted small">{{ $f->proyecto->nombre ?? '—' }}</td>
            <td class="text-center small">{{ $f->fecha_emision->format('d/m/Y') }}</td>
            <td class="text-center small">
              @if($f->fecha_vencimiento)
                <span class="{{ $f->esta_vencida ? 'text-danger fw-semibold' : 'text-muted' }}">
                  {{ $f->fecha_vencimiento->format('d/m/Y') }}
                  @if($f->esta_vencida) ⚠️ @endif
                </span>
              @else —
              @endif
            </td>
            <td class="text-end fw-semibold">${{ number_format($f->total, 2) }}</td>
            <td class="text-center">
              <span class="badge bg-{{ $f->estado_badge }}">{{ $f->estado_label }}</span>
            </td>
            <td class="pe-3">
              <div class="d-flex gap-1 justify-content-end">
                <a href="{{ route('admin.facturas.show', $f) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                  <i data-lucide="eye" style="width:13px;height:13px;"></i>
                </a>
                <a href="{{ route('admin.facturas.pdf', $f) }}" class="btn btn-sm btn-outline-dark" title="Descargar PDF">
                  <i data-lucide="download" style="width:13px;height:13px;"></i>
                </a>
                @if($f->estado !== 'pagada')
                <a href="{{ route('admin.facturas.edit', $f) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                  <i data-lucide="edit" style="width:13px;height:13px;"></i>
                </a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-5">
              <i data-lucide="file-text" style="width:36px;height:36px;" class="d-block mx-auto mb-2 opacity-30"></i>
              Sin documentos. <a href="{{ route('admin.facturas.create') }}">Crear la primera factura</a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if($facturas->hasPages())
  <div class="card-footer">{{ $facturas->links() }}</div>
  @endif
</div>
@endsection
