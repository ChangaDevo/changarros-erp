@extends('admin.layouts.app')

@section('title', 'Editar Marca')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Editar Marca</h4>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.marcas.show', $marca) }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-2"></i>Volver
    </a>
  </div>
</div>

<div class="row">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Información de la Marca</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.marcas.update', $marca) }}">
          @csrf @method('PUT')

          <div class="mb-3">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" value="{{ $marca->cliente->nombre_empresa ?? '—' }}" disabled>
            <div class="form-text">El cliente no se puede cambiar después de crear la marca.</div>
          </div>

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Marca <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
              id="nombre" name="nombre" value="{{ old('nombre', $marca->nombre) }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="tagline" class="form-label">Tagline / Slogan</label>
            <input type="text" class="form-control @error('tagline') is-invalid @enderror"
              id="tagline" name="tagline" value="{{ old('tagline', $marca->tagline) }}">
            @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="industria" class="form-label">Industria / Sector</label>
              <input type="text" class="form-control @error('industria') is-invalid @enderror"
                id="industria" name="industria" value="{{ old('industria', $marca->industria) }}">
              @error('industria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="sitio_web" class="form-label">Sitio Web</label>
              <input type="url" class="form-control @error('sitio_web') is-invalid @enderror"
                id="sitio_web" name="sitio_web" value="{{ old('sitio_web', $marca->sitio_web) }}" placeholder="https://">
              @error('sitio_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-4">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror"
              id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $marca->descripcion) }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Guardar Cambios
            </button>
            <a href="{{ route('admin.marcas.show', $marca) }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card border-danger">
      <div class="card-body">
        <h6 class="fw-semibold text-danger mb-2">
          <i data-lucide="trash-2" style="width:15px;height:15px;" class="me-1"></i>Zona Peligrosa
        </h6>
        <p class="text-muted small mb-3">
          Eliminar esta marca borrará permanentemente todos sus recursos y archivos. Esta acción no se puede deshacer.
        </p>
        <form method="POST" action="{{ route('admin.marcas.destroy', $marca) }}"
          onsubmit="return confirm('¿Seguro que deseas eliminar la marca «{{ addslashes($marca->nombre) }}»? Se borrarán TODOS sus archivos. Esta acción es irreversible.')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm">
            <i data-lucide="trash-2" style="width:14px;height:14px;" class="me-1"></i>Eliminar Marca
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
