@extends('admin.layouts.app')

@section('title', 'Brief Creativo — ' . $proyecto->nombre)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Brief Creativo</h4>
    <p class="text-muted mb-0">
      <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="text-muted">
        {{ $proyecto->nombre }}
      </a>
      &nbsp;·&nbsp; {{ $proyecto->cliente->nombre_empresa }}
    </p>
  </div>
  <div>
    <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver al Proyecto
    </a>
  </div>
</div>

<form method="POST" action="{{ route('admin.proyectos.brief.update', $proyecto) }}">
  @csrf
  @method('PUT')

  <div class="row">
    <div class="col-lg-8">

      <div class="card mb-4">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i data-lucide="target" style="width:16px;height:16px;" class="me-2"></i>
            Objetivo y Audiencia
          </h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Objetivo de la campaña / proyecto</label>
            <textarea name="objetivo_campana" class="form-control @error('objetivo_campana') is-invalid @enderror"
              rows="3" placeholder="¿Qué se quiere lograr con este proyecto? (ventas, posicionamiento, awareness, etc.)">{{ old('objetivo_campana', $brief->objetivo_campana) }}</textarea>
            @error('objetivo_campana')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Público objetivo</label>
            <textarea name="publico_objetivo" class="form-control @error('publico_objetivo') is-invalid @enderror"
              rows="3" placeholder="Edad, género, intereses, ubicación, comportamientos de compra...">{{ old('publico_objetivo', $brief->publico_objetivo) }}</textarea>
            @error('publico_objetivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i data-lucide="palette" style="width:16px;height:16px;" class="me-2"></i>
            Identidad de Marca
          </h6>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Tono de voz</label>
              <input type="text" name="tono_voz" class="form-control @error('tono_voz') is-invalid @enderror"
                value="{{ old('tono_voz', $brief->tono_voz) }}"
                placeholder="Ej: Profesional, cercano, juvenil, formal...">
              @error('tono_voz')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Colores de marca</label>
              <input type="text" name="colores_marca" class="form-control @error('colores_marca') is-invalid @enderror"
                value="{{ old('colores_marca', $brief->colores_marca) }}"
                placeholder="Ej: #FF0000, azul marino, dorado...">
              @error('colores_marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Referencias e inspiración</label>
            <textarea name="referencias" class="form-control @error('referencias') is-invalid @enderror"
              rows="3" placeholder="URLs, nombres de marcas, campañas de referencia...">{{ old('referencias', $brief->referencias) }}</textarea>
            @error('referencias')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i data-lucide="trending-up" style="width:16px;height:16px;" class="me-2"></i>
            Contexto Competitivo y Entregables
          </h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Competencia</label>
            <textarea name="competencia" class="form-control @error('competencia') is-invalid @enderror"
              rows="2" placeholder="Principales competidores, marcas del sector...">{{ old('competencia', $brief->competencia) }}</textarea>
            @error('competencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Entregables esperados</label>
            <textarea name="entregables_esperados" class="form-control @error('entregables_esperados') is-invalid @enderror"
              rows="3" placeholder="Ej: 8 publicaciones por mes, 1 video de 30s, logo, manual de marca...">{{ old('entregables_esperados', $brief->entregables_esperados) }}</textarea>
            @error('entregables_esperados')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Presupuesto referencial ($)</label>
              <input type="number" name="presupuesto_referencial"
                class="form-control @error('presupuesto_referencial') is-invalid @enderror"
                value="{{ old('presupuesto_referencial', $brief->presupuesto_referencial) }}"
                step="0.01" min="0" placeholder="0.00">
              @error('presupuesto_referencial')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Observaciones adicionales</label>
            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
              rows="3" placeholder="Cualquier información relevante que deba conocer el equipo creativo...">{{ old('observaciones', $brief->observaciones) }}</textarea>
            @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">
          <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Guardar Brief
        </button>
        <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>

    </div>

    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header">
          <h6 class="card-title mb-0">Información del Proyecto</h6>
        </div>
        <div class="card-body">
          <p class="mb-1 small text-muted">Cliente</p>
          <p class="fw-semibold mb-3">{{ $proyecto->cliente->nombre_empresa }}</p>
          <p class="mb-1 small text-muted">Estado</p>
          <span class="badge bg-{{ $proyecto->estado_badge }} mb-3">{{ $proyecto->estado_label }}</span>
          @if($proyecto->fecha_entrega_estimada)
          <p class="mb-1 small text-muted">Entrega estimada</p>
          <p class="fw-semibold mb-0">{{ $proyecto->fecha_entrega_estimada->format('d/m/Y') }}</p>
          @endif
        </div>
      </div>
      @if($brief->exists)
      <div class="card">
        <div class="card-body text-center py-3">
          <p class="text-muted small mb-1">Completado</p>
          <h3 class="mb-0 text-primary">{{ $brief->camposLlenos() }}/{{ $brief->totalCampos() }}</h3>
          <p class="text-muted small mb-0">campos llenos</p>
          @if($brief->actualizado_por)
          <hr class="my-2">
          <p class="text-muted small mb-0">Última edición:<br>
            <strong>{{ $brief->actualizadoPor?->name ?? 'Sistema' }}</strong><br>
            {{ $brief->updated_at->format('d/m/Y H:i') }}
          </p>
          @endif
        </div>
      </div>
      @endif
    </div>
  </div>
</form>
@endsection
