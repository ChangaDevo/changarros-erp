@extends('admin.layouts.app')

@section('title', 'Marcas & Branding')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">Marcas & Branding</h4>
    <p class="text-muted mb-0">Gestiona los recursos de identidad visual de tus clientes.</p>
  </div>
  <div>
    <a href="{{ route('admin.marcas.create') }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nueva Marca
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" style="width:16px;height:16px;" class="me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i data-lucide="alert-circle" style="width:16px;height:16px;" class="me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

@if($marcas->isEmpty())
  <div class="card">
    <div class="card-body text-center py-5">
      <i data-lucide="layers" style="width:48px;height:48px;" class="text-muted mb-3 d-block mx-auto"></i>
      <h5 class="text-muted">Sin marcas registradas</h5>
      <p class="text-muted">Crea la primera marca para comenzar a organizar los recursos de identidad visual.</p>
      <a href="{{ route('admin.marcas.create') }}" class="btn btn-primary">
        <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nueva Marca
      </a>
    </div>
  </div>
@else
  <div class="row g-4">
    @foreach($marcas as $marca)
    <div class="col-sm-6 col-xl-4">
      <div class="card h-100 marca-card">
        {{-- Thumbnail --}}
        <div class="marca-thumb d-flex align-items-center justify-content-center bg-light"
             style="height:160px; overflow:hidden; border-radius: .5rem .5rem 0 0;">
          @if($marca->logo_thumb)
            <img src="{{ $marca->logo_thumb }}" alt="{{ $marca->nombre }}"
                 style="max-height:140px; max-width:100%; object-fit:contain; padding:1rem;">
          @else
            <i data-lucide="image" style="width:48px;height:48px;" class="text-muted opacity-50"></i>
          @endif
        </div>

        <div class="card-body d-flex flex-column">
          <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
              <h6 class="fw-bold mb-1">{{ $marca->nombre }}</h6>
              <small class="text-muted">{{ $marca->cliente->nombre_empresa ?? '—' }}</small>
            </div>
            @if($marca->acceso_cliente)
              <span class="badge bg-success">Público</span>
            @else
              <span class="badge bg-secondary">Privado</span>
            @endif
          </div>

          @if($marca->tagline)
            <p class="text-muted small mb-2 fst-italic">"{{ $marca->tagline }}"</p>
          @endif

          @if($marca->industria)
            <span class="badge bg-light text-dark border mb-2 me-auto">{{ $marca->industria }}</span>
          @endif

          <div class="d-flex align-items-center gap-2 text-muted small mt-auto mb-3">
            <i data-lucide="paperclip" style="width:13px;height:13px;"></i>
            {{ $marca->recursos_count }} {{ $marca->recursos_count === 1 ? 'recurso' : 'recursos' }}
          </div>

          <div class="d-flex gap-1 mt-auto">
            <a href="{{ route('admin.marcas.show', $marca) }}" class="btn btn-sm btn-primary flex-fill">
              <i data-lucide="eye" style="width:13px;height:13px;" class="me-1"></i>Ver
            </a>
            <a href="{{ route('admin.marcas.edit', $marca) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
              <i data-lucide="edit" style="width:13px;height:13px;"></i>
            </a>
            <form method="POST" action="{{ route('admin.marcas.destroy', $marca) }}" class="d-inline"
              onsubmit="return confirm('¿Eliminar la marca «{{ addslashes($marca->nombre) }}»? Esta acción no se puede deshacer.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  @if($marcas->hasPages())
  <div class="mt-4 d-flex justify-content-center">
    {{ $marcas->links() }}
  </div>
  @endif
@endif
@endsection

@push('style')
<style>
.marca-card { transition: box-shadow .2s; }
.marca-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.12); }
</style>
@endpush
