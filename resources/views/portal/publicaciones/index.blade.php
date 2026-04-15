@extends('portal.layouts.app')

@section('title', 'Mis Publicaciones')

@push('plugin-styles')
  <style>
    #calendar { min-height: 560px; }
    .fc .fc-event {
      cursor: pointer;
      font-size: 0.8rem;
      border-radius: 4px;
      padding: 1px 4px;
    }
    .legend-dot {
      width: 12px; height: 12px;
      border-radius: 50%;
      display: inline-block;
    }
    #pub_modal_imagen { max-width: 100%; max-height: 260px; border-radius: 8px; }
  </style>
@endpush

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <h4 class="mb-0 fw-bold">Calendario de Publicaciones</h4>
    <p class="text-muted small mb-0">Aquí puedes aprobar o rechazar el contenido propuesto</p>
  </div>
  <div class="d-flex gap-3 align-items-center flex-wrap">
    <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#fd7e14"></span> Pendiente</span>
    <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#198754"></span> Aprobado</span>
    <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#dc3545"></span> Rechazado</span>
    <span class="d-flex align-items-center gap-1 small text-muted"><span class="legend-dot" style="background:#0d6efd"></span> Publicado</span>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

{{-- ======== MODAL VER / APROBAR / RECHAZAR ======== --}}
<div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <span id="pub_red_badge" class="badge bg-secondary me-1"></span>
          <span id="pub_estado_badge" class="badge"></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h5 id="pub_modal_titulo" class="fw-bold mb-1"></h5>
        <p class="text-muted small mb-3" id="pub_modal_fecha"></p>

        {{-- Vista previa de imagen o video --}}
        <div id="pub_modal_media_wrap" class="mb-3 text-center d-none">
          <img id="pub_modal_imagen" src="" alt="Imagen del post" class="img-fluid rounded" style="max-height:300px; cursor:zoom-in;" onclick="window.open(this.src,'_blank')">
          <video id="pub_modal_video" src="" controls class="d-none w-100 rounded" style="max-height:300px;"></video>
          <p class="text-muted x-small mt-1 mb-0">
            <i data-lucide="zoom-in" style="width:12px;height:12px;"></i>
            Haz clic en la imagen para verla en tamaño completo
          </p>
        </div>

        <div class="mb-3">
          <label class="small fw-semibold text-muted">Copy del post</label>
          <p id="pub_modal_descripcion" class="mb-0" style="white-space:pre-line;"></p>
        </div>

        {{-- Comentario previo del cliente (estados ya resueltos) --}}
        <div id="pub_modal_nota_wrap" class="d-none">
          <label class="small fw-semibold text-muted" id="pub_modal_nota_label">Tu comentario</label>
          <p id="pub_modal_nota" class="small mb-0 fst-italic"></p>
        </div>

        {{-- Audiencia sugerida (visible para el cliente) --}}
        <div id="pub_modal_audiencia_wrap" class="d-none mt-2">
          <label class="small fw-semibold text-muted">
            <i data-lucide="target" style="width:12px;height:12px;" class="me-1"></i>
            Audiencia sugerida para publicidad
          </label>
          <p id="pub_modal_audiencia" class="small mb-0 fst-italic text-body"></p>
        </div>

        {{-- Área de comentario (solo para estado propuesto) --}}
        <div id="wrap_comentario" class="mt-3 d-none">
          <label class="form-label small fw-semibold">
            Comentario para el equipo
            <span class="text-muted fw-normal">(opcional)</span>
          </label>
          <textarea class="form-control form-control-sm" id="nota_cliente_input" rows="3" maxlength="500"
            placeholder="Ej: Cambiar el texto del CTA, ajustar los colores, etc."></textarea>
        </div>
      </div>
      <div class="modal-footer gap-2" id="pub_modal_footer">
        {{-- Botones dinámicos según estado --}}
      </div>
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
  let activeEventId = null;

  const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  'dayGridMonth,listMonth',
    },
    buttonText: { today: 'Hoy', month: 'Mes', list: 'Lista' },
    editable: false,
    selectable: false,
    events: function(info, success, failure) {
      const params = new URLSearchParams({ start: info.startStr, end: info.endStr });
      fetch(`{{ route('portal.publicaciones.eventos') }}?${params}`)
        .then(r => r.json())
        .then(data => success(data))
        .catch(() => failure());
    },
    eventClick: function(info) {
      openModal(info.event);
    },
  });
  calendar.render();

  function esVideo(url) {
    return /\.(mp4|mov|webm|avi|mkv)(\?.*)?$/i.test(url);
  }

  function openModal(event) {
    activeEventId = event.id;
    const p = event.extendedProps;

    // Badges
    const redBadge = document.getElementById('pub_red_badge');
    redBadge.textContent = p.redLabel;
    redBadge.className   = 'badge bg-secondary me-1';

    const estadoBadge = document.getElementById('pub_estado_badge');
    estadoBadge.textContent = p.estadoLabel;
    estadoBadge.className   = 'badge bg-' + p.estadoBadge;

    // Título y fecha
    document.getElementById('pub_modal_titulo').textContent = event.title.replace(/^\S+\s/, '');
    const fechaStr = moment(event.start).locale('es').format('dddd D [de] MMMM YYYY [·] HH:mm');
    document.getElementById('pub_modal_fecha').textContent = fechaStr;

    // Descripción
    document.getElementById('pub_modal_descripcion').textContent = p.descripcion;

    // Media (imagen o video)
    const mediaWrap = document.getElementById('pub_modal_media_wrap');
    const imgEl     = document.getElementById('pub_modal_imagen');
    const videoEl   = document.getElementById('pub_modal_video');
    if (p.imagen_url) {
      mediaWrap.classList.remove('d-none');
      if (esVideo(p.imagen_url)) {
        imgEl.classList.add('d-none');
        videoEl.classList.remove('d-none');
        videoEl.src = p.imagen_url;
        // Ocultar hint de zoom para videos
        mediaWrap.querySelector('p').classList.add('d-none');
      } else {
        videoEl.classList.add('d-none');
        imgEl.classList.remove('d-none');
        imgEl.src = p.imagen_url;
        mediaWrap.querySelector('p').classList.remove('d-none');
      }
    } else {
      mediaWrap.classList.add('d-none');
      imgEl.src  = '';
      videoEl.src = '';
    }

    // Audiencia sugerida
    const audWrap = document.getElementById('pub_modal_audiencia_wrap');
    if (p.audiencia_sugerida) {
      document.getElementById('pub_modal_audiencia').textContent = p.audiencia_sugerida;
      audWrap.classList.remove('d-none');
    } else {
      audWrap.classList.add('d-none');
    }

    // Comentario previo (estados ya resueltos)
    const notaWrap = document.getElementById('pub_modal_nota_wrap');
    if (p.nota_cliente) {
      const label = p.estado === 'rechazado'
        ? 'Tu comentario al rechazar'
        : 'Tu comentario al aprobar';
      document.getElementById('pub_modal_nota_label').textContent = label;
      document.getElementById('pub_modal_nota').textContent = p.nota_cliente;
      notaWrap.classList.remove('d-none');
    } else {
      notaWrap.classList.add('d-none');
    }

    // Área de comentario + footer
    document.getElementById('nota_cliente_input').value = '';
    const wrapComentario = document.getElementById('wrap_comentario');
    if (p.estado === 'propuesto') {
      wrapComentario.classList.remove('d-none');
    } else {
      wrapComentario.classList.add('d-none');
    }

    renderFooter(p.estado);
    new bootstrap.Modal(document.getElementById('modalVer')).show();
    lucide.createIcons();
  }

  function renderFooter(estado) {
    const footer = document.getElementById('pub_modal_footer');
    if (estado === 'propuesto') {
      footer.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm" id="btnRechazar">
          <i data-lucide="x-circle" style="width:14px;height:14px;" class="me-1"></i> Rechazar
        </button>
        <button type="button" class="btn btn-success btn-sm" id="btnAprobar">
          <i data-lucide="check-circle" style="width:14px;height:14px;" class="me-1"></i> Aprobar
        </button>
      `;
      lucide.createIcons();
      document.getElementById('btnRechazar').addEventListener('click', () => rechazar());
      document.getElementById('btnAprobar').addEventListener('click', () => aprobar());
    } else {
      footer.innerHTML = `<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>`;
    }
  }

  async function aprobar() {
    const nota = document.getElementById('nota_cliente_input').value.trim();
    const resp = await fetch(`{{ url('portal/publicaciones') }}/${activeEventId}/aprobar`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ nota }),
    });
    const data = await resp.json();
    if (resp.ok && data.ok) {
      bootstrap.Modal.getInstance(document.getElementById('modalVer')).hide();
      calendar.refetchEvents();
      mostrarToast('Publicación aprobada ✓', 'success');
    } else {
      alert(data.error || 'Error al aprobar.');
    }
  }

  async function rechazar() {
    const nota = document.getElementById('nota_cliente_input').value.trim();
    const resp = await fetch(`{{ url('portal/publicaciones') }}/${activeEventId}/rechazar`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ nota }),
    });
    const data = await resp.json();
    if (resp.ok && data.ok) {
      bootstrap.Modal.getInstance(document.getElementById('modalVer')).hide();
      calendar.refetchEvents();
      mostrarToast('Publicación rechazada.', 'warning');
    } else {
      alert(data.error || 'Error al rechazar.');
    }
  }

  function mostrarToast(mensaje, tipo) {
    const div = document.createElement('div');
    div.className = `alert alert-${tipo} alert-dismissible fade show position-fixed bottom-0 end-0 m-3`;
    div.style.zIndex = 9999;
    div.innerHTML = `${mensaje}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 4000);
  }
})();
</script>
@endpush
