@extends('admin.layouts.app')

@section('title', 'Nueva Marca')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Nueva Marca</h4>
  </div>
  <div>
    <a href="{{ route('admin.marcas.index') }}" class="btn btn-outline-secondary">
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
        <form method="POST" action="{{ route('admin.marcas.store') }}">
          @csrf

          <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
            <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
              <option value="">— Selecciona un cliente —</option>
              @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                  {{ $cliente->nombre_empresa }}
                </option>
              @endforeach
            </select>
            @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Marca <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror"
              id="nombre" name="nombre" value="{{ old('nombre') }}" required placeholder="ej. Marca Oficial, Sub-marca, etc.">
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="mb-3">
            <label for="tagline" class="form-label">Tagline / Slogan</label>
            <input type="text" class="form-control @error('tagline') is-invalid @enderror"
              id="tagline" name="tagline" value="{{ old('tagline') }}" placeholder="ej. Innovación que transforma">
            @error('tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="industria" class="form-label">Industria / Sector</label>
              <input type="text" class="form-control @error('industria') is-invalid @enderror"
                id="industria" name="industria" value="{{ old('industria') }}" placeholder="ej. Tecnología, Salud, Retail…">
              @error('industria')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label for="sitio_web" class="form-label">Sitio Web</label>
              <input type="url" class="form-control @error('sitio_web') is-invalid @enderror"
                id="sitio_web" name="sitio_web" value="{{ old('sitio_web') }}" placeholder="https://">
              @error('sitio_web')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="mb-4">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control @error('descripcion') is-invalid @enderror"
              id="descripcion" name="descripcion" rows="3"
              placeholder="Breve descripción de la marca y su identidad…">{{ old('descripcion') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Crear Marca
            </button>
            <a href="{{ route('admin.marcas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card border-0 bg-light">
      <div class="card-body">
        <h6 class="fw-semibold mb-2"><i data-lucide="info" style="width:15px;height:15px;" class="me-1"></i> ¿Cómo funciona?</h6>
        <ul class="text-muted small mb-0 ps-3">
          <li class="mb-1">Crea la marca con la información básica.</li>
          <li class="mb-1">Luego sube <strong>logos</strong>, <strong>tipografías</strong>, <strong>colores</strong> y <strong>templates</strong>.</li>
          <li class="mb-1">Activa el <strong>acceso del cliente</strong> cuando quieras compartirla.</li>
          <li class="mb-1">El cliente verá todos los recursos desde un enlace público.</li>
          <li>Puedes exportar todos los archivos como <strong>ZIP</strong> en cualquier momento.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
