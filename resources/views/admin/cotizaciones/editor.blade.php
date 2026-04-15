@extends('admin.layouts.app')

@section('title', 'Editar Cotización')

@push('style')
<style>
  .editor-dark-card {
    background: #1a1d23;
    color: #e8eaf0;
    border: none;
  }
  .editor-dark-card .text-muted { color: #8b909a !important; }
  .editor-dark-card .total-label { font-size: .78rem; text-transform: uppercase; letter-spacing: .07em; color: #8b909a; }
  .editor-dark-card .total-amount { font-size: 2rem; font-weight: 700; color: #fff; }
  .share-box { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: .5rem; padding: 1rem; }
  .item-row td { vertical-align: middle; }
  .add-item-row td { background: #f8f9fa; }
  [data-bs-theme="dark"] .add-item-row td { background: #2c2f36; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Editar Cotización</h4>
    <p class="text-muted small mb-0">
      <span class="badge bg-{{ $cotizacion->estado_badge }}">{{ ucfirst($cotizacion->estado) }}</span>
      &nbsp;·&nbsp; Creada {{ $cotizacion->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}
      &nbsp;·&nbsp; <span class="font-monospace">{{ substr($cotizacion->token, 0, 12) }}…</span>
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ $cotizacion->public_url }}" target="_blank" class="btn btn-outline-info btn-sm">
      <i data-lucide="external-link" style="width:14px;height:14px;" class="me-1"></i>Vista cliente
    </a>
    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-outline-secondary btn-sm">
      <i data-lucide="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Volver
    </a>
  </div>
</div>

<div class="row">

  <!-- ── LEFT COLUMN ── -->
  <div class="col-xl-8">

    <!-- Datos Generales -->
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">Datos Generales</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.cotizaciones.update', $cotizacion) }}" id="formGeneral">
          @csrf @method('PUT')

          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Nombre / Título <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                     value="{{ old('nombre', $cotizacion->nombre) }}" required>
              @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Estado</label>
              <select name="estado" class="form-select">
                @foreach(['borrador','enviada','vista','aprobada','rechazada','vencida'] as $e)
                  <option value="{{ $e }}" {{ $cotizacion->estado === $e ? 'selected' : '' }}>
                    {{ ucfirst($e) }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Cliente <span class="text-danger">*</span></label>
              <select name="cliente_id" id="clienteSelect" class="form-select" required>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}" {{ $cotizacion->cliente_id == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre_empresa }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Proyecto (opcional)</label>
              <select name="proyecto_id" id="proyectoSelect" class="form-select">
                <option value="">Sin proyecto asociado</option>
                @foreach($proyectos as $p)
                  <option value="{{ $p->id }}" {{ $cotizacion->proyecto_id == $p->id ? 'selected' : '' }}>
                    {{ $p->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Email Contacto</label>
              <input type="text" class="form-control" value="{{ $cotizacion->cliente->email }}" readonly disabled>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">IVA %</label>
              <input type="number" name="iva_porcentaje" class="form-control"
                     value="{{ old('iva_porcentaje', $cotizacion->iva_porcentaje) }}"
                     min="0" max="100" step="0.01" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Fecha Vencimiento</label>
              <input type="date" name="fecha_vencimiento" class="form-control"
                     value="{{ old('fecha_vencimiento', $cotizacion->fecha_vencimiento?->format('Y-m-d')) }}">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Notas / Términos</label>
            <textarea name="notas" class="form-control" rows="3">{{ old('notas', $cotizacion->notas) }}</textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:14px;height:14px;" class="me-1"></i>
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Desglose de Servicios -->
    <div class="card grid-margin">
      <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="card-title mb-0">Desglose de Servicios</h6>
        @if(isset($plantillas) && $plantillas->count() > 0)
        <div class="d-flex align-items-center gap-2">
          <select id="selectPlantilla" class="form-select form-select-sm" style="min-width:180px;">
            <option value="">Cargar plantilla...</option>
            @foreach($plantillas as $pt)
              <option value="{{ $pt->id }}">{{ $pt->nombre }}</option>
            @endforeach
          </select>
          <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCargarPlantilla">
            <i data-lucide="download" style="width:14px;height:14px;" class="me-1"></i>Cargar
          </button>
        </div>
        @endif
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0" id="itemsTable">
            <thead>
              <tr>
                <th style="width:45%">Descripción</th>
                <th class="text-end" style="width:15%">Cantidad</th>
                <th class="text-end" style="width:20%">Precio Unit.</th>
                <th class="text-end" style="width:15%">Total</th>
                <th style="width:5%"></th>
              </tr>
            </thead>
            <tbody id="itemsTbody">
              @foreach($cotizacion->items as $item)
              <tr class="item-row" data-id="{{ $item->id }}">
                <td>{{ $item->descripcion }}</td>
                <td class="text-end">{{ number_format($item->cantidad, 2) }}</td>
                <td class="text-end">${{ number_format($item->precio_unitario, 2) }}</td>
                <td class="text-end fw-medium">${{ number_format($item->total, 2) }}</td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item"
                          data-id="{{ $item->id }}" title="Eliminar">
                    <i data-lucide="x" style="width:13px;height:13px;"></i>
                  </button>
                </td>
              </tr>
              @endforeach
              @if($cotizacion->items->isEmpty())
              <tr id="emptyRow">
                <td colspan="5" class="text-center text-muted py-3 small">
                  Aún no hay servicios. Agrega el primero abajo.
                </td>
              </tr>
              @endif
            </tbody>
            <!-- Add item row -->
            <tfoot>
              <tr class="add-item-row" id="addItemRow">
                <td>
                  <input type="text" id="newDesc" class="form-control form-control-sm"
                         placeholder="Descripción del servicio...">
                </td>
                <td>
                  <input type="number" id="newCantidad" class="form-control form-control-sm text-end"
                         value="1" min="0.01" step="0.01" style="min-width:70px;">
                </td>
                <td>
                  <input type="number" id="newPrecio" class="form-control form-control-sm text-end"
                         value="" min="0" step="0.01" placeholder="0.00" style="min-width:90px;">
                </td>
                <td class="text-end">
                  <span id="previewTotal" class="fw-medium text-muted">$0.00</span>
                </td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-success" id="btnAddItem" title="Añadir">
                    <i data-lucide="plus" style="width:13px;height:13px;"></i>
                  </button>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div><!-- /left col -->

  <!-- ── RIGHT COLUMN ── -->
  <div class="col-xl-4">

    <!-- Resumen financiero (dark card) -->
    <div class="card editor-dark-card grid-margin">
      <div class="card-body p-4">
        <p class="text-uppercase small mb-3" style="letter-spacing:.1em;color:#8b909a;">Resumen Financiero</p>

        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Subtotal</span>
          <span id="summSubtotal" class="fw-medium">${{ number_format($cotizacion->subtotal, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span class="text-muted">IVA (<span id="summIvaPct">{{ $cotizacion->iva_porcentaje }}</span>%)</span>
          <span id="summIva">${{ number_format($cotizacion->iva_monto, 2) }}</span>
        </div>

        <hr style="border-color:#2e3340;">

        <div class="mt-3">
          <p class="total-label mb-1">Total Inversión</p>
          <p class="total-amount mb-0" id="summTotal">${{ number_format($cotizacion->total, 2) }}</p>
        </div>
      </div>
    </div>

    <!-- Compartir link -->
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">Compartir Cotización</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label small text-muted">Enlace público</label>
          <div class="input-group">
            <input type="text" class="form-control form-control-sm font-monospace"
                   id="publicLink" value="{{ $cotizacion->public_url }}" readonly>
            <button class="btn btn-outline-secondary btn-sm" type="button" id="btnCopyLink" title="Copiar">
              <i data-lucide="copy" style="width:14px;height:14px;"></i>
            </button>
          </div>
          <p class="text-muted x-small mt-1">Cualquiera con este enlace puede ver la cotización sin iniciar sesión.</p>
        </div>

        <div class="d-grid gap-2">
          <a href="{{ $cotizacion->whatsapp_url }}" target="_blank"
             class="btn btn-success btn-sm d-flex align-items-center justify-content-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
            </svg>
            Enviar por WhatsApp
          </a>
          <a href="{{ $cotizacion->email_url }}"
             class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-2">
            <i data-lucide="mail" style="width:14px;height:14px;"></i>
            Enviar por Email
          </a>
        </div>
      </div>
    </div>

    <!-- Quick info -->
    @if($cotizacion->aprobado_at || $cotizacion->rechazado_at || $cotizacion->visto_at)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">Actividad del Cliente</h6>
      </div>
      <div class="card-body small">
        @if($cotizacion->visto_at)
        <div class="d-flex align-items-center gap-2 mb-2">
          <i data-lucide="eye" style="width:14px;height:14px;" class="text-info flex-shrink-0"></i>
          <span>Vista: {{ $cotizacion->visto_at->format('d/m/Y H:i') }}</span>
        </div>
        @endif
        @if($cotizacion->aprobado_at)
        <div class="d-flex align-items-center gap-2 mb-2">
          <i data-lucide="check-circle" style="width:14px;height:14px;" class="text-success flex-shrink-0"></i>
          <span>Aprobada: {{ $cotizacion->aprobado_at->format('d/m/Y H:i') }}
            @if($cotizacion->aprobado_nombre)por {{ $cotizacion->aprobado_nombre }}@endif
          </span>
        </div>
        @endif
        @if($cotizacion->rechazado_at)
        <div class="d-flex align-items-start gap-2 mb-2">
          <i data-lucide="x-circle" style="width:14px;height:14px;" class="text-danger flex-shrink-0 mt-1"></i>
          <span>Rechazada: {{ $cotizacion->rechazado_at->format('d/m/Y H:i') }}<br>
            <em class="text-muted">{{ $cotizacion->razon_rechazo }}</em>
          </span>
        </div>
        @endif
      </div>
    </div>
    @endif

  </div><!-- /right col -->

</div><!-- /row -->
@endsection

@push('scripts')
<script>
const COTIZACION_ID = {{ $cotizacion->id }};
const BASE_URL      = '{{ url("admin/cotizaciones") }}';
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;

// ── Preview total while typing ──
function updatePreview() {
  const c = parseFloat(document.getElementById('newCantidad').value) || 0;
  const p = parseFloat(document.getElementById('newPrecio').value) || 0;
  document.getElementById('previewTotal').textContent = '$' + fmtMoney(c * p);
}
document.getElementById('newCantidad').addEventListener('input', updatePreview);
document.getElementById('newPrecio').addEventListener('input', updatePreview);

// ── Format helper ──
function fmtMoney(val) {
  return parseFloat(val).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── Update summary cards ──
function updateSummary(data) {
  document.getElementById('summSubtotal').textContent = '$' + fmtMoney(data.subtotal);
  document.getElementById('summIva').textContent      = '$' + fmtMoney(data.iva_monto);
  document.getElementById('summTotal').textContent    = '$' + fmtMoney(data.total);
}

// ── Add Item ──
document.getElementById('btnAddItem').addEventListener('click', function () {
  const desc    = document.getElementById('newDesc').value.trim();
  const cant    = document.getElementById('newCantidad').value;
  const precio  = document.getElementById('newPrecio').value;

  if (!desc) { document.getElementById('newDesc').focus(); return; }
  if (!precio || parseFloat(precio) < 0) { document.getElementById('newPrecio').focus(); return; }

  this.disabled = true;

  fetch(`${BASE_URL}/${COTIZACION_ID}/items`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': CSRF,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ descripcion: desc, cantidad: cant, precio_unitario: precio }),
  })
  .then(r => r.json())
  .then(data => {
    // Remove empty row if present
    const empty = document.getElementById('emptyRow');
    if (empty) empty.remove();

    const item  = data.item;
    const total = parseFloat(item.cantidad) * parseFloat(item.precio_unitario);
    const tr    = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.id = item.id;
    tr.innerHTML = `
      <td>${escHtml(item.descripcion)}</td>
      <td class="text-end">${fmtMoney(item.cantidad)}</td>
      <td class="text-end">$${fmtMoney(item.precio_unitario)}</td>
      <td class="text-end fw-medium">$${fmtMoney(total)}</td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item" data-id="${item.id}" title="Eliminar">
          <i data-lucide="x" style="width:13px;height:13px;"></i>
        </button>
      </td>`;
    document.getElementById('itemsTbody').appendChild(tr);
    lucide.createIcons();

    // Reset add-row
    document.getElementById('newDesc').value    = '';
    document.getElementById('newCantidad').value = 1;
    document.getElementById('newPrecio').value   = '';
    document.getElementById('previewTotal').textContent = '$0.00';

    updateSummary(data);
  })
  .catch(() => alert('Error al agregar el servicio.'))
  .finally(() => { this.disabled = false; });
});

// ── Delete Item (delegated) ──
document.getElementById('itemsTbody').addEventListener('click', function (e) {
  const btn = e.target.closest('.btn-delete-item');
  if (!btn) return;

  if (!confirm('¿Eliminar este servicio?')) return;
  const itemId = btn.dataset.id;
  btn.disabled = true;

  fetch(`${BASE_URL}/${COTIZACION_ID}/items/${itemId}`, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
  })
  .then(r => r.json())
  .then(data => {
    const row = document.querySelector(`.item-row[data-id="${itemId}"]`);
    if (row) row.remove();

    if (document.querySelectorAll('#itemsTbody .item-row').length === 0) {
      const tr = document.createElement('tr');
      tr.id = 'emptyRow';
      tr.innerHTML = '<td colspan="5" class="text-center text-muted py-3 small">Aún no hay servicios. Agrega el primero abajo.</td>';
      document.getElementById('itemsTbody').appendChild(tr);
    }

    updateSummary(data);
  })
  .catch(() => alert('Error al eliminar.'))
  .finally(() => { btn.disabled = false; });
});

// ── Copy link ──
document.getElementById('btnCopyLink').addEventListener('click', function () {
  const input = document.getElementById('publicLink');
  navigator.clipboard.writeText(input.value).then(() => {
    const icon = this.querySelector('[data-lucide]');
    icon.setAttribute('data-lucide', 'check');
    lucide.createIcons();
    setTimeout(() => {
      icon.setAttribute('data-lucide', 'copy');
      lucide.createIcons();
    }, 2000);
  });
});

// ── Load proyectos on cliente change ──
const proyectosUrl = '{{ url("admin/cotizaciones-clientes") }}';
document.getElementById('clienteSelect').addEventListener('change', function () {
  const clienteId = this.value;
  const sel = document.getElementById('proyectoSelect');
  sel.innerHTML = '<option value="">Sin proyecto asociado</option>';
  if (!clienteId) return;

  fetch(`${proyectosUrl}/${clienteId}/proyectos`, {
    headers: { 'X-CSRF-TOKEN': CSRF }
  })
  .then(r => r.json())
  .then(data => {
    data.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id;
      opt.textContent = p.nombre;
      sel.appendChild(opt);
    });
  });
});

function escHtml(str) {
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Cargar Plantilla ──
const btnCargarPlantilla = document.getElementById('btnCargarPlantilla');
if (btnCargarPlantilla) {
  btnCargarPlantilla.addEventListener('click', function () {
    const plantillaId = document.getElementById('selectPlantilla').value;
    if (!plantillaId) { alert('Selecciona una plantilla primero.'); return; }

    if (!confirm('¿Cargar los ítems de esta plantilla? Se agregarán a los existentes.')) return;

    this.disabled = true;
    fetch(`/admin/plantillas-cotizacion/${plantillaId}/items`, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(async items => {
      for (const item of items) {
        await postItem(item.descripcion, item.cantidad, item.precio_unitario);
      }
      document.getElementById('selectPlantilla').value = '';
    })
    .catch(() => alert('Error al cargar la plantilla.'))
    .finally(() => { this.disabled = false; });
  });
}

async function postItem(desc, cant, precio) {
  const btn = document.getElementById('btnAddItem');
  const response = await fetch(`${BASE_URL}/${COTIZACION_ID}/items`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ descripcion: desc, cantidad: cant, precio_unitario: precio }),
  });
  const data = await response.json();
  if (data.item) {
    const empty = document.getElementById('emptyRow');
    if (empty) empty.remove();
    const item  = data.item;
    const total = parseFloat(item.cantidad) * parseFloat(item.precio_unitario);
    const tr    = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.id = item.id;
    tr.innerHTML = `
      <td>${escHtml(item.descripcion)}</td>
      <td class="text-end">${fmtMoney(item.cantidad)}</td>
      <td class="text-end">$${fmtMoney(item.precio_unitario)}</td>
      <td class="text-end fw-medium">$${fmtMoney(total)}</td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item" data-id="${item.id}" title="Eliminar">
          <i data-lucide="x" style="width:13px;height:13px;"></i>
        </button>
      </td>`;
    document.getElementById('itemsTbody').appendChild(tr);
    lucide.createIcons();
    updateSummary(data);
  }
}
</script>
@endpush
