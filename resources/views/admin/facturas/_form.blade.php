{{--
  Variables: $factura (null en create), $clientes, $proyectos
  $action = route url,  $method = PUT|POST
--}}
<form method="POST" action="{{ $action }}" id="factura-form">
  @csrf
  @if(isset($method)) @method($method) @endif

  <div class="row g-4">

    {{-- ── Columna principal ── --}}
    <div class="col-lg-8">

      {{-- Encabezado del documento --}}
      <div class="card mb-3">
        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Información del Documento</h6></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select @error('tipo') is-invalid @enderror" name="tipo" required>
                <option value="factura" {{ old('tipo', $factura->tipo ?? 'factura') === 'factura' ? 'selected' : '' }}>📄 Factura</option>
                <option value="recibo"  {{ old('tipo', $factura->tipo ?? '') === 'recibo'  ? 'selected' : '' }}>✅ Recibo</option>
              </select>
              @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha de Emisión <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('fecha_emision') is-invalid @enderror"
                     name="fecha_emision" value="{{ old('fecha_emision', optional($factura)->fecha_emision?->format('Y-m-d') ?? today()->toDateString()) }}" required>
              @error('fecha_emision')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Fecha de Vencimiento</label>
              <input type="date" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                     name="fecha_vencimiento" value="{{ old('fecha_vencimiento', optional($factura)->fecha_vencimiento?->format('Y-m-d')) }}">
              @error('fecha_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Cliente <span class="text-danger">*</span></label>
              <select class="form-select @error('cliente_id') is-invalid @enderror" name="cliente_id" required>
                <option value="">— Selecciona un cliente —</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}" {{ old('cliente_id', optional($factura)->cliente_id) == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre_empresa }}
                  </option>
                @endforeach
              </select>
              @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Proyecto <span class="text-muted small">(opcional)</span></label>
              <select class="form-select @error('proyecto_id') is-invalid @enderror" name="proyecto_id">
                <option value="">— Ninguno —</option>
                @foreach($proyectos as $p)
                  <option value="{{ $p->id }}" {{ old('proyecto_id', optional($factura)->proyecto_id) == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                  </option>
                @endforeach
              </select>
              @error('proyecto_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Método de Pago</label>
              <select class="form-select" name="metodo_pago">
                <option value="">— Selecciona —</option>
                @foreach(['Transferencia bancaria','Efectivo','Tarjeta de crédito','Tarjeta de débito','PayPal','Depósito','Cheque','Otro'] as $mp)
                  <option value="{{ $mp }}" {{ old('metodo_pago', optional($factura)->metodo_pago) === $mp ? 'selected' : '' }}>{{ $mp }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Moneda</label>
              <select class="form-select" name="moneda">
                @foreach(['MXN','USD','EUR'] as $m)
                  <option value="{{ $m }}" {{ old('moneda', optional($factura)->moneda ?? 'MXN') === $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- ── Items ── --}}
      <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h6 class="card-title mb-0 fw-semibold">Conceptos / Servicios</h6>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarItem()">
            <i data-lucide="plus" style="width:13px;height:13px;" class="me-1"></i>Agregar
          </button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="items-table">
              <thead class="table-light">
                <tr>
                  <th class="ps-3" style="width:40%;">Descripción</th>
                  <th style="width:10%;">Cantidad</th>
                  <th style="width:13%;">Unidad</th>
                  <th style="width:15%;">P. Unitario</th>
                  <th style="width:13%;" class="text-end">Subtotal</th>
                  <th style="width:9%;"></th>
                </tr>
              </thead>
              <tbody id="items-body">
                {{-- Filas de items existentes o vacías --}}
                @php $existingItems = old('items', isset($factura) ? $factura->items->toArray() : []); @endphp
                @if(count($existingItems) > 0)
                  @foreach($existingItems as $i => $item)
                  <tr class="item-row">
                    <td class="ps-3">
                      <input type="text" class="form-control form-control-sm" name="items[{{ $i }}][descripcion]"
                             value="{{ $item['descripcion'] }}" required placeholder="Descripción del servicio…">
                    </td>
                    <td>
                      <input type="number" class="form-control form-control-sm item-cantidad" name="items[{{ $i }}][cantidad]"
                             value="{{ $item['cantidad'] }}" min="0.01" step="0.01" required>
                    </td>
                    <td>
                      <select class="form-select form-select-sm" name="items[{{ $i }}][unidad]">
                        @foreach(['servicio','hora','pieza','mes','día','proyecto','licencia'] as $u)
                          <option value="{{ $u }}" {{ ($item['unidad'] ?? 'servicio') === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control item-precio" name="items[{{ $i }}][precio_unitario]"
                               value="{{ $item['precio_unitario'] }}" min="0" step="0.01" required>
                      </div>
                    </td>
                    <td class="text-end fw-semibold item-subtotal">
                      ${{ number_format(($item['cantidad'] ?? 1) * ($item['precio_unitario'] ?? 0), 2) }}
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-link btn-sm p-0 text-danger" onclick="eliminarItem(this)">
                        <i data-lucide="x" style="width:15px;height:15px;"></i>
                      </button>
                    </td>
                  </tr>
                  @endforeach
                @else
                  {{-- Una fila vacía por defecto --}}
                @endif
              </tbody>
            </table>
          </div>
          <div class="p-3 border-top">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="agregarItem()">
              <i data-lucide="plus" style="width:13px;height:13px;" class="me-1"></i>Agregar concepto
            </button>
          </div>
        </div>
      </div>

      {{-- Notas --}}
      <div class="card mb-3">
        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Notas y Condiciones</h6></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label small">Notas para el cliente</label>
            <textarea class="form-control form-control-sm" name="notas" rows="2"
                      placeholder="Instrucciones de pago, agradecimiento…">{{ old('notas', optional($factura)->notas) }}</textarea>
          </div>
          <div>
            <label class="form-label small">Términos y condiciones</label>
            <textarea class="form-control form-control-sm" name="condiciones" rows="2"
                      placeholder="Política de cambios, validez de la cotización…">{{ old('condiciones', optional($factura)->condiciones) }}</textarea>
          </div>
        </div>
      </div>

    </div>{{-- /col-lg-8 --}}

    {{-- ── Sidebar: totales ── --}}
    <div class="col-lg-4">
      <div class="card sticky-top" style="top:80px;">
        <div class="card-header"><h6 class="card-title mb-0 fw-semibold">Totales</h6></div>
        <div class="card-body">
          <div class="d-flex justify-content-between small mb-2">
            <span class="text-muted">Subtotal</span>
            <span id="preview-subtotal" class="fw-semibold">$0.00</span>
          </div>
          <div class="mb-2">
            <label class="form-label small mb-1">Descuento ($)</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text">$</span>
              <input type="number" class="form-control" name="descuento" id="input-descuento"
                     value="{{ old('descuento', optional($factura)->descuento ?? 0) }}" min="0" step="0.01">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label small mb-1">IVA / Impuesto (%)</label>
            <div class="input-group input-group-sm">
              <input type="number" class="form-control" name="impuesto_porcentaje" id="input-iva"
                     value="{{ old('impuesto_porcentaje', optional($factura)->impuesto_porcentaje ?? 0) }}" min="0" max="100" step="0.01">
              <span class="input-group-text">%</span>
            </div>
          </div>
          <hr>
          <div class="d-flex justify-content-between small mb-1">
            <span class="text-muted">Descuento</span>
            <span id="preview-descuento" class="text-danger">-$0.00</span>
          </div>
          <div class="d-flex justify-content-between small mb-3">
            <span class="text-muted">IVA</span>
            <span id="preview-iva">$0.00</span>
          </div>
          <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-3">
            <span>TOTAL</span>
            <span id="preview-total" class="text-primary">$0.00</span>
          </div>
          <div class="text-end text-muted small mt-1" id="preview-moneda">MXN</div>

          <div class="mt-4 d-grid gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:15px;height:15px;" class="me-1"></i>
              {{ isset($factura) ? 'Guardar Cambios' : 'Crear Documento' }}
            </button>
            <a href="{{ route('admin.facturas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</form>

@push('scripts')
<script>
let itemIndex = {{ count($existingItems ?? []) }};

// ── Agregar fila ──────────────────────────────────────────────
function agregarItem() {
  const i = itemIndex++;
  const row = `
  <tr class="item-row">
    <td class="ps-3">
      <input type="text" class="form-control form-control-sm" name="items[${i}][descripcion]"
             required placeholder="Descripción del servicio…">
    </td>
    <td>
      <input type="number" class="form-control form-control-sm item-cantidad"
             name="items[${i}][cantidad]" value="1" min="0.01" step="0.01" required>
    </td>
    <td>
      <select class="form-select form-select-sm" name="items[${i}][unidad]">
        ${['servicio','hora','pieza','mes','día','proyecto','licencia'].map(u=>
          `<option value="${u}">${u.charAt(0).toUpperCase()+u.slice(1)}</option>`
        ).join('')}
      </select>
    </td>
    <td>
      <div class="input-group input-group-sm">
        <span class="input-group-text">$</span>
        <input type="number" class="form-control item-precio" name="items[${i}][precio_unitario]"
               value="0" min="0" step="0.01" required>
      </div>
    </td>
    <td class="text-end fw-semibold item-subtotal">$0.00</td>
    <td class="text-center">
      <button type="button" class="btn btn-link btn-sm p-0 text-danger" onclick="eliminarItem(this)">
        <i data-lucide="x" style="width:15px;height:15px;"></i>
      </button>
    </td>
  </tr>`;
  document.getElementById('items-body').insertAdjacentHTML('beforeend', row);
  if (window.lucide) lucide.createIcons();
  recalcular();
}

function eliminarItem(btn) {
  btn.closest('tr').remove();
  recalcular();
}

// ── Recalcular totales ────────────────────────────────────────
function recalcular() {
  let subtotal = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const cant  = parseFloat(row.querySelector('.item-cantidad')?.value) || 0;
    const precio= parseFloat(row.querySelector('.item-precio')?.value)   || 0;
    const sub   = cant * precio;
    subtotal += sub;
    const cell = row.querySelector('.item-subtotal');
    if (cell) cell.textContent = '$' + sub.toFixed(2);
  });

  const descuento = parseFloat(document.getElementById('input-descuento').value) || 0;
  const ivaPct    = parseFloat(document.getElementById('input-iva').value) || 0;
  const base      = subtotal - descuento;
  const iva       = Math.round(base * (ivaPct / 100) * 100) / 100;
  const total     = Math.round((base + iva) * 100) / 100;
  const moneda    = document.querySelector('[name="moneda"]')?.value || 'MXN';

  document.getElementById('preview-subtotal').textContent = '$' + subtotal.toFixed(2);
  document.getElementById('preview-descuento').textContent= '-$' + descuento.toFixed(2);
  document.getElementById('preview-iva').textContent      = '$' + iva.toFixed(2);
  document.getElementById('preview-total').textContent    = '$' + total.toFixed(2);
  document.getElementById('preview-moneda').textContent   = moneda;
}

// Eventos
document.getElementById('items-body').addEventListener('input', recalcular);
document.getElementById('input-descuento').addEventListener('input', recalcular);
document.getElementById('input-iva').addEventListener('input', recalcular);
document.querySelector('[name="moneda"]')?.addEventListener('change', recalcular);

// Inicializar
@if(count($existingItems ?? []) === 0)
  agregarItem();
@endif
recalcular();
</script>
@endpush
