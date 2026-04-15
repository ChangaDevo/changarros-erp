@extends('admin.layouts.app')

@section('title', $proyecto->nombre)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">{{ $proyecto->nombre }}</h4>
    <p class="text-muted mb-0">
      <a href="{{ route('admin.clientes.show', $proyecto->cliente) }}" class="text-muted">
        {{ $proyecto->cliente->nombre_empresa ?? 'Sin cliente' }}
      </a>
      &nbsp;·&nbsp;
      <span class="badge bg-{{ $proyecto->estado_badge }}">{{ $proyecto->estado_label }}</span>
    </p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if($proyecto->carpeta_drive)
    <a href="{{ $proyecto->carpeta_drive }}" target="_blank" class="btn btn-outline-warning">
      <i data-lucide="folder-open" style="width:16px;height:16px;" class="me-2"></i>Carpeta Drive
    </a>
    @endif
    <a href="{{ route('admin.proyectos.edit', $proyecto) }}" class="btn btn-outline-secondary">
      <i data-lucide="edit" style="width:16px;height:16px;" class="me-2"></i>Editar
    </a>
    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEntrega">
      <i data-lucide="upload" style="width:16px;height:16px;" class="me-2"></i>Nueva Entrega
    </button>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDocumento">
      <i data-lucide="file-plus" style="width:16px;height:16px;" class="me-2"></i>Subir Documento
    </button>
    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalPago">
      <i data-lucide="dollar-sign" style="width:16px;height:16px;" class="me-2"></i>Generar Cobro
    </button>
  </div>
</div>

<!-- Project Info Row -->
<div class="row">
  <div class="col-lg-3 col-md-6 grid-margin">
    <div class="card text-center">
      <div class="card-body">
        <p class="text-muted small mb-1">Monto Total</p>
        <h4 class="mb-0 text-success">
          {{ $proyecto->monto_total ? '$' . number_format($proyecto->monto_total, 2) : 'No definido' }}
        </h4>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 grid-margin">
    <div class="card text-center">
      <div class="card-body">
        <p class="text-muted small mb-1">Inicio</p>
        <h5 class="mb-0">{{ $proyecto->fecha_inicio ? $proyecto->fecha_inicio->format('d/m/Y') : '-' }}</h5>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 grid-margin">
    <div class="card text-center">
      <div class="card-body">
        <p class="text-muted small mb-1">Entrega Estimada</p>
        <h5 class="mb-0 {{ $proyecto->fecha_entrega_estimada && $proyecto->fecha_entrega_estimada->isPast() && !in_array($proyecto->estado, ['finalizado']) ? 'text-danger' : '' }}">
          {{ $proyecto->fecha_entrega_estimada ? $proyecto->fecha_entrega_estimada->format('d/m/Y') : '-' }}
        </h5>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6 grid-margin">
    <div class="card text-center">
      <div class="card-body">
        <p class="text-muted small mb-1">Cobrado / Pagado</p>
        <h5 class="mb-0">
          ${{ number_format($proyecto->pagos->sum('monto'), 2) }} /
          <span class="text-success">${{ number_format($proyecto->pagos->where('estado', 'pagado')->sum('monto'), 2) }}</span>
        </h5>
      </div>
    </div>
  </div>
</div>

@if($proyecto->descripcion)
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-body py-3">
        <p class="mb-0 text-muted">{{ $proyecto->descripcion }}</p>
      </div>
    </div>
  </div>
</div>
@endif

{{-- Brief Creativo --}}
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="clipboard-list" style="width:16px;height:16px;" class="me-2"></i>
          Brief Creativo
        </h6>
        <a href="{{ route('admin.proyectos.brief.edit', $proyecto) }}" class="btn btn-sm btn-outline-primary">
          <i data-lucide="{{ $proyecto->brief ? 'edit' : 'plus' }}" style="width:14px;height:14px;" class="me-1"></i>
          {{ $proyecto->brief ? 'Editar Brief' : 'Crear Brief' }}
        </a>
      </div>
      <div class="card-body">
        @if($proyecto->brief)
          @php $brief = $proyecto->brief; @endphp
          <div class="row g-3">
            @if($brief->objetivo_campana)
            <div class="col-md-6">
              <p class="text-muted small mb-1">Objetivo de la campaña</p>
              <p class="mb-0">{{ $brief->objetivo_campana }}</p>
            </div>
            @endif
            @if($brief->publico_objetivo)
            <div class="col-md-6">
              <p class="text-muted small mb-1">Público objetivo</p>
              <p class="mb-0">{{ $brief->publico_objetivo }}</p>
            </div>
            @endif
            @if($brief->tono_voz)
            <div class="col-md-4">
              <p class="text-muted small mb-1">Tono de voz</p>
              <span class="badge bg-light text-dark border">{{ $brief->tono_voz }}</span>
            </div>
            @endif
            @if($brief->colores_marca)
            <div class="col-md-4">
              <p class="text-muted small mb-1">Colores de marca</p>
              <p class="mb-0 small">{{ $brief->colores_marca }}</p>
            </div>
            @endif
            @if($brief->presupuesto_referencial)
            <div class="col-md-4">
              <p class="text-muted small mb-1">Presupuesto referencial</p>
              <p class="mb-0 fw-semibold">${{ number_format($brief->presupuesto_referencial, 2) }}</p>
            </div>
            @endif
            @if($brief->entregables_esperados)
            <div class="col-12">
              <p class="text-muted small mb-1">Entregables esperados</p>
              <p class="mb-0">{{ $brief->entregables_esperados }}</p>
            </div>
            @endif
          </div>
          <div class="mt-3 d-flex align-items-center gap-2">
            <div class="progress flex-grow-1" style="height:6px;">
              <div class="progress-bar bg-primary" style="width:{{ ($brief->camposLlenos() / $brief->totalCampos()) * 100 }}%"></div>
            </div>
            <small class="text-muted">{{ $brief->camposLlenos() }}/{{ $brief->totalCampos() }} campos</small>
          </div>
        @else
          <div class="text-center py-3">
            <i data-lucide="clipboard" style="width:32px;height:32px;" class="text-muted mb-2"></i>
            <p class="text-muted mb-2">Aún no hay brief creativo para este proyecto.</p>
            <a href="{{ route('admin.proyectos.brief.edit', $proyecto) }}" class="btn btn-sm btn-primary">
              <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>Crear Brief
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Deliveries Timeline -->
  <div class="col-lg-7 grid-margin">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="package" style="width:16px;height:16px;" class="me-2"></i>
          Entregas ({{ $proyecto->entregas->count() }})
        </h6>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEntrega">
          <i data-lucide="plus" style="width:14px;height:14px;"></i>
        </button>
      </div>
      <div class="card-body">
        @forelse($proyecto->entregas as $entrega)
        <div class="d-flex mb-4">
          <div class="me-3 text-center" style="min-width:40px;">
            <div class="w-35px h-35px d-flex align-items-center justify-content-center rounded-circle bg-{{ $entrega->estado_badge }}-subtle">
              <i data-lucide="package" style="width:16px;height:16px;" class="text-{{ $entrega->estado_badge }}"></i>
            </div>
            @if(!$loop->last)
            <div style="width:2px;height:30px;background:#dee2e6;margin:4px auto;"></div>
            @endif
          </div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-start justify-content-between">
              <div>
                <h6 class="mb-1">{{ $entrega->titulo }}</h6>
                <span class="badge bg-{{ $entrega->estado_badge }} me-2">{{ $entrega->estado_label }}</span>
                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $entrega->tipo)) }}</span>
              </div>
              <div class="d-flex gap-1">
                <form method="POST" action="{{ route('admin.entregas.destroy', $entrega) }}"
                  onsubmit="return confirm('¿Eliminar esta entrega?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                  </button>
                </form>
              </div>
            </div>
            @if($entrega->descripcion)
            <p class="small text-muted mt-1 mb-1">{{ $entrega->descripcion }}</p>
            @endif
            @if($entrega->fecha_entrega)
            <p class="x-small text-muted mb-1">Fecha: {{ $entrega->fecha_entrega->format('d/m/Y') }}</p>
            @endif
            <!-- Files -->
            @if($entrega->archivos->count() > 0)
            <div class="mt-2 d-flex flex-wrap gap-1">
              @foreach($entrega->archivos as $archivo)
                @if($archivo->es_video_url)
                  <button type="button" class="btn btn-sm btn-outline-danger btn-visor"
                    data-tipo="video_url"
                    data-url="{{ $archivo->video_url }}"
                    data-nombre="{{ $archivo->nombre }}">
                    <i data-lucide="play-circle" style="width:12px;height:12px;" class="me-1"></i>{{ Str::limit($archivo->nombre, 20) }}
                  </button>
                @elseif($archivo->es_imagen)
                  <button type="button" class="btn btn-sm btn-outline-info btn-visor"
                    data-tipo="imagen"
                    data-url="{{ route('admin.archivos.view', $archivo) }}"
                    data-download="{{ route('admin.archivos.download', $archivo) }}"
                    data-nombre="{{ $archivo->nombre }}">
                    <i data-lucide="image" style="width:12px;height:12px;" class="me-1"></i>{{ Str::limit($archivo->nombre, 20) }}
                  </button>
                @elseif($archivo->es_pdf)
                  <button type="button" class="btn btn-sm btn-outline-primary btn-visor"
                    data-tipo="pdf"
                    data-url="{{ route('admin.archivos.view', $archivo) }}"
                    data-download="{{ route('admin.archivos.download', $archivo) }}"
                    data-nombre="{{ $archivo->nombre }}">
                    <i data-lucide="file-text" style="width:12px;height:12px;" class="me-1"></i>{{ Str::limit($archivo->nombre, 20) }}
                  </button>
                @elseif($archivo->tipo_archivo === 'video_archivo')
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-visor"
                    data-tipo="video_archivo"
                    data-url="{{ route('admin.archivos.view', $archivo) }}"
                    data-nombre="{{ $archivo->nombre }}">
                    <i data-lucide="video" style="width:12px;height:12px;" class="me-1"></i>{{ Str::limit($archivo->nombre, 20) }}
                  </button>
                @else
                  <button type="button" class="btn btn-sm btn-outline-secondary btn-visor"
                    data-tipo="otro"
                    data-url="{{ route('admin.archivos.view', $archivo) }}"
                    data-download="{{ route('admin.archivos.download', $archivo) }}"
                    data-nombre="{{ $archivo->nombre }}">
                    <i data-lucide="file" style="width:12px;height:12px;" class="me-1"></i>{{ Str::limit($archivo->nombre, 20) }}
                  </button>
                @endif
              @endforeach
            </div>
            @endif
            <!-- Client Notes -->
            @if($entrega->notas_cliente)
            <div class="alert alert-light border-start border-warning border-3 py-2 px-3 mt-2 mb-0">
              <p class="small mb-0"><strong>Nota del cliente:</strong> {{ $entrega->notas_cliente }}</p>
            </div>
            @endif
            <x-comentarios
              :comentarios="$entrega->comentarios"
              store-route="admin.comentarios.store"
              delete-route="admin.comentarios.destroy"
              comentable-type="App\Models\Entrega"
              :comentable-id="$entrega->id"
              :current-user-id="auth()->id()"
              :is-super-admin="auth()->user()->isSuperAdmin()"
            />
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">
          <i data-lucide="inbox" style="width:32px;height:32px;" class="mb-2"></i>
          <p>Sin entregas. <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalEntrega">Crear primera entrega</button></p>
        </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <!-- Documents -->
    <div class="card grid-margin">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="file-text" style="width:16px;height:16px;" class="me-2"></i>
          Documentos ({{ $proyecto->documentos->count() }})
        </h6>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalDocumento">
          <i data-lucide="plus" style="width:14px;height:14px;"></i>
        </button>
      </div>
      <div class="card-body p-0">
        @forelse($proyecto->documentos as $documento)
        <div class="border-bottom">
        <div class="d-flex align-items-center px-3 py-2">
          <div class="me-3">
            @if($documento->es_pdf)
              <i data-lucide="file-text" style="width:24px;height:24px;" class="text-danger"></i>
            @elseif($documento->es_imagen)
              <i data-lucide="image" style="width:24px;height:24px;" class="text-info"></i>
            @else
              <i data-lucide="file" style="width:24px;height:24px;" class="text-secondary"></i>
            @endif
          </div>
          <div class="flex-grow-1 me-2">
            <p class="mb-0 small fw-medium">{{ $documento->nombre }}</p>
            <div class="d-flex gap-1 align-items-center flex-wrap">
              <span class="badge bg-{{ $documento->estado_badge }} x-small">{{ ucfirst($documento->estado) }}</span>
              <span class="badge bg-secondary x-small">{{ ucfirst($documento->tipo) }}</span>
              @if($documento->visible_cliente)
                <span class="badge bg-success x-small">Visible cliente</span>
              @endif
              @if($documento->es_sellado)
                <span class="badge bg-dark x-small">
                  <i data-lucide="lock" style="width:10px;height:10px;" class="me-1"></i>Sellado
                </span>
              @endif
            </div>
          </div>
          <div class="d-flex flex-column gap-1">
            <button type="button" class="btn btn-xs btn-outline-primary btn-visor"
              data-tipo="{{ $documento->es_imagen ? 'imagen' : 'pdf' }}"
              data-url="{{ route('admin.documentos.view', $documento) }}"
              data-download="{{ route('admin.documentos.download', $documento) }}"
              data-nombre="{{ $documento->nombre }}"
              title="Vista previa">
              <i data-lucide="eye" style="width:12px;height:12px;"></i>
            </button>
            <a href="{{ route('admin.documentos.download', $documento) }}" class="btn btn-xs btn-outline-secondary" title="Descargar">
              <i data-lucide="download" style="width:12px;height:12px;"></i>
            </a>
            @if(!$documento->es_sellado && $documento->estado === 'borrador')
            <form method="POST" action="{{ route('admin.documentos.enviar', $documento) }}">
              @csrf
              <button type="submit" class="btn btn-xs btn-outline-info w-100" title="Enviar al cliente">
                <i data-lucide="send" style="width:12px;height:12px;"></i>
              </button>
            </form>
            @endif
            @if(!$documento->es_sellado && $documento->estado === 'enviado')
            <form method="POST" action="{{ route('admin.documentos.sellar', $documento) }}">
              @csrf
              <button type="submit" class="btn btn-xs btn-outline-dark w-100" title="Sellar documento">
                <i data-lucide="lock" style="width:12px;height:12px;"></i>
              </button>
            </form>
            @endif
            @if(!$documento->es_sellado)
            <form method="POST" action="{{ route('admin.documentos.destroy', $documento) }}"
              onsubmit="return confirm('¿Eliminar este documento?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-xs btn-outline-danger w-100">
                <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
              </button>
            </form>
            @endif
          </div>
        </div>
        {{-- Comentarios del documento --}}
        <div class="px-3 pb-2">
          <x-comentarios
            :comentarios="$documento->comentarios"
            store-route="admin.comentarios.store"
            delete-route="admin.comentarios.destroy"
            comentable-type="App\Models\Documento"
            :comentable-id="$documento->id"
            :current-user-id="auth()->id()"
            :is-super-admin="auth()->user()->isSuperAdmin()"
          />
        </div>
        </div>
        @empty
        <div class="text-center text-muted py-4 px-3">
          <i data-lucide="folder-open" style="width:32px;height:32px;" class="mb-2"></i>
          <p class="small">Sin documentos. <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalDocumento">Subir documento</button></p>
        </div>
        @endforelse
      </div>
    </div>

    <!-- Payments -->
    <div class="card grid-margin">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="credit-card" style="width:16px;height:16px;" class="me-2"></i>
          Pagos ({{ $proyecto->pagos->count() }})
        </h6>
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalPago">
          <i data-lucide="plus" style="width:14px;height:14px;"></i>
        </button>
      </div>
      <div class="card-body p-0">
        @forelse($proyecto->pagos as $pago)
        <div class="d-flex align-items-center px-3 py-2 border-bottom">
          <div class="flex-grow-1">
            <p class="mb-0 small fw-medium">{{ $pago->concepto }}</p>
            <div class="d-flex gap-1 align-items-center">
              <span class="badge bg-{{ $pago->estado_badge }} x-small">{{ $pago->estado_label }}</span>
              @if($pago->fecha_vencimiento)
                <span class="x-small text-muted">Vence: {{ $pago->fecha_vencimiento->format('d/m/Y') }}</span>
              @endif
            </div>
          </div>
          <div class="text-end">
            <p class="mb-0 fw-bold">${{ number_format($pago->monto, 2) }}</p>
            @if($pago->estado === 'pendiente')
            <button type="button" class="btn btn-xs btn-success"
              onclick="marcarPagado({{ $pago->id }})">Pagado</button>
            @endif
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4 px-3">
          <i data-lucide="credit-card" style="width:32px;height:32px;" class="mb-2"></i>
          <p class="small">Sin cobros. <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalPago">Generar cobro</button></p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ===== COTIZACIONES DEL PROYECTO ===== --}}
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="receipt" style="width:16px;height:16px;" class="me-2"></i>
          Cotizaciones del Proyecto ({{ $proyecto->cotizaciones->count() }})
        </h6>
        <a href="{{ route('admin.cotizaciones.create', ['proyecto_id' => $proyecto->id, 'cliente_id' => $proyecto->cliente_id]) }}"
           class="btn btn-sm btn-outline-primary">
          <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>Nueva Cotización
        </a>
      </div>
      @if($proyecto->cotizaciones->isEmpty())
      <div class="card-body text-center text-muted py-4">
        <i data-lucide="receipt" style="width:32px;height:32px;" class="mb-2"></i>
        <p class="small mb-2">No hay cotizaciones vinculadas a este proyecto.</p>
        <a href="{{ route('admin.cotizaciones.create', ['proyecto_id' => $proyecto->id, 'cliente_id' => $proyecto->cliente_id]) }}"
           class="btn btn-sm btn-primary">
          <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>Crear Cotización
        </a>
      </div>
      @else
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Nombre</th>
                <th>Estado</th>
                <th>Subtotal</th>
                <th>IVA</th>
                <th>Total</th>
                <th>Vencimiento</th>
                <th class="pe-3">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($proyecto->cotizaciones as $cot)
              <tr>
                <td class="ps-3 fw-medium small">{{ $cot->nombre }}</td>
                <td>
                  <span class="badge bg-{{ $cot->estado_badge }}">{{ ucfirst($cot->estado) }}</span>
                </td>
                <td class="small">${{ number_format($cot->subtotal, 2) }}</td>
                <td class="small">${{ number_format($cot->iva_monto, 2) }}</td>
                <td class="small fw-bold">${{ number_format($cot->total, 2) }}</td>
                <td class="small text-muted">{{ $cot->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                <td class="pe-3">
                  <div class="d-flex gap-1">
                    <a href="{{ route('admin.cotizaciones.edit', $cot) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                      <i data-lucide="edit" style="width:13px;height:13px;"></i>
                    </a>
                    <a href="{{ $cot->public_url }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver cotización pública">
                      <i data-lucide="external-link" style="width:13px;height:13px;"></i>
                    </a>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- ===== ACCESO COMPARTIDO ===== --}}
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="users" style="width:16px;height:16px;" class="me-2"></i>
          Acceso Compartido
          <span class="badge bg-secondary ms-1">{{ $proyecto->usuariosCompartidos->count() }}</span>
        </h6>
        @if($admins_disponibles->isNotEmpty())
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCompartir">
          <i data-lucide="user-plus" style="width:14px;height:14px;" class="me-1"></i>Compartir
        </button>
        @endif
      </div>
      <div class="card-body p-0">
        @if($proyecto->usuariosCompartidos->isEmpty())
        <div class="text-center text-muted py-3 small">
          <i data-lucide="lock" style="width:20px;height:20px;" class="mb-1"></i>
          <p class="mb-0">Solo el creador ({{ $proyecto->creadoPor?->name ?? 'desconocido' }}) tiene acceso.</p>
        </div>
        @else
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="ps-3">Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Desde</th>
                <th class="pe-3"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($proyecto->usuariosCompartidos as $u)
              <tr>
                <td class="ps-3 fw-medium small">{{ $u->name }}</td>
                <td class="small text-muted">{{ $u->email }}</td>
                <td>
                  <span class="badge {{ $u->pivot->rol === 'editor' ? 'bg-primary' : 'bg-secondary' }}">
                    {{ ucfirst($u->pivot->rol) }}
                  </span>
                </td>
                <td class="small text-muted">{{ $u->pivot->created_at?->format('d/m/Y') ?? '—' }}</td>
                <td class="pe-3">
                  <form method="POST" action="{{ route('admin.proyectos.quitar-usuario', [$proyecto, $u]) }}"
                    onsubmit="return confirm('¿Quitar acceso a {{ addslashes($u->name) }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-xs btn-outline-danger">
                      <i data-lucide="user-x" style="width:12px;height:12px;"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@include('admin.partials.nueva-entrega-modal', ['proyecto' => $proyecto])
@include('admin.partials.upload-documento-modal', ['proyecto' => $proyecto])
@include('admin.partials.nuevo-pago-modal', ['proyecto' => $proyecto])

{{-- ===== MODAL COMPARTIR PROYECTO ===== --}}
<div class="modal fade" id="modalCompartir" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.proyectos.compartir-usuario', $proyecto) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Compartir Proyecto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-3">Otorga acceso a otro administrador para gestionar este proyecto.</p>
          <div class="mb-3">
            <label class="form-label">Usuario <span class="text-danger">*</span></label>
            <select name="user_id" class="form-select" required>
              <option value="">Seleccionar usuario...</option>
              @foreach($admins_disponibles as $admin)
                <option value="{{ $admin->id }}">{{ $admin->name }} — {{ $admin->email }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nivel de acceso</label>
            <select name="rol" class="form-select">
              <option value="colaborador">Colaborador — puede ver y agregar entregas/documentos</option>
              <option value="editor">Editor — puede editar el proyecto completo</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="user-plus" style="width:14px;height:14px;" class="me-1"></i>Otorgar Acceso
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Mark Paid Modal -->
<div class="modal fade" id="modalMarcarPagado" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="formMarcarPagado" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Registrar Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Método de Pago <span class="text-danger">*</span></label>
            <select class="form-select" name="metodo_pago" required>
              <option value="">Seleccionar...</option>
              <option value="transferencia">Transferencia Bancaria</option>
              <option value="efectivo">Efectivo</option>
              <option value="codi">CoDi</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="cheque">Cheque</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Comprobante (opcional)</label>
            <input type="file" class="form-control" name="comprobante" accept=".pdf,.jpg,.jpeg,.png">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Confirmar Pago</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

{{-- ============================================================
     MODAL VISOR DE ARCHIVOS
     ============================================================ --}}
<div class="modal fade" id="modalVisorArchivo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0" style="background:#12121f;">
      <div class="modal-header border-0 px-3 py-2" style="background:#1e1e30; min-height:48px;">
        <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
          <i id="visorIcono" data-lucide="file" style="width:15px;height:15px;flex-shrink:0;color:#94a3b8;"></i>
          <span id="visorNombre" class="text-white small fw-medium text-truncate"></span>
        </div>
        <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
          <a id="visorBtnDescargar" href="#"
            class="btn btn-sm btn-outline-light py-1 px-2" style="display:none;font-size:12px;">
            <i data-lucide="download" style="width:13px;height:13px;" class="me-1"></i>Descargar
          </a>
          <a id="visorBtnNuevaPestana" href="#" target="_blank"
            class="btn btn-sm btn-outline-light py-1 px-2" style="font-size:12px;">
            <i data-lucide="external-link" style="width:13px;height:13px;" class="me-1"></i>Nueva pestaña
          </a>
          <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal"></button>
        </div>
      </div>
      <div class="modal-body p-0" style="height:80vh;position:relative;background:#0d0d1a;overflow:hidden;">

        <div id="visorLoading"
          style="position:absolute;inset:0;z-index:10;background:#0d0d1a;display:flex;align-items:center;justify-content:center;">
          <div class="text-center text-white">
            <div class="spinner-border text-primary mb-3" style="width:2.5rem;height:2.5rem;" role="status"></div>
            <p class="small mb-0" style="color:#94a3b8;">Cargando archivo…</p>
          </div>
        </div>

        <iframe id="visorPdf" src="about:blank"
          style="position:absolute;inset:0;width:100%;height:100%;border:none;opacity:0;transition:opacity .25s;">
        </iframe>

        <div id="visorImagenWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                 padding:16px;overflow:auto;opacity:0;transition:opacity .25s;">
          <img id="visorImagen" src="" alt=""
            style="max-width:100%;max-height:100%;object-fit:contain;border-radius:6px;
                   cursor:zoom-in;transition:transform .3s ease;"
            onclick="this.style.transform=this.style.transform?'':'scale(2)';
                     this.style.cursor=this.style.transform?'zoom-out':'zoom-in';">
        </div>

        <div id="visorVideoEmbedWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                 opacity:0;transition:opacity .25s;">
          <iframe id="visorVideoEmbed" src="about:blank"
            style="width:100%;height:100%;border:none;"
            allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture"
            allowfullscreen></iframe>
        </div>

        <div id="visorVideoWrapper"
          style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                 padding:16px;opacity:0;transition:opacity .25s;">
          <video id="visorVideo" controls style="max-width:100%;max-height:100%;border-radius:6px;">
            <source id="visorVideoSource" src="" type="video/mp4">
          </video>
        </div>

        <div id="visorFallback"
          style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                 padding:24px;opacity:0;transition:opacity .25s;">
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

@push('scripts')
<script>
function marcarPagado(pagoId) {
  const form = document.getElementById('formMarcarPagado');
  form.action = '{{ url("admin/pagos") }}/' + pagoId + '/marcar-pagado';
  new bootstrap.Modal(document.getElementById('modalMarcarPagado')).show();
}

// ── Visor universal ────────────────────────────────────────────────────
(function () {
  var PANELES = ['visorPdf','visorImagenWrapper','visorVideoEmbedWrapper','visorVideoWrapper','visorFallback'];

  function resetVisor() {
    document.getElementById('visorLoading').style.display = 'flex';
    PANELES.forEach(function(id) {
      var el = document.getElementById(id);
      el.style.opacity = '0';
      el.style.pointerEvents = 'none';
    });
    document.getElementById('visorBtnDescargar').style.display = 'none';
  }

  function mostrar(id) {
    document.getElementById('visorLoading').style.display = 'none';
    var el = document.getElementById(id);
    el.style.opacity = '1';
    el.style.pointerEvents = 'auto';
  }

  function mostrarError(descarga) {
    document.getElementById('visorFallbackBtn').href = descarga || '#';
    mostrar('visorFallback');
    if (window.lucide) lucide.createIcons();
  }

  function toEmbed(url) {
    var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
    if (yt) return 'https://www.youtube.com/embed/' + yt[1] + '?autoplay=1&rel=0';
    var vi = url.match(/vimeo\.com\/(\d+)/);
    if (vi) return 'https://player.vimeo.com/video/' + vi[1] + '?autoplay=1';
    return url;
  }

  document.querySelectorAll('.btn-visor').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var tipo     = this.dataset.tipo     || 'otro';
      var url      = this.dataset.url      || '';
      var descarga = this.dataset.download || '';
      var nombre   = this.dataset.nombre   || 'Archivo';

      document.getElementById('visorNombre').textContent = nombre;
      document.getElementById('visorBtnNuevaPestana').href = url;

      var iconos = { pdf:'file-text', imagen:'image', video_url:'play-circle', video_archivo:'video' };
      var iconoEl = document.getElementById('visorIcono');
      iconoEl.setAttribute('data-lucide', iconos[tipo] || 'file');
      if (window.lucide) lucide.createIcons({ nodes: [iconoEl] });

      resetVisor();
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalVisorArchivo')).show();

      if (tipo === 'pdf') {
        var iframe = document.getElementById('visorPdf');
        iframe.onload = function() {
          if (iframe.src && iframe.src !== 'about:blank') mostrar('visorPdf');
        };
        iframe.onerror = function() { mostrarError(descarga || url); };
        iframe.src = url;
        if (descarga) {
          var btnD = document.getElementById('visorBtnDescargar');
          btnD.href = descarga;
          btnD.style.display = 'inline-flex';
        }

      } else if (tipo === 'imagen') {
        var img = document.getElementById('visorImagen');
        img.style.transform = '';
        img.onload  = function() { mostrar('visorImagenWrapper'); };
        img.onerror = function() { mostrarError(descarga || url); };
        img.src = url;
        if (descarga) {
          var btnD = document.getElementById('visorBtnDescargar');
          btnD.href = descarga;
          btnD.style.display = 'inline-flex';
        }

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
        video.onerror   = function() { mostrarError(descarga || url); };
        source.src = url;
        video.load();

      } else {
        mostrarError(descarga || url);
      }
    });
  });

  document.getElementById('modalVisorArchivo').addEventListener('hidden.bs.modal', function() {
    var pdf   = document.getElementById('visorPdf');
    var embed = document.getElementById('visorVideoEmbed');
    var video = document.getElementById('visorVideo');
    pdf.onload = null; embed.onload = null; video.oncanplay = null;
    pdf.src = 'about:blank'; embed.src = 'about:blank';
    document.getElementById('visorImagen').src = '';
    document.getElementById('visorVideoSource').src = '';
    video.load();
    resetVisor();
  });
})();
</script>
@endpush
