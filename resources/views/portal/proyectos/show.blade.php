@extends('portal.layouts.app')

@section('title', $proyecto->nombre)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">{{ $proyecto->nombre }}</h4>
    <p class="text-muted mb-0">
      <span class="badge bg-{{ $proyecto->estado_badge }}">{{ $proyecto->estado_label }}</span>
      @if($proyecto->fecha_entrega_estimada)
        &nbsp;·&nbsp; Entrega estimada: {{ $proyecto->fecha_entrega_estimada->format('d/m/Y') }}
      @endif
    </p>
  </div>
</div>

@if($proyecto->descripcion)
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-body py-3">
        <p class="mb-0">{{ $proyecto->descripcion }}</p>
      </div>
    </div>
  </div>
</div>
@endif

@if($proyecto->carpeta_drive)
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card border-0" style="background:rgba(255,193,7,.08);">
      <div class="card-body py-3 d-flex align-items-center gap-3">
        <i data-lucide="folder-open" style="width:28px;height:28px;" class="text-warning flex-shrink-0"></i>
        <div>
          <p class="mb-1 fw-semibold">Carpeta de archivos del proyecto</p>
          <a href="{{ $proyecto->carpeta_drive }}" target="_blank" class="btn btn-sm btn-warning">
            <i data-lucide="external-link" style="width:14px;height:14px;" class="me-1"></i>
            Abrir en Drive / Dropbox
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row">
  <!-- Entregas -->
  <div class="col-lg-8 grid-margin">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="package" style="width:16px;height:16px;" class="me-2"></i>
          Entregas ({{ $proyecto->entregas->count() }})
        </h6>
      </div>
      <div class="card-body">
        @forelse($proyecto->entregas as $entrega)
        <div class="card border mb-3 {{ $entrega->estado === 'enviado' ? 'border-primary' : '' }}">
          <div class="card-body">
            <div class="d-flex align-items-start justify-content-between mb-2">
              <div>
                <h6 class="mb-1">{{ $entrega->titulo }}</h6>
                <div class="d-flex gap-1 flex-wrap">
                  <span class="badge bg-{{ $entrega->estado_badge }}">{{ $entrega->estado_label }}</span>
                  <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $entrega->tipo)) }}</span>
                  @if($entrega->fecha_entrega)
                    <span class="badge bg-light text-dark">{{ $entrega->fecha_entrega->format('d/m/Y') }}</span>
                  @endif
                </div>
              </div>
            </div>

            @if($entrega->descripcion)
            <p class="text-muted small mb-3">{{ $entrega->descripcion }}</p>
            @endif

            <!-- Archivos -->
            @if($entrega->archivos->count() > 0)
            <div class="mb-3">
              <p class="small fw-medium mb-2">
                <i data-lucide="paperclip" style="width:13px;height:13px;" class="me-1"></i>
                Archivos adjuntos:
              </p>
              <div class="d-flex flex-wrap gap-2">
                @foreach($entrega->archivos as $archivo)
                  @if($archivo->es_video_url)
                    {{-- Video externo (YouTube/Vimeo) --}}
                    <button type="button"
                      class="btn btn-sm btn-outline-danger btn-visor"
                      data-tipo="video_url"
                      data-url="{{ $archivo->video_url }}"
                      data-nombre="{{ $archivo->nombre }}">
                      <i data-lucide="play-circle" style="width:14px;height:14px;" class="me-1"></i>
                      {{ Str::limit($archivo->nombre, 25) }}
                    </button>
                  @elseif($archivo->es_imagen)
                    {{-- Imagen --}}
                    <button type="button"
                      class="btn btn-sm btn-outline-info btn-visor"
                      data-tipo="imagen"
                      data-url="{{ route('portal.archivos.view', $archivo) }}"
                      data-nombre="{{ $archivo->nombre }}">
                      <i data-lucide="image" style="width:14px;height:14px;" class="me-1"></i>
                      {{ Str::limit($archivo->nombre, 25) }}
                    </button>
                  @elseif($archivo->es_pdf)
                    {{-- PDF --}}
                    <button type="button"
                      class="btn btn-sm btn-outline-primary btn-visor"
                      data-tipo="pdf"
                      data-url="{{ route('portal.archivos.view', $archivo) }}"
                      data-nombre="{{ $archivo->nombre }}">
                      <i data-lucide="file-text" style="width:14px;height:14px;" class="me-1"></i>
                      {{ Str::limit($archivo->nombre, 25) }}
                    </button>
                  @else
                    {{-- Otro (video archivo, etc.) --}}
                    <button type="button"
                      class="btn btn-sm btn-outline-secondary btn-visor"
                      data-tipo="{{ $archivo->tipo_archivo }}"
                      data-url="{{ route('portal.archivos.view', $archivo) }}"
                      data-nombre="{{ $archivo->nombre }}">
                      <i data-lucide="file" style="width:14px;height:14px;" class="me-1"></i>
                      {{ Str::limit($archivo->nombre, 25) }}
                    </button>
                  @endif
                @endforeach
              </div>
            </div>
            @endif

            <!-- Notas del cliente -->
            @if($entrega->notas_cliente && in_array($entrega->estado, ['cambios_solicitados', 'rechazado']))
            <div class="alert alert-warning py-2 mb-3">
              <p class="small mb-0"><strong>Tus comentarios:</strong> {{ $entrega->notas_cliente }}</p>
            </div>
            @endif

            <!-- Botones de acción -->
            @if($entrega->estado === 'enviado')
            <div class="d-flex gap-2 flex-wrap">
              <form method="POST" action="{{ route('portal.entregas.aprobar', $entrega) }}">
                @csrf
                <button type="submit" class="btn btn-success"
                  onclick="return confirm('¿Confirmas la aprobación de esta entrega?')">
                  <i data-lucide="check" style="width:16px;height:16px;" class="me-2"></i>
                  Aprobar Entrega
                </button>
              </form>
              <button type="button" class="btn btn-outline-warning"
                data-bs-toggle="collapse" data-bs-target="#formRechazar{{ $entrega->id }}">
                <i data-lucide="message-square" style="width:16px;height:16px;" class="me-2"></i>
                Solicitar Cambios
              </button>
            </div>
            <div class="collapse mt-3" id="formRechazar{{ $entrega->id }}">
              <form method="POST" action="{{ route('portal.entregas.rechazar', $entrega) }}">
                @csrf
                <div class="mb-2">
                  <label class="form-label small fw-medium">Describe los cambios que necesitas: <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="notas" rows="3" required minlength="10"
                    placeholder="Por favor describe detalladamente los cambios que necesitas..."></textarea>
                </div>
                <button type="submit" class="btn btn-warning">
                  <i data-lucide="send" style="width:14px;height:14px;" class="me-1"></i>
                  Enviar Solicitud de Cambios
                </button>
              </form>
            </div>
            @elseif($entrega->estado === 'aprobado')
            <div class="alert alert-success py-2 mb-0">
              <i data-lucide="check-circle" style="width:14px;height:14px;" class="me-1"></i>
              <span class="small">Entrega aprobada correctamente</span>
            </div>
            @elseif($entrega->estado === 'cambios_solicitados')
            <div class="alert alert-warning py-2 mb-0">
              <i data-lucide="clock" style="width:14px;height:14px;" class="me-1"></i>
              <span class="small">Cambios solicitados. El equipo está trabajando en ello.</span>
            </div>
            @endif
            <x-comentarios
              :comentarios="$entrega->comentarios"
              store-route="portal.comentarios.store"
              comentable-type="App\Models\Entrega"
              :comentable-id="$entrega->id"
              :current-user-id="auth()->id()"
            />
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i data-lucide="inbox" style="width:32px;height:32px;" class="mb-2"></i>
          <p>No hay entregas disponibles en este momento.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <!-- Documentos -->
    @if($proyecto->documentos->count() > 0)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="file-text" style="width:16px;height:16px;" class="me-2"></i>
          Documentos ({{ $proyecto->documentos->count() }})
        </h6>
      </div>
      <div class="card-body p-0">
        @foreach($proyecto->documentos as $documento)
        <div class="border-bottom">
        <div class="d-flex align-items-center px-3 py-2">
          <div class="me-2">
            @if($documento->es_sellado)
              <i data-lucide="lock" style="width:18px;height:18px;" class="text-dark"></i>
            @elseif($documento->es_pdf)
              <i data-lucide="file-text" style="width:18px;height:18px;" class="text-danger"></i>
            @elseif($documento->es_imagen)
              <i data-lucide="image" style="width:18px;height:18px;" class="text-info"></i>
            @else
              <i data-lucide="file" style="width:18px;height:18px;" class="text-secondary"></i>
            @endif
          </div>
          <div class="flex-grow-1 me-2">
            <p class="mb-0 small fw-medium">{{ $documento->nombre }}</p>
            <div class="d-flex gap-1 flex-wrap">
              <span class="badge bg-{{ $documento->estado_badge }} x-small">{{ ucfirst($documento->estado) }}</span>
              @if($documento->es_sellado)
                <span class="badge bg-dark x-small">
                  <i data-lucide="lock" style="width:10px;height:10px;"></i> Sellado
                </span>
              @endif
            </div>
          </div>
          <div class="d-flex gap-1">
            {{-- Botón Ver en modal --}}
            <button type="button"
              class="btn btn-xs btn-outline-primary btn-visor"
              data-tipo="{{ $documento->es_imagen ? 'imagen' : 'pdf' }}"
              data-url="{{ route('portal.documentos.view', $documento) }}"
              data-nombre="{{ $documento->nombre }}"
              title="Ver">
              <i data-lucide="eye" style="width:12px;height:12px;"></i>
            </button>
            {{-- Descargar --}}
            <a href="{{ route('portal.documentos.download', $documento) }}"
              class="btn btn-xs btn-outline-secondary" title="Descargar">
              <i data-lucide="download" style="width:12px;height:12px;"></i>
            </a>
            {{-- Aprobar --}}
            @if(!$documento->es_sellado && $documento->estado === 'enviado')
            <button type="button" class="btn btn-xs btn-success"
              data-bs-toggle="modal" data-bs-target="#modalAprobarDoc{{ $documento->id }}"
              title="Aprobar y sellar">
              <i data-lucide="check" style="width:12px;height:12px;"></i>
            </button>
            @endif
          </div>
        </div>

        </div>
        {{-- Comentarios del documento --}}
        <div class="px-3 pb-2">
          <x-comentarios
            :comentarios="$documento->comentarios"
            store-route="portal.comentarios.store"
            comentable-type="App\Models\Documento"
            :comentable-id="$documento->id"
            :current-user-id="auth()->id()"
          />
        </div>

        @if(!$documento->es_sellado && $documento->estado === 'enviado')
        <div class="modal fade" id="modalAprobarDoc{{ $documento->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST" action="{{ route('portal.documentos.aprobar', $documento) }}">
                @csrf
                <div class="modal-header">
                  <h5 class="modal-title">Aprobar y Sellar Documento</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="alert alert-info">
                    <p class="small mb-0">
                      <strong>Importante:</strong> Al aprobar, quedará sellado digitalmente con tu nombre, fecha
                      y hora. Esta acción no se puede deshacer.
                    </p>
                  </div>
                  <p class="fw-medium">{{ $documento->nombre }}</p>
                  <div class="mb-3">
                    <label class="form-label">Comentario (opcional)</label>
                    <textarea class="form-control" name="comentario" rows="2"
                      placeholder="Aprobado conforme..."></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-success">
                    <i data-lucide="check-circle" style="width:16px;height:16px;" class="me-2"></i>
                    Aprobar y Sellar
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @endif
        </div>{{-- /.border-bottom --}}
        @endforeach
      </div>
    </div>
    @endif

    {{-- Brief Creativo (solo lectura para el cliente) --}}
    @if($proyecto->brief)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="clipboard-list" style="width:16px;height:16px;" class="me-2"></i>
          Brief del Proyecto
        </h6>
      </div>
      <div class="card-body">
        @php $brief = $proyecto->brief; @endphp
        @if($brief->objetivo_campana)
        <p class="text-muted small mb-1">Objetivo</p>
        <p class="small mb-3">{{ $brief->objetivo_campana }}</p>
        @endif
        @if($brief->publico_objetivo)
        <p class="text-muted small mb-1">Público objetivo</p>
        <p class="small mb-3">{{ $brief->publico_objetivo }}</p>
        @endif
        @if($brief->tono_voz)
        <p class="text-muted small mb-1">Tono de voz</p>
        <span class="badge bg-light text-dark border mb-3">{{ $brief->tono_voz }}</span>
        @endif
        @if($brief->entregables_esperados)
        <p class="text-muted small mb-1">Entregables esperados</p>
        <p class="small mb-0">{{ $brief->entregables_esperados }}</p>
        @endif
      </div>
    </div>
    @endif

    <!-- Pagos -->
    @if($proyecto->pagos->count() > 0)
    <div class="card grid-margin">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="credit-card" style="width:16px;height:16px;" class="me-2"></i>
          Pagos ({{ $proyecto->pagos->count() }})
        </h6>
      </div>
      <div class="card-body p-0">
        @foreach($proyecto->pagos as $pago)
        <div class="px-3 py-2 border-bottom">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="mb-0 small fw-medium">{{ $pago->concepto }}</p>
              <span class="badge bg-{{ $pago->estado_badge }} x-small">{{ ucfirst($pago->estado) }}</span>
              @if($pago->fecha_vencimiento)
                <p class="mb-0 x-small {{ $pago->fecha_vencimiento->isPast() && $pago->estado !== 'pagado' ? 'text-danger' : 'text-muted' }}">
                  Vence: {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                </p>
              @endif
              @if($pago->estado === 'pendiente' && $pago->referencia_codi)
                <p class="mb-0 x-small text-muted font-monospace">Ref: {{ $pago->referencia_codi }}</p>
              @endif
            </div>
            <span class="fw-bold text-{{ $pago->estado_badge }}">${{ number_format($pago->monto, 2) }}</span>
          </div>
        </div>
        @endforeach
        <div class="px-3 py-2">
          <a href="{{ route('portal.pagos.index') }}" class="btn btn-sm btn-outline-secondary w-100">
            Ver todos los pagos
          </a>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>

{{-- ================================================================
     MODAL VISOR DE ARCHIVOS UNIVERSAL
     ================================================================ --}}
<div class="modal fade" id="modalVisorArchivo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0" style="background:#12121f;">

      {{-- Header --}}
      <div class="modal-header border-0 px-3 py-2" style="background:#1e1e30; min-height:48px;">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
          <i id="visorIcono" data-lucide="file" style="width:15px;height:15px;flex-shrink:0;color:#94a3b8;"></i>
          <span id="visorNombre" class="text-white small fw-medium text-truncate"></span>
        </div>
        <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
          <a id="visorBtnDescargar" href="#"
            class="btn btn-sm btn-outline-light py-1 px-2" style="display:none; font-size:12px;">
            <i data-lucide="download" style="width:13px;height:13px;" class="me-1"></i>Descargar
          </a>
          <a id="visorBtnNuevaPestana" href="#" target="_blank"
            class="btn btn-sm btn-outline-light py-1 px-2" style="font-size:12px;">
            <i data-lucide="external-link" style="width:13px;height:13px;" class="me-1"></i>Nueva pestaña
          </a>
          <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal"></button>
        </div>
      </div>

      {{-- Body: contenedor relativo, altura fija --}}
      <div class="modal-body p-0" style="height:80vh; position:relative; background:#0d0d1a; overflow:hidden;">

        {{-- Overlay de carga (position:absolute, tapa todo) --}}
        <div id="visorLoading"
          style="position:absolute;inset:0;z-index:10;background:#0d0d1a;
                 display:flex;align-items:center;justify-content:center;">
          <div class="text-center text-white">
            <div class="spinner-border text-primary mb-3" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p class="small mb-0" style="color:#94a3b8;">Cargando archivo…</p>
          </div>
        </div>

        {{-- PDF --}}
        <iframe id="visorPdf" src="about:blank"
          style="position:absolute;inset:0;width:100%;height:100%;border:none;opacity:0;transition:opacity .25s;">
        </iframe>

        {{-- Imagen --}}
        <div id="visorImagenWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;
                 justify-content:center;padding:16px;overflow:auto;opacity:0;transition:opacity .25s;">
          <img id="visorImagen" src="" alt=""
            style="max-width:100%;max-height:100%;object-fit:contain;border-radius:6px;
                   cursor:zoom-in;transition:transform .3s ease;"
            onclick="this.style.transform=this.style.transform?'':'scale(2)';
                     this.style.cursor=this.style.transform?'zoom-out':'zoom-in';">
        </div>

        {{-- Video embed YouTube/Vimeo --}}
        <div id="visorVideoEmbedWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;
                 justify-content:center;opacity:0;transition:opacity .25s;">
          <iframe id="visorVideoEmbed" src="about:blank"
            style="width:100%;height:100%;border:none;"
            allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
            allowfullscreen>
          </iframe>
        </div>

        {{-- Video MP4 --}}
        <div id="visorVideoWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;
                 justify-content:center;padding:16px;opacity:0;transition:opacity .25s;">
          <video id="visorVideo" controls
            style="max-width:100%;max-height:100%;border-radius:6px;">
            <source id="visorVideoSource" src="" type="video/mp4">
          </video>
        </div>

        {{-- Fallback --}}
        <div id="visorFallback"
          style="position:absolute;inset:0;display:flex;align-items:center;
                 justify-content:center;padding:24px;opacity:0;transition:opacity .25s;">
          <div class="text-center">
            <i data-lucide="file-x" style="width:48px;height:48px;color:#475569;" class="mb-3"></i>
            <p class="text-white mb-3">Este archivo no puede previsualizarse.</p>
            <a id="visorFallbackBtn" href="#" class="btn btn-outline-light">
              <i data-lucide="download" style="width:14px;height:14px;" class="me-1"></i>Descargar
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  const PANELES = ['visorPdf', 'visorImagenWrapper', 'visorVideoEmbedWrapper', 'visorVideoWrapper', 'visorFallback'];

  // Mostrar loading, ocultar todos los paneles (usan opacity + pointer-events)
  function resetVisor() {
    document.getElementById('visorLoading').style.display = 'flex';
    PANELES.forEach(function(id) {
      var el = document.getElementById(id);
      el.style.opacity  = '0';
      el.style.pointerEvents = 'none';
    });
    document.getElementById('visorBtnDescargar').style.display = 'none';
  }

  // Mostrar un panel y ocultar el spinner
  function mostrar(id) {
    document.getElementById('visorLoading').style.display = 'none';
    var el = document.getElementById(id);
    el.style.opacity = '1';
    el.style.pointerEvents = 'auto';
  }

  // Mostrar fallback de error
  function mostrarError(urlDescarga) {
    document.getElementById('visorFallbackBtn').href = urlDescarga || '#';
    mostrar('visorFallback');
    if (window.lucide) lucide.createIcons();
  }

  // Convertir URL YouTube / Vimeo → embed
  function toEmbed(url) {
    var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    if (yt) return 'https://www.youtube.com/embed/' + yt[1] + '?autoplay=1&rel=0';
    var vi = url.match(/vimeo\.com\/(\d+)/);
    if (vi) return 'https://player.vimeo.com/video/' + vi[1] + '?autoplay=1';
    return url;
  }

  // URL de descarga a partir de la URL de vista
  function urlDescarga(viewUrl) {
    return viewUrl ? viewUrl.replace(/\/view(\?.*)?$/, '/download') : '#';
  }

  // ── Manejador principal ────────────────────────────────────────────
  document.querySelectorAll('.btn-visor').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var tipo   = this.dataset.tipo   || 'otro';
      var url    = this.dataset.url    || '';
      var nombre = this.dataset.nombre || 'Archivo';

      // Cabecera
      document.getElementById('visorNombre').textContent = nombre;
      document.getElementById('visorBtnNuevaPestana').href = url;

      var iconos = { pdf:'file-text', imagen:'image', video_url:'play-circle', video_archivo:'video' };
      var iconoEl = document.getElementById('visorIcono');
      iconoEl.setAttribute('data-lucide', iconos[tipo] || 'file');
      if (window.lucide) lucide.createIcons({ nodes: [iconoEl] });

      resetVisor();

      // Abrir modal Bootstrap
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalVisorArchivo')).show();

      // ── Renderizado por tipo ───────────────────────────────────────
      if (tipo === 'pdf') {
        var iframe = document.getElementById('visorPdf');

        iframe.onload = function() {
          // Comprobar que no esté en blanco (about:blank dispara onload también)
          if (iframe.src && iframe.src !== 'about:blank') mostrar('visorPdf');
        };
        iframe.onerror = function() { mostrarError(urlDescarga(url)); };
        iframe.src = url;

        // Botón descargar
        var btnD = document.getElementById('visorBtnDescargar');
        btnD.href = urlDescarga(url);
        btnD.style.display = 'inline-flex';

      } else if (tipo === 'imagen') {
        var img = document.getElementById('visorImagen');
        img.style.transform = '';

        img.onload = function() { mostrar('visorImagenWrapper'); };
        img.onerror = function() { mostrarError(urlDescarga(url)); };
        img.src = url;

        var btnD = document.getElementById('visorBtnDescargar');
        btnD.href = urlDescarga(url);
        btnD.style.display = 'inline-flex';

      } else if (tipo === 'video_url') {
        var embed = document.getElementById('visorVideoEmbed');
        embed.onload = function() {
          if (embed.src && embed.src !== 'about:blank') mostrar('visorVideoEmbedWrapper');
        };
        embed.src = toEmbed(url);

      } else if (tipo === 'video_archivo') {
        var video  = document.getElementById('visorVideo');
        var source = document.getElementById('visorVideoSource');

        video.oncanplay = function() { mostrar('visorVideoWrapper'); };
        video.onerror   = function() { mostrarError(urlDescarga(url)); };
        source.src = url;
        video.load();

      } else {
        // Tipo desconocido
        mostrarError(url);
      }
    });
  });

  // ── Limpiar al cerrar ──────────────────────────────────────────────
  document.getElementById('modalVisorArchivo').addEventListener('hidden.bs.modal', function() {
    var pdf   = document.getElementById('visorPdf');
    var embed = document.getElementById('visorVideoEmbed');
    var video = document.getElementById('visorVideo');

    pdf.onload = null;
    embed.onload = null;
    pdf.src   = 'about:blank';
    embed.src = 'about:blank';

    document.getElementById('visorImagen').src = '';
    document.getElementById('visorVideoSource').src = '';
    video.oncanplay = null;
    video.load();

    resetVisor();
  });

})();
</script>
@endpush
