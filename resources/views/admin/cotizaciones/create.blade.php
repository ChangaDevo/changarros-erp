@extends('admin.layouts.app')

@section('title', 'Nueva Cotización')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Nueva Cotización</h4>
  </div>
  <div>
    @if($preProyecto)
      <a href="{{ route('admin.proyectos.show', $preProyecto) }}" class="btn btn-outline-secondary me-2">
        <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i>
        Volver al Proyecto
      </a>
    @else
      <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-outline-secondary">
        <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i>
        Volver
      </a>
    @endif
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Información de la Cotización</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.cotizaciones.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Nombre / Título <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre') }}"
                   placeholder="Ej: Diseño de identidad corporativa"
                   required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Cliente <span class="text-danger">*</span></label>
            <select name="cliente_id" id="clienteSelect"
                    class="form-select @error('cliente_id') is-invalid @enderror" required>
              <option value="">Seleccionar cliente...</option>
              @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}"
                  {{ (old('cliente_id', $preCliente) == $cliente->id) ? 'selected' : '' }}>
                  {{ $cliente->nombre_empresa }}
                </option>
              @endforeach
            </select>
            @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Proyecto (opcional)</label>
            <select name="proyecto_id" id="proyectoSelect" class="form-select">
              <option value="">Sin proyecto asociado</option>
              @foreach($proyectos as $proyecto)
                <option value="{{ $proyecto->id }}"
                  {{ (old('proyecto_id', $preProyecto) == $proyecto->id) ? 'selected' : '' }}>
                  {{ $proyecto->nombre }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">IVA % <span class="text-danger">*</span></label>
              <input type="number" name="iva_porcentaje" class="form-control"
                     value="{{ old('iva_porcentaje', 16) }}" min="0" max="100" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Fecha de Vencimiento</label>
              <input type="date" name="fecha_vencimiento" class="form-control"
                     value="{{ old('fecha_vencimiento') }}">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Notas / Términos</label>
            <textarea name="notas" class="form-control" rows="3"
                      placeholder="Condiciones, plazos, notas adicionales...">{{ old('notas') }}</textarea>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:15px;height:15px;" class="me-1"></i>
              Crear Cotización
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const proyectosUrl = '{{ url("admin/cotizaciones-clientes") }}';
const preProyecto  = '{{ $preProyecto ?? "" }}';

document.getElementById('clienteSelect').addEventListener('change', function () {
  const clienteId = this.value;
  const sel = document.getElementById('proyectoSelect');
  sel.innerHTML = '<option value="">Sin proyecto asociado</option>';

  if (!clienteId) return;

  fetch(`${proyectosUrl}/${clienteId}/proyectos`, {
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  })
  .then(r => r.json())
  .then(data => {
    data.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id;
      opt.textContent = p.nombre;
      if (preProyecto && p.id == preProyecto) opt.selected = true;
      sel.appendChild(opt);
    });
  });
});
</script>
@endpush
