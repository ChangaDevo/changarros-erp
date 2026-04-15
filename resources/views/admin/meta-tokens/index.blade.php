@extends('admin.layouts.app')

@section('title', 'Tokens de Meta')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <h4 class="mb-0 fw-bold">Tokens de Meta (Facebook & Instagram)</h4>
    <p class="text-muted small mb-0">Gestiona los access tokens por cliente para publicación automática</p>
  </div>
  <button class="btn btn-primary btn-sm" id="btnNuevoToken">
    <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i> Nuevo Token
  </button>
</div>

{{-- Info sobre el scheduler --}}
<div class="alert alert-info d-flex align-items-start gap-2 py-2 mb-3">
  <i data-lucide="clock" style="width:16px;height:16px;flex-shrink:0;margin-top:2px;"></i>
  <div class="small">
    <strong>Publicación automática:</strong> Cada hora el sistema revisa publicaciones con estado <strong>Aprobado</strong> cuya fecha ya llegó y las publica automáticamente en Meta.
    Para activar el scheduler en producción agrega al cron del servidor:<br>
    <code class="user-select-all">* * * * * php {{ base_path() }}/artisan schedule:run >> /dev/null 2>&1</code>
  </div>
</div>

@if($tokens->isEmpty())
  <div class="card">
    <div class="card-body text-center py-5 text-muted">
      <i data-lucide="key-round" style="width:40px;height:40px;" class="mb-3 d-block mx-auto opacity-50"></i>
      <p class="mb-0">No hay tokens configurados. Agrega uno para habilitar la publicación automática.</p>
    </div>
  </div>
@else
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th class="ps-3">Cliente</th>
            <th>Nombre / Label</th>
            <th>Plataforma</th>
            <th>Page ID</th>
            <th>Estado</th>
            <th>Última verificación</th>
            <th class="text-end pe-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($tokens as $token)
          <tr data-token-id="{{ $token->id }}">
            <td class="ps-3 fw-semibold small">{{ $token->cliente->nombre_empresa }}</td>
            <td class="small">{{ $token->nombre }}</td>
            <td>
              <span class="badge bg-{{ $token->plataforma_badge }}">
                {{ $token->plataforma_label }}
              </span>
              @if(!$token->activo)
                <span class="badge bg-secondary ms-1">Inactivo</span>
              @endif
            </td>
            <td class="small font-monospace">{{ $token->page_id }}</td>
            <td>
              @if($token->estado_verificacion === 'ok')
                <span class="badge bg-success">✓ OK</span>
              @elseif($token->estado_verificacion === 'error')
                <span class="badge bg-danger">✗ Error</span>
              @else
                <span class="badge bg-secondary">Sin verificar</span>
              @endif
              @if($token->expires_at && $token->expires_at->isPast())
                <span class="badge bg-warning text-dark ms-1">Expirado</span>
              @endif
            </td>
            <td class="small text-muted">
              {{ $token->ultima_verificacion?->diffForHumans() ?? '—' }}
            </td>
            <td class="text-end pe-3">
              <button class="btn btn-sm btn-outline-secondary btnVerificar" data-id="{{ $token->id }}" title="Verificar token">
                <i data-lucide="wifi" style="width:13px;height:13px;"></i>
              </button>
              <button class="btn btn-sm btn-outline-primary btnEditar ms-1" data-id="{{ $token->id }}" title="Editar">
                <i data-lucide="pencil" style="width:13px;height:13px;"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger btnEliminar ms-1" data-id="{{ $token->id }}" title="Eliminar">
                <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

{{-- ======== MODAL CREAR / EDITAR ======== --}}
<div class="modal fade" id="modalToken" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTokenTitulo">Nuevo Token</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formToken">
        @csrf
        <input type="hidden" id="tok_id" value="">
        <input type="hidden" id="tok_method" value="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Cliente <span class="text-danger">*</span></label>
              <select class="form-select" id="tok_cliente_id" name="cliente_id" required>
                <option value="">— Seleccionar —</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}">{{ $c->nombre_empresa }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Plataforma <span class="text-danger">*</span></label>
              <select class="form-select" id="tok_plataforma" name="plataforma" required>
                <option value="">— Seleccionar —</option>
                <option value="facebook">📘 Facebook</option>
                <option value="instagram">📸 Instagram</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">Nombre / Label <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="tok_nombre" name="nombre" maxlength="100"
                placeholder="Ej: Facebook — Empresa Demo" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">
                Facebook Page ID <span class="text-danger">*</span>
                <span class="text-muted fw-normal">(ID numérico de la página)</span>
              </label>
              <input type="text" class="form-control font-monospace" id="tok_page_id" name="page_id"
                placeholder="708216695701780" required>
            </div>
            <div class="col-md-6" id="wrap_ig_account">
              <label class="form-label fw-semibold small">
                Instagram Account ID
                <span class="text-muted fw-normal">(solo Instagram)</span>
              </label>
              <div class="input-group">
                <input type="text" class="form-control font-monospace" id="tok_ig_account_id" name="ig_account_id"
                  placeholder="ID de cuenta Instagram Business">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnDetectarIg" title="Detectar automáticamente">
                  <i data-lucide="search" style="width:13px;height:13px;" class="me-1"></i> Detectar
                </button>
              </div>
              <div class="form-text">Usa "Detectar" para obtenerlo automáticamente del Page ID.</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">
                Access Token <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <input type="password" class="form-control font-monospace" id="tok_access_token" name="access_token"
                  placeholder="EAAeZC..." required>
                <button type="button" class="btn btn-outline-secondary" id="btnToggleToken" title="Mostrar/ocultar">
                  <i data-lucide="eye" style="width:14px;height:14px;" id="eyeIcon"></i>
                </button>
              </div>
              <div class="form-text">El token se guarda encriptado en la base de datos.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Fecha de expiración <span class="text-muted fw-normal">(opcional)</span></label>
              <input type="date" class="form-control" id="tok_expires_at" name="expires_at">
            </div>
            <div class="col-md-6 d-flex align-items-end pb-1">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="tok_activo" name="activo" value="1" checked>
                <label class="form-check-label fw-semibold small" for="tok_activo">Token activo</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-outline-danger btn-sm me-auto" id="btnEliminarToken" style="display:none">
            <i data-lucide="trash-2" style="width:14px;height:14px;" class="me-1"></i> Eliminar
          </button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarToken">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // ---- Modal helpers ----
  function resetModal() {
    document.getElementById('formToken').reset();
    document.getElementById('tok_id').value = '';
    document.getElementById('tok_method').value = 'POST';
    document.getElementById('modalTokenTitulo').textContent = 'Nuevo Token';
    document.getElementById('btnEliminarToken').style.display = 'none';
    document.getElementById('tok_activo').checked = true;
  }

  function abrirModalNuevo() {
    resetModal();
    new bootstrap.Modal(document.getElementById('modalToken')).show();
    lucide.createIcons();
  }

  function abrirModalEditar(id) {
    resetModal();
    fetch(`{{ url('admin/meta-tokens') }}/${id}`, { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalTokenTitulo').textContent = 'Editar Token';
        document.getElementById('tok_id').value               = data.id;
        document.getElementById('tok_method').value           = 'PUT';
        document.getElementById('tok_cliente_id').value       = data.cliente_id;
        document.getElementById('tok_nombre').value           = data.nombre;
        document.getElementById('tok_plataforma').value       = data.plataforma;
        document.getElementById('tok_page_id').value          = data.page_id;
        document.getElementById('tok_ig_account_id').value    = data.ig_account_id || '';
        document.getElementById('tok_access_token').value     = data.access_token;
        document.getElementById('tok_expires_at').value       = data.expires_at || '';
        document.getElementById('tok_activo').checked         = data.activo;
        document.getElementById('btnEliminarToken').style.display = '';
        new bootstrap.Modal(document.getElementById('modalToken')).show();
        lucide.createIcons();
      });
  }

  document.getElementById('btnNuevoToken').addEventListener('click', abrirModalNuevo);

  // Delegación de clics en la tabla
  document.addEventListener('click', function(e) {
    const btnEditar   = e.target.closest('.btnEditar');
    const btnEliminar = e.target.closest('.btnEliminar');
    const btnVerif    = e.target.closest('.btnVerificar');
    if (btnEditar)   abrirModalEditar(btnEditar.dataset.id);
    if (btnVerif)    verificarToken(btnVerif.dataset.id, btnVerif);
    if (btnEliminar) eliminarToken(btnEliminar.dataset.id);
  });

  // Toggle visibilidad del token
  document.getElementById('btnToggleToken').addEventListener('click', function() {
    const input = document.getElementById('tok_access_token');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
      input.type = 'text';
      icon.setAttribute('data-lucide', 'eye-off');
    } else {
      input.type = 'password';
      icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
  });

  // Detectar IG Account ID
  document.getElementById('btnDetectarIg').addEventListener('click', async function() {
    const pageId = document.getElementById('tok_page_id').value.trim();
    const token  = document.getElementById('tok_access_token').value.trim();
    if (!pageId || !token) { alert('Ingresa el Page ID y el Access Token primero.'); return; }

    this.disabled = true;
    this.textContent = 'Detectando...';

    try {
      const resp = await fetch('{{ route('admin.meta-tokens.detectar-ig') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({ page_id: pageId, access_token: token }),
      });
      const data = await resp.json();
      if (data.ok) {
        document.getElementById('tok_ig_account_id').value = data.ig_account_id;
        alert('✓ ID detectado: ' + data.ig_account_id);
      } else {
        alert('No se detectó: ' + (data.mensaje || 'Sin respuesta'));
      }
    } finally {
      this.disabled = false;
      this.innerHTML = '<i data-lucide="search" style="width:13px;height:13px;" class="me-1"></i> Detectar';
      lucide.createIcons();
    }
  });

  // Submit guardar
  document.getElementById('formToken').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id     = document.getElementById('tok_id').value;
    const method = document.getElementById('tok_method').value;
    const url    = id ? `{{ url('admin/meta-tokens') }}/${id}` : `{{ route('admin.meta-tokens.store') }}`;

    const formData = new FormData(this);
    if (method === 'PUT') formData.set('_method', 'PUT');
    // Checkbox activo
    formData.set('activo', document.getElementById('tok_activo').checked ? '1' : '0');

    const btn = document.getElementById('btnGuardarToken');
    btn.disabled = true; btn.textContent = 'Guardando...';

    try {
      const resp = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData,
      });
      const data = await resp.json();
      if (resp.ok && data.ok) {
        bootstrap.Modal.getInstance(document.getElementById('modalToken')).hide();
        window.location.reload();
      } else {
        const msgs = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || JSON.stringify(data));
        alert('Error:\n' + msgs);
      }
    } catch (err) {
      alert('Error de conexión: ' + err.message);
    } finally {
      btn.disabled = false; btn.textContent = 'Guardar';
    }
  });

  // Eliminar (desde modal)
  document.getElementById('btnEliminarToken').addEventListener('click', async function() {
    if (!confirm('¿Eliminar este token?')) return;
    const id   = document.getElementById('tok_id').value;
    const resp = await fetch(`{{ url('admin/meta-tokens') }}/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    if (resp.ok) { window.location.reload(); }
  });

  // Verificar token
  async function verificarToken(id, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
      const resp = await fetch(`{{ url('admin/meta-tokens') }}/${id}/verificar`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      });
      const data = await resp.json();
      if (data.ok) {
        alert(`✓ Token válido\nNombre de página: ${data.nombre}\nID: ${data.id}`);
      } else {
        alert('✗ Token inválido: ' + (data.mensaje || data.message));
      }
      window.location.reload();
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i data-lucide="wifi" style="width:13px;height:13px;"></i>';
      lucide.createIcons();
    }
  }
})();
</script>
@endpush
