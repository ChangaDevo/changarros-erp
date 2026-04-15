@extends('admin.layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Editar Cliente: {{ $cliente->nombre_empresa }}</h4>
  </div>
  <div>
    <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Información del Cliente</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.clientes.update', $cliente) }}">
          @csrf
          @method('PUT')
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre_empresa" class="form-label">Nombre de Empresa <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nombre_empresa') is-invalid @enderror"
                id="nombre_empresa" name="nombre_empresa" value="{{ old('nombre_empresa', $cliente->nombre_empresa) }}" required>
              @error('nombre_empresa')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="nombre_contacto" class="form-label">Nombre de Contacto <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('nombre_contacto') is-invalid @enderror"
                id="nombre_contacto" name="nombre_contacto" value="{{ old('nombre_contacto', $cliente->nombre_contacto) }}" required>
              @error('nombre_contacto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" name="email" value="{{ old('email', $cliente->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                id="telefono" name="telefono" value="{{ old('telefono', $cliente->telefono) }}">
              @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="rfc" class="form-label">RFC</label>
              <input type="text" class="form-control @error('rfc') is-invalid @enderror"
                id="rfc" name="rfc" value="{{ old('rfc', $cliente->rfc) }}" maxlength="13">
              @error('rfc')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Estado</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                  {{ old('activo', $cliente->activo) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Cliente Activo</label>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <textarea class="form-control @error('direccion') is-invalid @enderror"
              id="direccion" name="direccion" rows="2">{{ old('direccion', $cliente->direccion) }}</textarea>
            @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="notas" class="form-label">Notas Internas</label>
            <textarea class="form-control @error('notas') is-invalid @enderror"
              id="notas" name="notas" rows="3">{{ old('notas', $cliente->notas) }}</textarea>
            @error('notas')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <hr>
          <h6 class="mb-3">Configuración de Social Media</h6>
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="form-check form-switch mt-1">
                <input class="form-check-input" type="checkbox" id="es_cliente_interno" name="es_cliente_interno" value="1"
                  {{ old('es_cliente_interno', $cliente->es_cliente_interno) ? 'checked' : '' }} onchange="toggleDiasMinimos()">
                <label class="form-check-label" for="es_cliente_interno">
                  <strong>Cliente Interno</strong>
                  <span class="d-block text-muted small">Sin restricción de urgencia estándar</span>
                </label>
              </div>
            </div>
            <div class="col-md-6" id="dias-minimos-field">
              <label for="dias_minimos_publicacion" class="form-label">Días mínimos de anticipación</label>
              <input type="number" class="form-control @error('dias_minimos_publicacion') is-invalid @enderror"
                id="dias_minimos_publicacion" name="dias_minimos_publicacion"
                value="{{ old('dias_minimos_publicacion', $cliente->dias_minimos_publicacion) }}" min="0" max="30">
              @error('dias_minimos_publicacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">0 = sin límite mínimo</div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Guardar Cambios
            </button>
            <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDiasMinimos() {
  const interno = document.getElementById('es_cliente_interno').checked;
  const field   = document.getElementById('dias-minimos-field');
  const input   = document.getElementById('dias_minimos_publicacion');
  field.style.opacity = interno ? '.5' : '1';
  input.readOnly = interno;
  if (interno) input.value = 0;
}
document.addEventListener('DOMContentLoaded', toggleDiasMinimos);
</script>
@endpush
