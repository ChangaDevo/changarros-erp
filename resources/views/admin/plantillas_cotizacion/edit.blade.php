@extends('admin.layouts.app')

@section('title', 'Editar Plantilla')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Editar Plantilla: {{ $plantilla->nombre }}</h4>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.plantillas-cotizacion.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver
    </a>
    <form method="POST" action="{{ route('admin.plantillas-cotizacion.destroy', $plantilla) }}"
          onsubmit="return confirm('¿Eliminar esta plantilla?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-outline-danger">
        <i data-lucide="trash-2" style="width:16px;height:16px;" class="me-2"></i>Eliminar
      </button>
    </form>
  </div>
</div>

<form method="POST" action="{{ route('admin.plantillas-cotizacion.update', $plantilla) }}" id="formPlantilla">
  @csrf @method('PUT')
  @include('admin.plantillas_cotizacion._form', ['plantilla' => $plantilla])
</form>
@endsection

@push('scripts')
@include('admin.plantillas_cotizacion._scripts')
@endpush
