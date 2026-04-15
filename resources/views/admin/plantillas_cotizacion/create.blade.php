@extends('admin.layouts.app')

@section('title', 'Nueva Plantilla de Cotización')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Nueva Plantilla de Cotización</h4>
  </div>
  <div>
    <a href="{{ route('admin.plantillas-cotizacion.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver
    </a>
  </div>
</div>

<form method="POST" action="{{ route('admin.plantillas-cotizacion.store') }}" id="formPlantilla">
  @csrf
  @include('admin.plantillas_cotizacion._form', ['plantilla' => $plantilla])
</form>
@endsection

@push('scripts')
@include('admin.plantillas_cotizacion._scripts')
@endpush
