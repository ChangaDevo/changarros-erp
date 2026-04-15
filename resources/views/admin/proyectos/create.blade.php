@extends('admin.layouts.app')

@section('title', 'Nuevo Proyecto')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Nuevo Proyecto</h4>
  </div>
  <div>
    <a href="{{ route('admin.proyectos.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Información del Proyecto</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.proyectos.store') }}">
          @csrf
          <div class="row mb-3">
            <div class="col-md-8">
              <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
              <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}" {{ old('cliente_id', request('cliente_id')) == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre_empresa }} - {{ $c->nombre_contacto }}
                  </option>
                @endforeach
              </select>
              @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
              <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                <option value="cotizando" {{ old('estado', 'cotizando') == 'cotizando' ? 'selected' : '' }}>Cotizando</option>
                <option value="en_desarrollo" {{ old('estado') == 'en_desarrollo' ? 'selected' : '' }}>En Desarrollo</option>
                <option value="en_revision" {{ old('estado') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                <option value="aprobado" {{ old('estado') == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                <option value="finalizado" {{ old('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
              </select>
              @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Proyecto <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
              id="nombre" name="nombre" value="{{ old('nombre') }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror"
              id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="row mb-3">
            <div class="col-md-4">
              <label for="monto_total" class="form-label">Monto Total ($)</label>
              <input type="number" class="form-control @error('monto_total') is-invalid @enderror"
                id="monto_total" name="monto_total" value="{{ old('monto_total') }}" step="0.01" min="0">
              @error('monto_total')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
              <input type="date" class="form-control @error('fecha_inicio') is-invalid @enderror"
                id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
              @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
              <label for="fecha_entrega_estimada" class="form-label">Entrega Estimada</label>
              <input type="date" class="form-control @error('fecha_entrega_estimada') is-invalid @enderror"
                id="fecha_entrega_estimada" name="fecha_entrega_estimada" value="{{ old('fecha_entrega_estimada') }}">
              @error('fecha_entrega_estimada')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="mb-3">
            <label for="notas" class="form-label">Notas Internas</label>
            <textarea class="form-control @error('notas') is-invalid @enderror"
              id="notas" name="notas" rows="2">{{ old('notas') }}</textarea>
            @error('notas')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Crear Proyecto
            </button>
            <a href="{{ route('admin.proyectos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
