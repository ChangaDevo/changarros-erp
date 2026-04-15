@extends('admin.layouts.app')

@section('title', 'Plantillas de Cotizaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Plantillas de Cotizaciones</h4>
  </div>
  <div>
    <a href="{{ route('admin.plantillas-cotizacion.create') }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nueva Plantilla
    </a>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
  {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-0">
        @forelse($plantillas as $plantilla)
        <div class="d-flex align-items-center px-4 py-3 border-bottom">
          <div class="me-3">
            <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center"
                 style="width:42px;height:42px;">
              <i data-lucide="layout-template" style="width:20px;height:20px;" class="text-primary"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <p class="mb-0 fw-semibold">{{ $plantilla->nombre }}</p>
            <p class="mb-0 text-muted small">
              {{ $plantilla->descripcion ?? 'Sin descripción' }}
              &nbsp;·&nbsp;
              <span class="badge bg-secondary">{{ $plantilla->items_count }} ítem(s)</span>
              @if($plantilla->creadoPor)
                &nbsp;·&nbsp; Por: {{ $plantilla->creadoPor->name }}
              @endif
            </p>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.plantillas-cotizacion.edit', $plantilla) }}" class="btn btn-sm btn-outline-primary">
              <i data-lucide="edit" style="width:14px;height:14px;"></i>
            </a>
            <form method="POST" action="{{ route('admin.plantillas-cotizacion.destroy', $plantilla) }}"
                  onsubmit="return confirm('¿Eliminar esta plantilla?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger">
                <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
              </button>
            </form>
          </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">
          <i data-lucide="layout-template" style="width:48px;height:48px;" class="mb-3"></i>
          <p>No hay plantillas creadas.</p>
          <a href="{{ route('admin.plantillas-cotizacion.create') }}" class="btn btn-primary">
            Crear primera plantilla
          </a>
        </div>
        @endforelse
      </div>
      @if($plantillas->hasPages())
      <div class="card-footer">{{ $plantillas->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
