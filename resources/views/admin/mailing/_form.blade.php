{{-- Variables: $campana (new o existing), $clientes, $action, $method --}}
<div class="row g-4">

  {{-- Columna izquierda: formulario --}}
  <div class="col-xl-7">

    {{-- Info básica --}}
    <div class="card mb-4">
      <div class="card-body">
        <h6 class="card-title fw-semibold mb-3">
          <i data-lucide="info" style="width:16px;height:16px;" class="me-1 text-primary"></i>
          Información de la campaña
        </h6>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Cliente <span class="text-danger">*</span></label>
            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror"
                    {{ isset($campana->id) ? 'disabled' : '' }}>
              <option value="">— Seleccionar —</option>
              @foreach($clientes as $c)
                <option value="{{ $c->id }}" {{ old('cliente_id', $campana->cliente_id ?? '') == $c->id ? 'selected' : '' }}>
                  {{ $c->nombre_empresa }}
                </option>
              @endforeach
            </select>
            @if(isset($campana->id))
              <input type="hidden" name="cliente_id" value="{{ $campana->cliente_id }}">
            @endif
            @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nombre interno <span class="text-danger">*</span></label>
            <input type="text" name="titulo" value="{{ old('titulo', $campana->titulo ?? '') }}"
                   class="form-control @error('titulo') is-invalid @enderror"
                   placeholder="Ej. Newsletter Mayo 2025">
            @error('titulo') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-12">
            <label class="form-label small fw-semibold">Asunto del correo <span class="text-danger">*</span></label>
            <input type="text" name="asunto" value="{{ old('asunto', $campana->asunto ?? '') }}"
                   class="form-control @error('asunto') is-invalid @enderror"
                   placeholder="Ej. Hola {nombre}, tenemos novedades para ti">
            <small class="text-muted">Puedes usar variables como <code>{nombre}</code>, <code>{empresa}</code></small>
            @error('asunto') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Nombre del remitente <span class="text-danger">*</span></label>
            <input type="text" name="remitente_nombre" value="{{ old('remitente_nombre', $campana->remitente_nombre ?? '') }}"
                   class="form-control @error('remitente_nombre') is-invalid @enderror"
                   placeholder="Ej. Comunicación Espiral">
            @error('remitente_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-semibold">Correo remitente <span class="text-danger">*</span></label>
            <input type="email" name="remitente_email" value="{{ old('remitente_email', $campana->remitente_email ?? '') }}"
                   class="form-control @error('remitente_email') is-invalid @enderror"
                   placeholder="comunicacion@cliente.com">
            @error('remitente_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- CSV --}}
    <div class="card mb-4">
      <div class="card-body">
        <h6 class="card-title fw-semibold mb-1">
          <i data-lucide="users" style="width:16px;height:16px;" class="me-1 text-success"></i>
          Base de datos de contactos (CSV)
          @if(isset($campana->id)) <span class="badge bg-secondary ms-2">{{ $campana->total_contactos }} actuales</span> @endif
        </h6>
        <p class="text-muted small mb-3">
          El CSV debe tener columnas: <code>email</code>, <code>nombre</code>, <code>apellido</code>, <code>empresa</code> (otras columnas son accesibles como variables).
        </p>

        <div class="mb-2">
          <input type="file" name="csv" id="csvInput" accept=".csv,.txt"
                 class="form-control @error('csv') is-invalid @enderror"
                 onchange="previewCsv(this)">
          @if(isset($campana->id))
            <small class="text-muted">Opcional: sube un nuevo CSV para reemplazar los contactos actuales</small>
          @endif
          @error('csv') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Preview CSV --}}
        <div id="csvPreview" class="d-none mt-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold small">Vista previa del CSV</span>
            <span id="csvCount" class="badge bg-success"></span>
          </div>
          <div class="table-responsive" style="max-height:200px;overflow-y:auto;">
            <table class="table table-sm table-bordered mb-0 small" id="csvTable">
              <thead class="table-dark" id="csvHead"></thead>
              <tbody id="csvBody"></tbody>
            </table>
          </div>
        </div>

        {{-- Descargar plantilla CSV --}}
        <div class="mt-3">
          <a href="#" onclick="descargarPlantilla()" class="btn btn-sm btn-outline-secondary">
            <i data-lucide="download" style="width:13px;height:13px;" class="me-1"></i>
            Descargar plantilla CSV
          </a>
        </div>
      </div>
    </div>

    {{-- HTML Editor --}}
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="card-title fw-semibold mb-0">
            <i data-lucide="code" style="width:16px;height:16px;" class="me-1 text-warning"></i>
            Diseño del correo (HTML)
          </h6>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertarVariable('{nombre}')">
              + {nombre}
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertarVariable('{apellido}')">
              + {apellido}
            </button>
            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertarVariable('{empresa}')">
              + {empresa}
            </button>
          </div>
        </div>

        <div class="mb-2">
          <div class="d-flex gap-2 mb-2">
            <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnEditor" onclick="mostrarTab('editor')">
              <i data-lucide="code-2" style="width:12px;height:12px;" class="me-1"></i> Editor
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreview" onclick="actualizarPreview()">
              <i data-lucide="eye" style="width:12px;height:12px;" class="me-1"></i> Preview en vivo
            </button>
          </div>

          <div id="tabEditor">
            <textarea name="cuerpo_html" id="htmlEditor" rows="22"
                      class="form-control font-monospace @error('cuerpo_html') is-invalid @enderror"
                      style="font-size:12px;resize:vertical;"
                      placeholder="Pega aquí tu HTML del correo...">{{ old('cuerpo_html', $campana->cuerpo_html ?? '') }}</textarea>
            @error('cuerpo_html') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div id="tabPreview" class="d-none">
            <div class="border rounded" style="height:500px;overflow:hidden;">
              <iframe id="previewFrame" style="width:100%;height:100%;border:none;"></iframe>
            </div>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-2">
          <small class="text-muted flex-grow-1">
            Variables disponibles: <code>{nombre}</code> <code>{apellido}</code> <code>{nombre_completo}</code> <code>{empresa}</code> <code>{email}</code>
          </small>
        </div>
      </div>
    </div>

  </div>

  {{-- Columna derecha: ayuda --}}
  <div class="col-xl-5">
    <div class="card mb-4 border-primary" style="border-width:2px!important;">
      <div class="card-body">
        <h6 class="fw-semibold mb-3 text-primary">
          <i data-lucide="zap" style="width:16px;height:16px;" class="me-1"></i>
          Variables disponibles
        </h6>
        <table class="table table-sm small mb-0">
          <thead><tr><th>Variable</th><th>Descripción</th></tr></thead>
          <tbody>
            <tr><td><code>{nombre}</code></td><td>Nombre del contacto</td></tr>
            <tr><td><code>{apellido}</code></td><td>Apellido del contacto</td></tr>
            <tr><td><code>{nombre_completo}</code></td><td>Nombre + Apellido</td></tr>
            <tr><td><code>{empresa}</code></td><td>Empresa del contacto</td></tr>
            <tr><td><code>{email}</code></td><td>Correo del contacto</td></tr>
            <tr><td><code>{columna_csv}</code></td><td>Cualquier columna extra de tu CSV</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">
          <i data-lucide="file-text" style="width:16px;height:16px;" class="me-1 text-success"></i>
          Formato del CSV
        </h6>
        <pre class="bg-light rounded p-3 small mb-2" style="font-size:11px;">nombre,apellido,email,empresa
Juan,Pérez,juan@mail.com,Empresa A
María,López,maria@mail.com,Empresa B</pre>
        <p class="text-muted small mb-0">La primera fila debe ser el encabezado. Las columnas <code>email</code> y <code>nombre</code> son requeridas.</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">
          <i data-lucide="mail" style="width:16px;height:16px;" class="me-1 text-warning"></i>
          Ejemplo de HTML personalizado
        </h6>
        <pre class="bg-light rounded p-3 small" style="font-size:11px;">&lt;h1&gt;Hola, {nombre}!&lt;/h1&gt;
&lt;p&gt;Tenemos novedades para
{empresa}.&lt;/p&gt;
&lt;a href="https://..."&gt;
  Ver más
&lt;/a&gt;</pre>
        <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2"
                onclick="cargarEjemplo()">
          Cargar HTML de ejemplo
        </button>
      </div>
    </div>
  </div>

</div>

<div class="d-flex gap-2 mt-3">
  <button type="submit" class="btn btn-primary">
    <i data-lucide="save" style="width:15px;height:15px;" class="me-1"></i>
    {{ isset($campana->id) ? 'Guardar cambios' : 'Crear campaña' }}
  </button>
  <a href="{{ route('admin.mailing.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>

@push('scripts')
<script>
const PREVIEW_ROUTE = "{{ route('admin.mailing.preview-live') }}";
const CSRF = "{{ csrf_token() }}";

// ── Tabs Editor / Preview ──────────────────────────────
function mostrarTab(tab) {
  document.getElementById('tabEditor').classList.toggle('d-none', tab !== 'editor');
  document.getElementById('tabPreview').classList.toggle('d-none', tab === 'editor');
  document.getElementById('btnEditor').classList.toggle('active', tab === 'editor');
  document.getElementById('btnPreview').classList.toggle('active', tab !== 'editor');
}

async function actualizarPreview() {
  mostrarTab('preview');
  const html = document.getElementById('htmlEditor').value;
  const res  = await fetch(PREVIEW_ROUTE, {
    method: 'POST',
    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
    body: JSON.stringify({cuerpo_html: html}),
  });
  const texto = await res.text();
  const iframe = document.getElementById('previewFrame');
  iframe.srcdoc = texto;
}

// ── Insertar variable en el editor ────────────────────
function insertarVariable(v) {
  const el  = document.getElementById('htmlEditor');
  const pos = el.selectionStart;
  el.value  = el.value.substring(0, pos) + v + el.value.substring(el.selectionEnd);
  el.focus();
  el.selectionStart = el.selectionEnd = pos + v.length;
}

// ── Preview CSV ───────────────────────────────────────
function previewCsv(input) {
  const file = input.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    const lines = e.target.result.trim().split('\n');
    if (lines.length < 2) return;

    const headers = lines[0].split(',').map(h => h.trim());
    const rows    = lines.slice(1, 6); // máx 5 filas preview

    const thead = document.getElementById('csvHead');
    const tbody = document.getElementById('csvBody');

    thead.innerHTML = '<tr>' + headers.map(h => `<th>${h}</th>`).join('') + '</tr>';
    tbody.innerHTML = rows.map(r => {
      const cols = r.split(',');
      return '<tr>' + cols.map(c => `<td>${c.trim()}</td>`).join('') + '</tr>';
    }).join('');

    document.getElementById('csvCount').textContent = (lines.length - 1) + ' contactos';
    document.getElementById('csvPreview').classList.remove('d-none');
  };
  reader.readAsText(file);
}

// ── Descargar plantilla CSV ───────────────────────────
function descargarPlantilla() {
  const csv = 'nombre,apellido,email,empresa\nJuan,Pérez,juan@ejemplo.com,Empresa Demo\nMaría,López,maria@ejemplo.com,Empresa B';
  const blob = new Blob([csv], {type: 'text/csv'});
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href = url; a.download = 'plantilla_contactos.csv'; a.click();
}

// ── Cargar HTML de ejemplo ───────────────────────────
function cargarEjemplo() {
  document.getElementById('htmlEditor').value = `<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
    .container { max-width:600px; margin:30px auto; background:#fff; border-radius:8px; overflow:hidden; }
    .header { background:#1a1a2e; color:#fff; padding:30px; text-align:center; }
    .header h1 { margin:0; font-size:24px; }
    .body { padding:30px; color:#333; line-height:1.6; }
    .cta { display:inline-block; background:#e94560; color:#fff; padding:12px 28px;
           border-radius:6px; text-decoration:none; font-weight:bold; margin-top:20px; }
    .footer { background:#f8f9fa; padding:20px; text-align:center; font-size:12px; color:#999; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>ESPIRAL ERP</h1>
    </div>
    <div class="body">
      <h2>Hola, {nombre}! 👋</h2>
      <p>Tenemos novedades importantes para <strong>{empresa}</strong>.</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      <a href="#" class="cta">Ver más detalles</a>
    </div>
    <div class="footer">
      Este correo fue enviado a {email}. © {{ date('Y') }} ESPIRAL ERP
    </div>
  </div>
</body>
</html>`;
}
</script>
@endpush
