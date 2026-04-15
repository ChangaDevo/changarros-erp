@extends('admin.layouts.app')

@section('title', 'Calendario de Publicaciones')

@push('plugin-styles')
  <style>
    #calendar {
      min-height: 600px;
    }
    .fc .fc-event {
      cursor: pointer;
      font-size: 0.78rem;
      border-radius: 4px;
      padding: 1px 4px;
    }
    .fc .fc-daygrid-day:hover {
      background: rgba(91,71,251,0.04);
      cursor: pointer;
    }
    .legend-dot {
      width: 12px; height: 12px;
      border-radius: 50%;
      display: inline-block;
    }
    .filter-bar .form-select, .filter-bar .form-control {
      font-size: 0.85rem;
    }
  </style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <h4 class="mb-0 fw-bold">Calendario de Publicaciones</h4>
    <p class="text-muted small mb-0">Gestiona el contenido propuesto a tus clientes</p>
  </div>
  <button class="btn btn-primary btn-sm" id="btnNuevaPublicacion">
    <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i> Nueva Publicación
  </button>
</div>

{{-- Filtros --}}
<div class="card mb-3">
  <div class="card-body py-2 filter-bar">
    <div class="row g-2 align-items-center">
      <div class="col-sm-4 col-md-3">
        <select id="filtro_cliente" class="form-select form-select-sm">
          <option value="">Todos los clientes</option>
          @foreach($clientes as $c)
            <option value="{{ $c->id }}">{{ $c->nombre_empresa }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-sm-4 col-md-3">
        <select id="filtro_red" class="form-select form-select-sm">
          <option value="">Todas las redes</option>
          <option value="instagram">📸 Instagram</option>
          <option value="facebook">📘 Facebook</option>
          <option value="tiktok">🎵 TikTok</option>
          <option value="twitter">🐦 Twitter / X</option>
          <option value="linkedin">💼 LinkedIn</option>
          <option value="youtube">▶️ YouTube</option>
        </select>
      </div>
      <div class="col-sm-4 col-md-3">
        <select id="filtro_estado" class="form-select form-select-sm">
          <option value="">Todos los estados</option>
          <option value="borrador">Borrador</option>
          <option value="propuesto">Pendiente aprobación</option>
          <option value="aprobado">Aprobado</option>
          <option value="rechazado">Rechazado</option>
          <option value="publicado">Publicado</option>
        </select>
      </div>
      <div class="col-md-3 d-none d-md-flex gap-3 align-items-center ps-3">
        <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#6c757d"></span> Borrador</span>
        <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#fd7e14"></span> Pendiente</span>
        <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#198754"></span> Aprobado</span>
        <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#dc3545"></span> Rechazado</span>
        <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#0d6efd"></span> Publicado</span>
      </div>
    </div>
  </div>
</div>

{{-- Calendario --}}
<div class="card">
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

{{-- ======== MODAL CREAR / EDITAR ======== --}}
<div class="modal fade" id="modalPublicacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPublicacionTitulo">Nueva Publicación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPublicacion" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="pub_id" name="pub_id" value="">
        <input type="hidden" id="pub_method" name="_method" value="POST">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Cliente <span class="text-danger">*</span></label>
              <select class="form-select" id="pub_cliente_id" name="cliente_id" required>
                <option value="">— Seleccionar —</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}">{{ $c->nombre_empresa }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Red Social <span class="text-danger">*</span></label>
              <select class="form-select" id="pub_red_social" name="red_social" required>
                <option value="">— Seleccionar —</option>
                <option value="instagram">📸 Instagram</option>
                <option value="facebook">📘 Facebook</option>
                <option value="tiktok">🎵 TikTok</option>
                <option value="twitter">🐦 Twitter / X</option>
                <option value="linkedin">💼 LinkedIn</option>
                <option value="youtube">▶️ YouTube</option>
              </select>
            </div>

            {{-- Archivo (imagen o video) + botón IA --}}
            <div class="col-12">
              <label class="form-label fw-semibold small">
                Imagen o Video
                <span class="text-muted fw-normal">(jpg, png, gif, mp4, mov — máx 50MB)</span>
              </label>
              <div class="d-flex gap-2 align-items-start">
                <input type="file" class="form-control" id="pub_archivo" name="archivo"
                  accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/webm">
                <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" id="btnAnalizarIA" disabled
                  title="Selecciona un archivo primero">
                  <span id="iaSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                  <i data-lucide="sparkles" style="width:14px;height:14px;" class="me-1" id="iaIcon"></i>
                  Analizar con IA
                </button>
              </div>
              {{-- Preview --}}
              <div id="pub_archivo_preview" class="mt-2 d-none">
                <img id="pub_archivo_img" src="" class="img-thumbnail d-none" style="max-height:120px;">
                <video id="pub_archivo_video" src="" class="d-none rounded" style="max-height:120px;" controls></video>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="btnQuitarArchivo">Quitar</button>
              </div>
            </div>

            {{-- Sugerencia IA --}}
            <div class="col-12 d-none" id="wrap_ia_resultado">
              <div class="alert alert-primary py-2 px-3 mb-0">
                <div class="d-flex justify-content-between align-items-start">
                  <span class="fw-semibold small">
                    <i data-lucide="sparkles" style="width:13px;height:13px;" class="me-1"></i>
                    Sugerencia de IA
                  </span>
                  <span id="ia_tipo_contenido" class="badge bg-primary small"></span>
                </div>
                <p class="small mb-1 mt-1 fst-italic" id="ia_justificacion"></p>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Título interno <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="pub_titulo" name="titulo" maxlength="255" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold small">
                Copy / Descripción <span class="text-danger">*</span>
              </label>
              <textarea class="form-control" id="pub_descripcion" name="descripcion" rows="4" required placeholder="Texto del post..."></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Fecha y hora programada <span class="text-danger">*</span></label>
              <input type="datetime-local" class="form-control" id="pub_fecha" name="fecha_programada" required>
              <div id="pub_fecha_aviso" class="form-text text-warning d-none">
                <i data-lucide="alert-triangle" style="width:12px;height:12px;" class="me-1"></i>
                <span id="pub_fecha_aviso_texto"></span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Estado</label>
              <select class="form-select" id="pub_estado" name="estado">
                <option value="borrador">Borrador (solo admin)</option>
                <option value="propuesto">Propuesto al cliente</option>
                <option value="aprobado">Aprobado</option>
                <option value="rechazado">Rechazado</option>
                <option value="publicado">Publicado</option>
              </select>
            </div>

            {{-- Audiencia sugerida (campo oculto + display) --}}
            <input type="hidden" id="pub_audiencia" name="audiencia_sugerida">
            <div class="col-12 d-none" id="wrap_audiencia">
              <label class="form-label fw-semibold small">
                <i data-lucide="users" style="width:13px;height:13px;" class="me-1"></i>
                Audiencia sugerida por IA
              </label>
              <div class="alert alert-warning py-2 px-3 mb-0 small" id="audiencia_display"></div>
            </div>

            {{-- Audiencia al editar (guardada en BD) --}}
            <div class="col-12 d-none" id="wrap_audiencia_guardada">
              <label class="form-label fw-semibold small">
                <i data-lucide="users" style="width:13px;height:13px;" class="me-1"></i>
                Audiencia sugerida
              </label>
              <div class="alert alert-warning py-2 px-3 mb-0 small" id="audiencia_guardada_display"></div>
            </div>

            <div class="col-12 d-none" id="wrap_nota_cliente">
              <label class="form-label fw-semibold small" id="nota_cliente_label">Comentario del cliente</label>
              <div class="alert py-2 mb-0 small" id="nota_cliente_texto"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer gap-2">
          <button type="button" class="btn btn-outline-danger btn-sm me-auto" id="btnEliminarPublicacion" style="display:none">
            <i data-lucide="trash-2" style="width:14px;height:14px;" class="me-1"></i> Eliminar
          </button>
          <button type="button" class="btn btn-success btn-sm" id="btnMarcarPublicado" style="display:none">
            <i data-lucide="check-circle" style="width:14px;height:14px;" class="me-1"></i> Marcar como Publicado
          </button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm" id="btnGuardarPublicacion">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('plugin-scripts')
  <script src="{{ asset('build/plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('build/plugins/fullcalendar/index.global.min.js') }}"></script>
@endpush

@push('scripts')
<script>
(function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  let calendar;
  let editingId = null;

  // ---- FullCalendar ----
  const calEl = document.getElementById('calendar');
  calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  'dayGridMonth,timeGridWeek,listMonth',
    },
    buttonText: {
      today:    'Hoy',
      month:    'Mes',
      week:     'Semana',
      list:     'Lista',
    },
    events: function(info, success, failure) {
      const params = new URLSearchParams({
        start:      info.startStr,
        end:        info.endStr,
        cliente_id: document.getElementById('filtro_cliente').value,
        red_social: document.getElementById('filtro_red').value,
        estado:     document.getElementById('filtro_estado').value,
      });
      fetch(`{{ route('admin.publicaciones.eventos') }}?${params}`)
        .then(r => r.json())
        .then(data => success(data))
        .catch(() => failure());
    },
    dateClick: function(info) {
      openModalCrear(info.dateStr + 'T09:00');
    },
    eventClick: function(info) {
      openModalEditar(info.event.id);
    },
  });
  calendar.render();
  lucide.createIcons();

  // Filtros
  ['filtro_cliente','filtro_red','filtro_estado'].forEach(id => {
    document.getElementById(id).addEventListener('change', () => calendar.refetchEvents());
  });

  // ---- Modal helpers ----
  function resetModal() {
    document.getElementById('formPublicacion').reset();
    document.getElementById('pub_id').value = '';
    document.getElementById('pub_method').value = 'POST';
    document.getElementById('pub_archivo_preview').classList.add('d-none');
    document.getElementById('pub_archivo_img').src   = '';
    document.getElementById('pub_archivo_video').src = '';
    document.getElementById('pub_archivo_img').classList.add('d-none');
    document.getElementById('pub_archivo_video').classList.add('d-none');
    document.getElementById('wrap_nota_cliente').classList.add('d-none');
    document.getElementById('wrap_ia_resultado').classList.add('d-none');
    document.getElementById('wrap_audiencia').classList.add('d-none');
    document.getElementById('wrap_audiencia_guardada').classList.add('d-none');
    document.getElementById('pub_audiencia').value = '';
    document.getElementById('btnAnalizarIA').disabled = true;
    document.getElementById('btnEliminarPublicacion').style.display = 'none';
    document.getElementById('btnMarcarPublicado').style.display = 'none';
    document.getElementById('modalPublicacionTitulo').textContent = 'Nueva Publicación';
    editingId = null;
  }

  function openModalCrear(datetime) {
    resetModal();
    if (datetime) document.getElementById('pub_fecha').value = datetime;
    document.getElementById('pub_estado').value = 'propuesto';
    actualizarFechaMinima();
    new bootstrap.Modal(document.getElementById('modalPublicacion')).show();
    lucide.createIcons();
  }

  function openModalEditar(id) {
    resetModal();
    editingId = id;
    fetch(`{{ url('admin/publicaciones') }}/${id}`, {
      headers: { 'Accept': 'application/json' }
    })
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalPublicacionTitulo').textContent = 'Editar Publicación';
        document.getElementById('pub_id').value            = data.id;
        document.getElementById('pub_method').value        = 'PUT';
        document.getElementById('pub_cliente_id').value    = data.cliente_id;
        actualizarFechaMinima();
        document.getElementById('pub_red_social').value    = data.red_social;
        document.getElementById('pub_titulo').value        = data.titulo;
        document.getElementById('pub_descripcion').value   = data.descripcion;
        document.getElementById('pub_fecha').value         = data.fecha_programada;
        document.getElementById('pub_estado').value        = data.estado;

        // Archivo guardado (imagen o video)
        if (data.archivo_url) {
          mostrarPreviewArchivo(data.archivo_url, esVideo(data.archivo_url));
        }

        // Audiencia guardada en BD
        if (data.audiencia_sugerida) {
          document.getElementById('pub_audiencia').value = data.audiencia_sugerida;
          document.getElementById('audiencia_guardada_display').textContent = data.audiencia_sugerida;
          document.getElementById('wrap_audiencia_guardada').classList.remove('d-none');
        }

        // Comentario del cliente
        if (data.nota_cliente) {
          const estadoLabels = {
            aprobado:  { label: 'Comentario del cliente al aprobar', cls: 'alert-success' },
            rechazado: { label: 'Motivo de rechazo del cliente',     cls: 'alert-danger'  },
          };
          const info = estadoLabels[data.estado] || { label: 'Comentario del cliente', cls: 'alert-warning' };
          document.getElementById('nota_cliente_label').textContent   = info.label;
          document.getElementById('nota_cliente_texto').className     = 'alert py-2 mb-0 small ' + info.cls;
          document.getElementById('nota_cliente_texto').textContent   = data.nota_cliente;
          document.getElementById('wrap_nota_cliente').classList.remove('d-none');
        }

        document.getElementById('btnEliminarPublicacion').style.display = '';
        if (data.estado === 'aprobado') {
          document.getElementById('btnMarcarPublicado').style.display = '';
        }
        new bootstrap.Modal(document.getElementById('modalPublicacion')).show();
        lucide.createIcons();
      });
  }

  function esVideo(url) {
    return /\.(mp4|mov|webm|avi|mkv)(\?.*)?$/i.test(url);
  }

  function mostrarPreviewArchivo(src, isVideo) {
    const wrap  = document.getElementById('pub_archivo_preview');
    const img   = document.getElementById('pub_archivo_img');
    const video = document.getElementById('pub_archivo_video');
    wrap.classList.remove('d-none');
    if (isVideo) {
      video.src = src;
      video.classList.remove('d-none');
      img.classList.add('d-none');
    } else {
      img.src = src;
      img.classList.remove('d-none');
      video.classList.add('d-none');
    }
  }

  // Botón Nueva Publicación
  document.getElementById('btnNuevaPublicacion').addEventListener('click', () => openModalCrear(null));

  // Selección de archivo → preview + activa botón IA
  document.getElementById('pub_archivo').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const isVid = file.type.startsWith('video/');
    const url   = URL.createObjectURL(file);
    mostrarPreviewArchivo(url, isVid);
    document.getElementById('btnAnalizarIA').disabled = false;
    // Limpiar resultado IA anterior
    document.getElementById('wrap_ia_resultado').classList.add('d-none');
    document.getElementById('wrap_audiencia').classList.add('d-none');
    document.getElementById('pub_audiencia').value = '';
  });

  document.getElementById('btnQuitarArchivo').addEventListener('click', function() {
    document.getElementById('pub_archivo').value = '';
    document.getElementById('pub_archivo_preview').classList.add('d-none');
    document.getElementById('pub_archivo_img').src   = '';
    document.getElementById('pub_archivo_video').src = '';
    document.getElementById('btnAnalizarIA').disabled = true;
    document.getElementById('wrap_ia_resultado').classList.add('d-none');
    document.getElementById('wrap_audiencia').classList.add('d-none');
    document.getElementById('pub_audiencia').value = '';
  });

  // ---- Análisis con IA ----
  document.getElementById('btnAnalizarIA').addEventListener('click', async function() {
    const fileInput = document.getElementById('pub_archivo');
    const redSocial = document.getElementById('pub_red_social').value;
    const clienteId = document.getElementById('pub_cliente_id').value;

    if (!fileInput.files[0]) { alert('Selecciona un archivo primero.'); return; }

    // Spinner
    this.disabled = true;
    document.getElementById('iaSpinner').classList.remove('d-none');
    document.getElementById('iaIcon').classList.add('d-none');

    try {
      const formData = new FormData();
      formData.append('archivo',    fileInput.files[0]);
      formData.append('red_social', redSocial || 'instagram');
      if (clienteId) formData.append('cliente_id', clienteId);

      const resp = await fetch('{{ route('admin.publicaciones.analizar') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData,
      });
      const data = await resp.json();

      if (!resp.ok || !data.ok) {
        alert('Error de IA: ' + (data.error || data.message || 'Respuesta inesperada'));
        return;
      }

      // Autocompletar campos
      document.getElementById('pub_descripcion').value = data.copy;

      // Hora sugerida → fecha de hoy + hora IA (o mañana si ya pasó)
      const hoy      = new Date();
      const [hh, mm] = (data.hora_sugerida || '18:00').split(':');
      hoy.setHours(parseInt(hh), parseInt(mm), 0, 0);
      if (hoy < new Date()) hoy.setDate(hoy.getDate() + 1);
      const pad   = n => String(n).padStart(2, '0');
      const fecha = `${hoy.getFullYear()}-${pad(hoy.getMonth()+1)}-${pad(hoy.getDate())}T${pad(hoy.getHours())}:${pad(hoy.getMinutes())}`;
      document.getElementById('pub_fecha').value = fecha;

      // Audiencia
      if (data.audiencia) {
        document.getElementById('pub_audiencia').value       = data.audiencia;
        document.getElementById('audiencia_display').textContent = data.audiencia;
        document.getElementById('wrap_audiencia').classList.remove('d-none');
        document.getElementById('wrap_audiencia_guardada').classList.add('d-none');
      }

      // Resultado IA (tipo + justificación)
      document.getElementById('ia_tipo_contenido').textContent = data.tipo_contenido || '';
      document.getElementById('ia_justificacion').textContent  = data.justificacion  || '';
      document.getElementById('wrap_ia_resultado').classList.remove('d-none');

    } catch (err) {
      alert('Error al conectar con IA: ' + err.message);
    } finally {
      this.disabled = false;
      document.getElementById('iaSpinner').classList.add('d-none');
      document.getElementById('iaIcon').classList.remove('d-none');
      lucide.createIcons();
    }
  });

  // Submit formulario
  document.getElementById('formPublicacion').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id     = document.getElementById('pub_id').value;
    const method = document.getElementById('pub_method').value;
    const url    = id
      ? `{{ url('admin/publicaciones') }}/${id}`
      : `{{ route('admin.publicaciones.store') }}`;

    const formData = new FormData(this);
    if (method === 'PUT') formData.set('_method', 'PUT');

    const btn = document.getElementById('btnGuardarPublicacion');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
      const resp = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: formData,
      });
      const data = await resp.json();
      if (resp.ok && data.ok) {
        bootstrap.Modal.getInstance(document.getElementById('modalPublicacion')).hide();
        calendar.refetchEvents();
      } else {
        // Mostrar errores de validación u otros errores
        if (data.errors) {
          const msgs = Object.values(data.errors).flat().join('\n');
          alert('Errores de validación:\n' + msgs);
        } else {
          alert('Error: ' + (data.message || JSON.stringify(data)));
        }
      }
    } catch (err) {
      alert('Error de conexión: ' + err.message);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar';
    }
  });

  // Eliminar
  document.getElementById('btnEliminarPublicacion').addEventListener('click', async function() {
    if (!confirm('¿Eliminar esta publicación?')) return;
    const id = document.getElementById('pub_id').value;
    const resp = await fetch(`{{ url('admin/publicaciones') }}/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    if (resp.ok) {
      bootstrap.Modal.getInstance(document.getElementById('modalPublicacion')).hide();
      calendar.refetchEvents();
    }
  });

  // ---- Bloqueo de fecha mínima por cliente ----
  const clientesMinimos = @json($clientes_minimos);

  function actualizarFechaMinima() {
    const clienteId = document.getElementById('pub_cliente_id').value;
    const fechaInput = document.getElementById('pub_fecha');
    const aviso    = document.getElementById('pub_fecha_aviso');
    const avisoTxt = document.getElementById('pub_fecha_aviso_texto');

    if (!clienteId || !clientesMinimos[clienteId]) {
      fechaInput.min = '';
      aviso.classList.add('d-none');
      return;
    }

    const cfg  = clientesMinimos[clienteId];
    const dias = cfg.dias || 0;

    if (dias === 0) {
      fechaInput.min = '';
      aviso.classList.add('d-none');
      return;
    }

    const minDate = new Date();
    minDate.setDate(minDate.getDate() + dias);
    // Formato yyyy-MM-ddTHH:mm requerido por datetime-local
    const pad = n => String(n).padStart(2,'0');
    const minStr = `${minDate.getFullYear()}-${pad(minDate.getMonth()+1)}-${pad(minDate.getDate())}T00:00`;
    fechaInput.min = minStr;

    const etiqueta = cfg.interno
      ? `Cliente interno — mínimo ${dias} día(s)`
      : `Mínimo ${dias} día(s) de anticipación requerido(s)`;
    avisoTxt.textContent = etiqueta;
    aviso.classList.remove('d-none');
    if (window.lucide) lucide.createIcons({ nodes: [aviso] });
  }

  document.getElementById('pub_cliente_id').addEventListener('change', actualizarFechaMinima);

  // Marcar como publicado
  document.getElementById('btnMarcarPublicado').addEventListener('click', async function() {
    const id = document.getElementById('pub_id').value;
    const resp = await fetch(`{{ url('admin/publicaciones') }}/${id}/publicar`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    });
    if (resp.ok) {
      bootstrap.Modal.getInstance(document.getElementById('modalPublicacion')).hide();
      calendar.refetchEvents();
    }
  });
})();
</script>
@endpush
