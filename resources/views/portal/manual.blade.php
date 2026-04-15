@extends('portal.layouts.app')

@section('title', 'Manual de Usuario')

@push('style')
<style>
  .manual-section { scroll-margin-top: 80px; }
  .step-circle {
    width: 28px; height: 28px; min-width: 28px;
    border-radius: 50%;
    background: var(--bs-primary);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .manual-toc a {
    color: inherit;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 14px;
    transition: background .15s;
  }
  .manual-toc a:hover { background: rgba(var(--bs-primary-rgb), .08); color: var(--bs-primary); }
  .manual-toc a.active { background: rgba(var(--bs-primary-rgb), .12); color: var(--bs-primary); font-weight: 600; }
  .badge-status {
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
    font-weight: 600;
  }
  .manual-img-placeholder {
    background: rgba(var(--bs-secondary-rgb), .08);
    border: 2px dashed rgba(var(--bs-secondary-rgb), .3);
    border-radius: 8px;
    padding: 24px;
    text-align: center;
    color: var(--bs-secondary);
  }
  .tip-box {
    border-left: 4px solid var(--bs-primary);
    background: rgba(var(--bs-primary-rgb), .06);
    border-radius: 0 6px 6px 0;
    padding: 12px 16px;
    font-size: 14px;
  }
  .warn-box {
    border-left: 4px solid var(--bs-warning);
    background: rgba(var(--bs-warning-rgb), .08);
    border-radius: 0 6px 6px 0;
    padding: 12px 16px;
    font-size: 14px;
  }
  .action-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">
      <i data-lucide="book-open" style="width:22px;height:22px;" class="me-2 text-primary"></i>
      Manual de Usuario
    </h4>
    <p class="text-muted mb-0">Guía completa para usar tu portal de cliente</p>
  </div>
</div>

<div class="row g-4">

  {{-- ====== TABLA DE CONTENIDOS (sticky lateral) ====== --}}
  <div class="col-lg-3 d-none d-lg-block">
    <div class="card" style="position:sticky;top:80px;">
      <div class="card-header py-2">
        <span class="small fw-bold text-muted text-uppercase" style="letter-spacing:.05em;">Contenido</span>
      </div>
      <div class="card-body p-2 manual-toc" id="manualToc">
        <a href="#sec-bienvenida">
          <i data-lucide="home" style="width:14px;height:14px;"></i> Bienvenida
        </a>
        <a href="#sec-dashboard">
          <i data-lucide="layout-dashboard" style="width:14px;height:14px;"></i> Dashboard (Inicio)
        </a>
        <a href="#sec-proyectos">
          <i data-lucide="briefcase" style="width:14px;height:14px;"></i> Mis Proyectos
        </a>
        <a href="#sec-entregas">
          <i data-lucide="package" style="width:14px;height:14px;"></i> Revisar Entregas
        </a>
        <a href="#sec-documentos">
          <i data-lucide="file-text" style="width:14px;height:14px;"></i> Documentos
        </a>
        <a href="#sec-publicaciones">
          <i data-lucide="calendar-days" style="width:14px;height:14px;"></i> Publicaciones
        </a>
        <a href="#sec-pagos">
          <i data-lucide="credit-card" style="width:14px;height:14px;"></i> Mis Pagos
        </a>
        <a href="#sec-cuenta">
          <i data-lucide="user-circle" style="width:14px;height:14px;"></i> Mi Cuenta
        </a>
      </div>
    </div>
  </div>

  {{-- ====== CONTENIDO DEL MANUAL ====== --}}
  <div class="col-lg-9">

    {{-- ─── BIENVENIDA ─── --}}
    <div class="card mb-4 manual-section" id="sec-bienvenida">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary"
               style="width:44px;height:44px;min-width:44px;">
            <i data-lucide="home" class="text-white" style="width:20px;height:20px;"></i>
          </div>
          <div>
            <h5 class="mb-0">Bienvenido al Portal de Cliente</h5>
            <p class="text-muted small mb-0">¿Qué puedes hacer aquí?</p>
          </div>
        </div>
        <p>Este es tu espacio de trabajo con <strong>Espiral Estudio Creativo</strong>. Desde aquí puedes seguir el avance de tus proyectos, aprobar o pedir cambios en entregas, firmar documentos digitalmente, ver el calendario de contenido para redes sociales y consultar el estado de tus pagos.</p>
        <div class="row g-3 mt-1">
          <div class="col-sm-6 col-md-4">
            <div class="d-flex align-items-center gap-2 p-3 rounded border">
              <i data-lucide="briefcase" class="text-primary" style="width:20px;height:20px;"></i>
              <span class="small fw-medium">Seguimiento de proyectos</span>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="d-flex align-items-center gap-2 p-3 rounded border">
              <i data-lucide="check-square" class="text-success" style="width:20px;height:20px;"></i>
              <span class="small fw-medium">Aprobación de entregas</span>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="d-flex align-items-center gap-2 p-3 rounded border">
              <i data-lucide="file-check" class="text-info" style="width:20px;height:20px;"></i>
              <span class="small fw-medium">Firma digital de documentos</span>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="d-flex align-items-center gap-2 p-3 rounded border">
              <i data-lucide="calendar-days" class="text-warning" style="width:20px;height:20px;"></i>
              <span class="small fw-medium">Calendario de publicaciones</span>
            </div>
          </div>
          <div class="col-sm-6 col-md-4">
            <div class="d-flex align-items-center gap-2 p-3 rounded border">
              <i data-lucide="credit-card" class="text-danger" style="width:20px;height:20px;"></i>
              <span class="small fw-medium">Consulta de pagos</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ─── DASHBOARD ─── --}}
    <div class="card mb-4 manual-section" id="sec-dashboard">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="layout-dashboard" class="text-primary" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Dashboard (Inicio)</h5>
      </div>
      <div class="card-body">
        <p>La pantalla de <strong>Inicio</strong> es lo primero que ves al entrar. Te muestra un resumen del estado actual de tu cuenta:</p>

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="p-3 rounded border text-center">
              <i data-lucide="briefcase" class="text-primary mb-2" style="width:24px;height:24px;"></i>
              <p class="fw-bold mb-0" style="font-size:22px;">N</p>
              <p class="small text-muted mb-0">Proyectos Activos</p>
              <p class="x-small text-muted mt-1 mb-0">Proyectos en los que se está trabajando actualmente.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded border text-center">
              <i data-lucide="package" class="text-info mb-2" style="width:24px;height:24px;"></i>
              <p class="fw-bold mb-0" style="font-size:22px;">N</p>
              <p class="small text-muted mb-0">Entregas por Revisar</p>
              <p class="x-small text-muted mt-1 mb-0">Entregas que el equipo envió y esperan tu aprobación.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded border text-center">
              <i data-lucide="credit-card" class="text-warning mb-2" style="width:24px;height:24px;"></i>
              <p class="fw-bold mb-0" style="font-size:22px;">N</p>
              <p class="small text-muted mb-0">Pagos Pendientes</p>
              <p class="x-small text-muted mt-1 mb-0">Pagos que aún no han sido confirmados como pagados.</p>
            </div>
          </div>
        </div>

        <p>También verás la <strong>tabla de proyectos</strong> con su estado y porcentaje de avance, y dos paneles laterales de acceso rápido a <em>entregas pendientes</em> y <em>pagos próximos a vencer</em>.</p>

        <div class="tip-box">
          <i data-lucide="lightbulb" style="width:14px;height:14px;" class="me-1 text-primary"></i>
          <strong>Consejo:</strong> Si ves el número de "Entregas por Revisar" mayor a cero, revísalas pronto. El equipo espera tu aprobación para continuar con el proyecto.
        </div>
      </div>
    </div>

    {{-- ─── MIS PROYECTOS ─── --}}
    <div class="card mb-4 manual-section" id="sec-proyectos">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="briefcase" class="text-primary" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Mis Proyectos</h5>
      </div>
      <div class="card-body">
        <p>Cada proyecto está disponible en el menú lateral izquierdo, bajo la sección <strong>Mis Proyectos</strong>. Al hacer clic en uno verás toda su información en una sola pantalla.</p>

        <h6 class="fw-bold mt-4 mb-3">Estados de un proyecto</h6>
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="badge badge-status bg-secondary">Cotizando</span>
          <span class="badge badge-status bg-info">En Desarrollo</span>
          <span class="badge badge-status bg-warning text-dark">En Revisión</span>
          <span class="badge badge-status bg-primary">Aprobado</span>
          <span class="badge badge-status bg-success">Finalizado</span>
        </div>

        <h6 class="fw-bold mb-3">¿Qué contiene la página de un proyecto?</h6>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="d-flex gap-2 align-items-start p-3 border rounded h-100">
              <i data-lucide="package" class="text-info mt-1" style="width:18px;height:18px;min-width:18px;"></i>
              <div>
                <p class="fw-medium mb-1 small">Entregas</p>
                <p class="text-muted x-small mb-0">Los trabajos que el equipo te envía para revisión: diseños, avances, entrega final, etc.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex gap-2 align-items-start p-3 border rounded h-100">
              <i data-lucide="file-text" class="text-danger mt-1" style="width:18px;height:18px;min-width:18px;"></i>
              <div>
                <p class="fw-medium mb-1 small">Documentos</p>
                <p class="text-muted x-small mb-0">Contratos, propuestas o briefings que requieren tu firma digital.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex gap-2 align-items-start p-3 border rounded h-100">
              <i data-lucide="credit-card" class="text-warning mt-1" style="width:18px;height:18px;min-width:18px;"></i>
              <div>
                <p class="fw-medium mb-1 small">Pagos del proyecto</p>
                <p class="text-muted x-small mb-0">Montos, conceptos y fechas de vencimiento asociados a este proyecto.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="d-flex gap-2 align-items-start p-3 border rounded h-100">
              <i data-lucide="bar-chart-2" class="text-success mt-1" style="width:18px;height:18px;min-width:18px;"></i>
              <div>
                <p class="fw-medium mb-1 small">Barra de progreso</p>
                <p class="text-muted x-small mb-0">Muestra el porcentaje de entregas aprobadas respecto al total.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ─── ENTREGAS ─── --}}
    <div class="card mb-4 manual-section" id="sec-entregas">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="package" class="text-info" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Revisar Entregas</h5>
      </div>
      <div class="card-body">
        <p>Una <strong>entrega</strong> es un trabajo que el equipo completó y pone a tu disposición para revisión. Puede incluir archivos adjuntos como imágenes, PDFs o videos.</p>

        <h6 class="fw-bold mt-3 mb-3">Estados de una entrega</h6>
        <div class="table-responsive mb-4">
          <table class="table table-sm border">
            <thead class="table-light">
              <tr>
                <th>Estado</th>
                <th>Significado</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="badge bg-secondary badge-status">Pendiente</span></td>
                <td class="small">El equipo aún está trabajando en ella.</td>
              </tr>
              <tr>
                <td><span class="badge bg-primary badge-status">Enviado</span></td>
                <td class="small"><strong>Requiere tu acción.</strong> El equipo la envió y espera tu respuesta.</td>
              </tr>
              <tr>
                <td><span class="badge bg-success badge-status">Aprobado</span></td>
                <td class="small">La aprobaste. El proyecto avanza.</td>
              </tr>
              <tr>
                <td><span class="badge bg-warning text-dark badge-status">Cambios solicitados</span></td>
                <td class="small">Solicitaste modificaciones. El equipo está trabajando en los ajustes.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h6 class="fw-bold mb-3">Cómo aprobar una entrega</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">1</div>
            <div>
              <p class="mb-0 small fw-medium">Accede al proyecto desde el menú lateral.</p>
            </div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">2</div>
            <div>
              <p class="mb-0 small fw-medium">Ubica la entrega con estado <span class="badge bg-primary badge-status">Enviado</span> — tendrá un borde azul resaltado.</p>
            </div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">3</div>
            <div>
              <p class="mb-0 small fw-medium">Revisa los archivos adjuntos haciendo clic en cada botón de archivo (imagen, PDF o video).</p>
            </div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">4</div>
            <div>
              <p class="mb-0 small fw-medium">Si estás de acuerdo, haz clic en <span class="action-chip bg-success text-white"><i data-lucide="check" style="width:12px;height:12px;"></i> Aprobar Entrega</span> y confirma.</p>
            </div>
          </div>
        </div>

        <div class="tip-box mb-4">
          <i data-lucide="lightbulb" style="width:14px;height:14px;" class="me-1 text-primary"></i>
          <strong>Visor de archivos:</strong> Al hacer clic en un archivo se abre un visor integrado. Puedes ver imágenes con zoom (clic para ampliar), PDFs sin salir del portal, y videos de YouTube/Vimeo directo.
        </div>

        <h6 class="fw-bold mb-3">Cómo solicitar cambios</h6>
        <div class="d-flex flex-column gap-3 mb-3">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-warning);">1</div>
            <div>
              <p class="mb-0 small fw-medium">En la entrega con estado <span class="badge bg-primary badge-status">Enviado</span>, haz clic en <span class="action-chip bg-warning text-dark"><i data-lucide="message-square" style="width:12px;height:12px;"></i> Solicitar Cambios</span>.</p>
            </div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-warning);">2</div>
            <div>
              <p class="mb-0 small fw-medium">Se abrirá un campo de texto. Describe <strong>con detalle</strong> los cambios que necesitas.</p>
            </div>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-warning);">3</div>
            <div>
              <p class="mb-0 small fw-medium">Haz clic en <strong>Enviar Solicitud de Cambios</strong>. El equipo recibirá tu comentario y trabajará en los ajustes.</p>
            </div>
          </div>
        </div>

        <div class="warn-box">
          <i data-lucide="alert-triangle" style="width:14px;height:14px;" class="me-1 text-warning"></i>
          <strong>Importante:</strong> Sé lo más específico posible al describir los cambios. Esto ayuda al equipo a entender exactamente qué necesitas y reduce el número de rondas de revisión.
        </div>
      </div>
    </div>

    {{-- ─── DOCUMENTOS ─── --}}
    <div class="card mb-4 manual-section" id="sec-documentos">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="file-text" class="text-danger" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Documentos</h5>
      </div>
      <div class="card-body">
        <p>Los <strong>documentos</strong> son archivos formales (contratos, propuestas, briefings) que el equipo adjunta a tu proyecto. Aparecen en el panel lateral derecho dentro de la pantalla del proyecto.</p>

        <h6 class="fw-bold mt-3 mb-3">Estados de un documento</h6>
        <div class="table-responsive mb-4">
          <table class="table table-sm border">
            <thead class="table-light"><tr><th>Estado</th><th>Significado</th></tr></thead>
            <tbody>
              <tr>
                <td><span class="badge bg-secondary badge-status">Borrador</span></td>
                <td class="small">El equipo aún lo está preparando. Todavía no está disponible para ti.</td>
              </tr>
              <tr>
                <td><span class="badge bg-primary badge-status">Enviado</span></td>
                <td class="small"><strong>Requiere tu acción.</strong> Está listo para que lo revises y apruebes.</td>
              </tr>
              <tr>
                <td><span class="badge bg-success badge-status">Aprobado</span></td>
                <td class="small">Lo aprobaste. Queda sellado digitalmente.</td>
              </tr>
              <tr>
                <td><td><span class="d-flex align-items-center gap-1"><i data-lucide="lock" style="width:13px;height:13px;"></i> <span class="badge bg-dark badge-status">Sellado</span></span></td>
                <td class="small">Documento firmado y cerrado definitivamente. Ya no se puede modificar.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <h6 class="fw-bold mb-3">Acciones disponibles sobre documentos</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-2 align-items-center">
            <span class="action-chip border"><i data-lucide="eye" style="width:12px;height:12px;"></i> Ver</span>
            <span class="small text-muted">Abre el archivo en el visor integrado sin salir del portal.</span>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <span class="action-chip border"><i data-lucide="download" style="width:12px;height:12px;"></i> Descargar</span>
            <span class="small text-muted">Descarga el archivo a tu computadora.</span>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <span class="action-chip bg-success text-white"><i data-lucide="check" style="width:12px;height:12px;"></i> Aprobar</span>
            <span class="small text-muted">Solo disponible cuando el estado es <span class="badge bg-primary badge-status">Enviado</span>.</span>
          </div>
        </div>

        <h6 class="fw-bold mb-3">Cómo aprobar y sellar un documento</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">1</div>
            <p class="mb-0 small fw-medium">En el panel de documentos del proyecto, localiza el documento con estado <span class="badge bg-primary badge-status">Enviado</span>.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">2</div>
            <p class="mb-0 small fw-medium">Haz clic en el ícono de ojo <i data-lucide="eye" style="width:13px;height:13px;"></i> para revisar el contenido completo.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">3</div>
            <p class="mb-0 small fw-medium">Haz clic en el botón verde <i data-lucide="check" style="width:13px;height:13px;"></i>. Se abrirá un modal de confirmación.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">4</div>
            <p class="mb-0 small fw-medium">Opcionalmente escribe un comentario (ej. "Aprobado conforme") y haz clic en <span class="action-chip bg-success text-white">Aprobar y Sellar</span>.</p>
          </div>
        </div>

        <div class="warn-box">
          <i data-lucide="alert-triangle" style="width:14px;height:14px;" class="me-1 text-warning"></i>
          <strong>Esta acción es permanente.</strong> Al aprobar, el documento queda sellado digitalmente con tu nombre, fecha y hora exacta. No puede deshacerse. Asegúrate de haberlo leído completo antes de aprobar.
        </div>
      </div>
    </div>

    {{-- ─── PUBLICACIONES ─── --}}
    <div class="card mb-4 manual-section" id="sec-publicaciones">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="calendar-days" class="text-warning" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Publicaciones (Calendario de Redes Sociales)</h5>
      </div>
      <div class="card-body">
        <p>En la sección <strong>Publicaciones</strong> verás el calendario con todo el contenido que el equipo ha programado para tus redes sociales. Puedes aprobar o rechazar cada publicación antes de que sea publicada.</p>

        <h6 class="fw-bold mt-3 mb-3">Estados y colores del calendario</h6>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <div class="d-flex align-items-center gap-2">
            <span style="width:14px;height:14px;border-radius:50%;background:#fd7e14;display:inline-block;"></span>
            <span class="small fw-medium">Pendiente</span>
            <span class="text-muted x-small">— Propuesto por el equipo, esperando tu aprobación.</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span style="width:14px;height:14px;border-radius:50%;background:#198754;display:inline-block;"></span>
            <span class="small fw-medium">Aprobado</span>
            <span class="text-muted x-small">— Lo aprobaste, listo para publicar.</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span style="width:14px;height:14px;border-radius:50%;background:#dc3545;display:inline-block;"></span>
            <span class="small fw-medium">Rechazado</span>
            <span class="text-muted x-small">— Lo rechazaste con comentarios.</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span style="width:14px;height:14px;border-radius:50%;background:#0d6efd;display:inline-block;"></span>
            <span class="small fw-medium">Publicado</span>
            <span class="text-muted x-small">— Ya fue publicado en la red social.</span>
          </div>
        </div>

        <h6 class="fw-bold mb-3">Cómo aprobar una publicación</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">1</div>
            <p class="mb-0 small fw-medium">Haz clic en cualquier evento del calendario para ver sus detalles.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">2</div>
            <p class="mb-0 small fw-medium">En el modal verás la red social, fecha, imagen o video, y el texto (copy) del post.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle">3</div>
            <p class="mb-0 small fw-medium">Si el contenido está en estado <strong>Pendiente</strong>, verás los botones de acción. Haz clic en <span class="action-chip bg-success text-white">Aprobar</span>.</p>
          </div>
        </div>

        <h6 class="fw-bold mb-3">Cómo rechazar una publicación</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-danger);">1</div>
            <p class="mb-0 small fw-medium">En el modal del post, haz clic en <span class="action-chip bg-danger text-white">Rechazar</span>.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-danger);">2</div>
            <p class="mb-0 small fw-medium">Escribe el motivo del rechazo o los cambios que necesitas.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle" style="background:var(--bs-danger);">3</div>
            <p class="mb-0 small fw-medium">Confirma. El equipo recibirá tu comentario y reelaborará la publicación.</p>
          </div>
        </div>

        <div class="tip-box">
          <i data-lucide="lightbulb" style="width:14px;height:14px;" class="me-1 text-primary"></i>
          <strong>Navega por el calendario:</strong> Usa las flechas en la parte superior del calendario para moverte entre semanas o meses. También puedes cambiar entre vista mensual, semanal y de lista.
        </div>
      </div>
    </div>

    {{-- ─── PAGOS ─── --}}
    <div class="card mb-4 manual-section" id="sec-pagos">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="credit-card" class="text-warning" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Mis Pagos</h5>
      </div>
      <div class="card-body">
        <p>En la sección <strong>Mis Pagos</strong> puedes consultar todos los conceptos de pago asociados a tus proyectos: anticipos, parcialidades y saldos finales.</p>

        <h6 class="fw-bold mt-3 mb-3">Estados de un pago</h6>
        <div class="table-responsive mb-4">
          <table class="table table-sm border">
            <thead class="table-light"><tr><th>Estado</th><th>Significado</th></tr></thead>
            <tbody>
              <tr>
                <td><span class="badge bg-warning text-dark badge-status">Pendiente</span></td>
                <td class="small">El pago está generado. Está esperando ser liquidado.</td>
              </tr>
              <tr>
                <td><span class="badge bg-success badge-status">Pagado</span></td>
                <td class="small">El equipo confirmó la recepción del pago.</td>
              </tr>
              <tr>
                <td><span class="badge bg-danger badge-status">Vencido</span></td>
                <td class="small">La fecha de vencimiento pasó sin registrar el pago. Contacta al equipo.</td>
              </tr>
              <tr>
                <td><span class="badge bg-secondary badge-status">Cancelado</span></td>
                <td class="small">El concepto fue cancelado.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="warn-box">
          <i data-lucide="alert-triangle" style="width:14px;height:14px;" class="me-1 text-warning"></i>
          <strong>Recuerda:</strong> Este portal es solo de consulta de pagos. Para realizar un pago o subir un comprobante, contacta directamente al equipo de Espiral.
        </div>
      </div>
    </div>

    {{-- ─── CUENTA ─── --}}
    <div class="card mb-4 manual-section" id="sec-cuenta">
      <div class="card-header d-flex align-items-center gap-2">
        <i data-lucide="user-circle" class="text-secondary" style="width:18px;height:18px;"></i>
        <h5 class="card-title mb-0">Mi Cuenta</h5>
      </div>
      <div class="card-body">

        <h6 class="fw-bold mb-3">Cómo cerrar sesión</h6>
        <div class="d-flex flex-column gap-3 mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle bg-secondary">1</div>
            <p class="mb-0 small fw-medium">Haz clic en tu foto de perfil en la esquina superior derecha de la pantalla.</p>
          </div>
          <div class="d-flex gap-3 align-items-start">
            <div class="step-circle bg-secondary">2</div>
            <p class="mb-0 small fw-medium">En el menú desplegable, haz clic en <span class="action-chip border"><i data-lucide="log-out" style="width:12px;height:12px;"></i> Cerrar Sesión</span>.</p>
          </div>
        </div>

        <h6 class="fw-bold mb-3">¿Olvidaste tu contraseña?</h6>
        <p class="small text-muted mb-3">Si no puedes acceder al portal, contacta al equipo de Espiral y ellos te asignarán una nueva contraseña.</p>

        <h6 class="fw-bold mb-3">Modo oscuro / claro</h6>
        <p class="small text-muted mb-0">En la barra superior verás un interruptor de tema. Puedes cambiar entre modo claro y oscuro según tu preferencia. La preferencia se guarda en tu navegador.</p>

        <div class="tip-box mt-4">
          <i data-lucide="smartphone" style="width:14px;height:14px;" class="me-1 text-primary"></i>
          <strong>Desde el celular:</strong> El portal es compatible con teléfonos y tabletas. El menú lateral se oculta automáticamente en pantallas pequeñas; usa el botón de hamburguesa (☰) en la parte superior para abrirlo.
        </div>
      </div>
    </div>

    {{-- ─── CONTACTO ─── --}}
    <div class="card border-primary mb-4">
      <div class="card-body d-flex align-items-center gap-3">
        <i data-lucide="headphones" class="text-primary" style="width:36px;height:36px;min-width:36px;"></i>
        <div>
          <h6 class="fw-bold mb-1">¿Necesitas ayuda?</h6>
          <p class="text-muted small mb-0">Si tienes dudas sobre el portal o sobre tu proyecto, contacta directamente a tu ejecutivo de cuenta en Espiral Estudio Creativo.</p>
        </div>
      </div>
    </div>

  </div>{{-- fin col-9 --}}
</div>{{-- fin row --}}
@endsection

@push('scripts')
<script>
// Resaltar sección activa en el índice al hacer scroll
(function () {
  const secciones = document.querySelectorAll('.manual-section');
  const enlaces = document.querySelectorAll('.manual-toc a');
  if (!secciones.length || !enlaces.length) return;

  function activar() {
    let actual = '';
    secciones.forEach(function(sec) {
      if (window.scrollY >= sec.offsetTop - 120) actual = sec.id;
    });
    enlaces.forEach(function(a) {
      a.classList.toggle('active', a.getAttribute('href') === '#' + actual);
    });
  }

  window.addEventListener('scroll', activar, { passive: true });
  activar();
})();
</script>
@endpush
