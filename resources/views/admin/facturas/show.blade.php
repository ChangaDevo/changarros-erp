@extends('admin.layouts.app')
@section('title', $factura->folio)

@section('content')

{{-- Header --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <a href="{{ route('admin.facturas.index') }}" class="text-muted text-decoration-none small">
      <i data-lucide="arrow-left" style="width:13px;height:13px;" class="me-1"></i>Facturas & Recibos
    </a>
    <h4 class="fw-bold mb-1 mt-1">{{ $factura->folio }}</h4>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-{{ $factura->estado_badge }} fs-6">{{ $factura->estado_label }}</span>
      <span class="text-muted small">{{ $factura->tipo_label }} · {{ $factura->fecha_emision->format('d/m/Y') }}</span>
      @if($factura->esta_vencida)
        <span class="badge bg-danger">Vencida</span>
      @endif
    </div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.facturas.pdf', $factura) }}" class="btn btn-outline-dark btn-sm" target="_blank">
      <i data-lucide="download" style="width:14px;height:14px;" class="me-1"></i>PDF
    </a>
    @if($factura->estado !== 'pagada')
    <a href="{{ route('admin.facturas.edit', $factura) }}" class="btn btn-outline-secondary btn-sm">
      <i data-lucide="edit" style="width:14px;height:14px;" class="me-1"></i>Editar
    </a>
    @endif
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">

  {{-- ── Vista previa del documento ── --}}
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body p-4">

        {{-- Encabezado del doc --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
          <div>
            <h5 class="fw-bold mb-1">{{ $factura->creadoPor->name ?? config('app.name') }}</h5>
            <div class="text-muted small">{{ $factura->creadoPor->email ?? '' }}</div>
          </div>
          <div class="text-end">
            <div class="fw-bold fs-4 text-uppercase">{{ $factura->tipo_label }}</div>
            <div class="text-muted">{{ $factura->folio }}</div>
          </div>
        </div>

        <hr>

        {{-- Info del cliente --}}
        <div class="row g-3 mb-4">
          <div class="col-md-5">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.08em;">Facturar a</div>
            <div class="fw-semibold">{{ $factura->cliente->nombre_empresa }}</div>
            <div class="text-muted small">{{ $factura->cliente->nombre_contacto }}</div>
            <div class="text-muted small">{{ $factura->cliente->email }}</div>
            @if($factura->cliente->rfc)
              <div class="text-muted small">RFC: {{ $factura->cliente->rfc }}</div>
            @endif
          </div>
          <div class="col-md-4">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.08em;">Detalles</div>
            <div class="small">Emisión: <strong>{{ $factura->fecha_emision->format('d/m/Y') }}</strong></div>
            @if($factura->fecha_vencimiento)
              <div class="small">Vencimiento: <strong class="{{ $factura->esta_vencida ? 'text-danger' : '' }}">
                {{ $factura->fecha_vencimiento->format('d/m/Y') }}
              </strong></div>
            @endif
            @if($factura->metodo_pago)
              <div class="small">Pago: {{ $factura->metodo_pago }}</div>
            @endif
            <div class="small">Moneda: {{ $factura->moneda }}</div>
          </div>
          @if($factura->proyecto)
          <div class="col-md-3">
            <div class="text-muted small fw-semibold text-uppercase mb-1" style="letter-spacing:.08em;">Proyecto</div>
            <div class="small fw-semibold">{{ $factura->proyecto->nombre }}</div>
          </div>
          @endif
        </div>

        {{-- Items --}}
        <div class="table-responsive mb-4">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>Descripción</th>
                <th class="text-center">Cant.</th>
                <th class="text-center">Unidad</th>
                <th class="text-end">P. Unit.</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($factura->items as $item)
              <tr>
                <td>{{ $item->descripcion }}</td>
                <td class="text-center">{{ number_format($item->cantidad, 2) }}</td>
                <td class="text-center text-muted small">{{ $item->unidad }}</td>
                <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-end fw-semibold">${{ number_format($item->subtotal, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- Totales --}}
        <div class="row justify-content-end mb-4">
          <div class="col-md-5">
            <div class="d-flex justify-content-between small py-1 border-bottom">
              <span class="text-muted">Subtotal</span>
              <span>${{ number_format($factura->subtotal, 2) }}</span>
            </div>
            @if($factura->descuento > 0)
            <div class="d-flex justify-content-between small py-1 border-bottom">
              <span class="text-muted">Descuento</span>
              <span class="text-danger">-${{ number_format($factura->descuento, 2) }}</span>
            </div>
            @endif
            @if($factura->impuesto_porcentaje > 0)
            <div class="d-flex justify-content-between small py-1 border-bottom">
              <span class="text-muted">IVA ({{ $factura->impuesto_porcentaje }}%)</span>
              <span>${{ number_format($factura->impuesto_monto, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between fw-bold fs-5 pt-2">
              <span>TOTAL {{ $factura->moneda }}</span>
              <span class="text-primary">${{ number_format($factura->total, 2) }}</span>
            </div>
          </div>
        </div>

        {{-- Notas --}}
        @if($factura->notas)
        <div class="alert alert-light border mb-2">
          <strong class="small text-muted text-uppercase" style="letter-spacing:.06em;">Notas</strong>
          <p class="mb-0 small mt-1">{{ $factura->notas }}</p>
        </div>
        @endif
        @if($factura->condiciones)
        <div class="alert alert-light border">
          <strong class="small text-muted text-uppercase" style="letter-spacing:.06em;">Términos y Condiciones</strong>
          <p class="mb-0 small mt-1">{{ $factura->condiciones }}</p>
        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- ── Panel de acciones ── --}}
  <div class="col-lg-4">

    {{-- Enviar por email --}}
    @if(!in_array($factura->estado, ['cancelada']))
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">
          <i data-lucide="mail" style="width:14px;height:14px;" class="me-1"></i>Enviar por Correo
        </h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.facturas.enviar', $factura) }}">
          @csrf
          <div class="mb-2">
            <label class="form-label small mb-1">Correo destino</label>
            <input type="email" class="form-control form-control-sm" name="email_destino"
                   value="{{ $factura->cliente->email }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label small mb-1">Mensaje personal <span class="text-muted">(opcional)</span></label>
            <textarea class="form-control form-control-sm" name="mensaje_personal" rows="2"
                      placeholder="Adjunto tu factura, cualquier duda con gusto…"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-sm w-100">
            <i data-lucide="send" style="width:13px;height:13px;" class="me-1"></i>Enviar con PDF adjunto
          </button>
        </form>
        @if($factura->enviada_at)
          <p class="text-muted small mt-2 mb-0 text-center">
            Enviada el {{ $factura->enviada_at->format('d/m/Y H:i') }}
          </p>
        @endif
      </div>
    </div>
    @endif

    {{-- Acciones de estado --}}
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">Acciones</h6>
      </div>
      <div class="card-body d-grid gap-2">

        @if($factura->estado !== 'pagada' && $factura->estado !== 'cancelada')
        <form method="POST" action="{{ route('admin.facturas.marcar-pagada', $factura) }}">
          @csrf
          <button type="submit" class="btn btn-success w-100 btn-sm"
                  onclick="return confirm('¿Marcar como pagada?')">
            <i data-lucide="check-circle" style="width:13px;height:13px;" class="me-1"></i>Marcar como Pagada
          </button>
        </form>
        @endif

        <form method="POST" action="{{ route('admin.facturas.duplicar', $factura) }}">
          @csrf
          <button type="submit" class="btn btn-outline-secondary w-100 btn-sm">
            <i data-lucide="copy" style="width:13px;height:13px;" class="me-1"></i>Duplicar documento
          </button>
        </form>

        @if($factura->estado !== 'pagada' && $factura->estado !== 'cancelada')
        <form method="POST" action="{{ route('admin.facturas.cancelar', $factura) }}"
              onsubmit="return confirm('¿Cancelar este documento? Esta acción no es reversible.')">
          @csrf
          <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
            <i data-lucide="x-circle" style="width:13px;height:13px;" class="me-1"></i>Cancelar
          </button>
        </form>
        @endif

        <form method="POST" action="{{ route('admin.facturas.destroy', $factura) }}"
              onsubmit="return confirm('¿Eliminar permanentemente?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-link text-danger w-100 btn-sm p-0 mt-1">
            <i data-lucide="trash-2" style="width:13px;height:13px;" class="me-1"></i>Eliminar
          </button>
        </form>

      </div>
    </div>

    {{-- Info --}}
    <div class="card">
      <div class="card-body">
        <dl class="row small mb-0">
          <dt class="col-5 text-muted">Creada por</dt>
          <dd class="col-7">{{ $factura->creadoPor->name ?? '—' }}</dd>
          <dt class="col-5 text-muted">Creada el</dt>
          <dd class="col-7">{{ $factura->created_at->format('d/m/Y') }}</dd>
          @if($factura->pagada_at)
          <dt class="col-5 text-muted">Pagada el</dt>
          <dd class="col-7 text-success fw-semibold">{{ $factura->pagada_at->format('d/m/Y') }}</dd>
          @endif
        </dl>
      </div>
    </div>

  </div>
</div>
@endsection
