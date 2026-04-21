@extends('admin.layouts.app')
@section('title', 'Mi Perfil')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Mi Perfil</h4>
    <p class="text-muted mb-0">Información de tu cuenta</p>
  </div>
  <a href="{{ route('admin.perfil.edit') }}" class="btn btn-primary">
    <i data-lucide="edit-2" style="width:16px;height:16px;" class="me-1"></i> Editar perfil
  </a>
</div>

<div class="row g-4">

  {{-- Columna izquierda: avatar + datos rápidos --}}
  <div class="col-md-4 col-xl-3">

    {{-- Card Avatar --}}
    <div class="card text-center mb-4">
      <div class="card-body py-4">
        @if($user->foto_url)
          <img src="{{ $user->foto_url }}" alt="{{ $user->name }}"
               class="rounded-circle mb-3 border"
               style="width:110px;height:110px;object-fit:cover;">
        @else
          <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
               style="width:110px;height:110px;font-size:2.2rem;font-weight:700;">
            {{ $user->iniciales }}
          </div>
        @endif
        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
        <p class="text-muted mb-2 small">{{ $user->cargo ?? 'Sin cargo asignado' }}</p>
        <span class="badge bg-{{ $user->role === 'superadmin' ? 'danger' : ($user->role === 'admin' ? 'primary' : 'secondary') }}">
          {{ $user->role_label }}
        </span>
      </div>
    </div>

    {{-- Card Info rápida --}}
    <div class="card">
      <div class="card-body">
        <h6 class="card-title fw-semibold mb-3">Información</h6>

        <div class="d-flex align-items-start mb-3">
          <i data-lucide="mail" class="text-muted me-2 mt-1" style="width:16px;height:16px;flex-shrink:0;"></i>
          <div>
            <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Email</p>
            <p class="mb-0 small">{{ $user->email }}</p>
          </div>
        </div>

        @if($user->telefono)
        <div class="d-flex align-items-start mb-3">
          <i data-lucide="phone" class="text-muted me-2 mt-1" style="width:16px;height:16px;flex-shrink:0;"></i>
          <div>
            <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Teléfono</p>
            <p class="mb-0 small">{{ $user->telefono }}</p>
          </div>
        </div>
        @endif

        <div class="d-flex align-items-start mb-3">
          <i data-lucide="calendar" class="text-muted me-2 mt-1" style="width:16px;height:16px;flex-shrink:0;"></i>
          <div>
            <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Miembro desde</p>
            <p class="mb-0 small">{{ $user->created_at->format('d M Y') }}</p>
          </div>
        </div>

        <div class="d-flex align-items-start">
          <i data-lucide="shield" class="text-muted me-2 mt-1" style="width:16px;height:16px;flex-shrink:0;"></i>
          <div>
            <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Estado</p>
            <p class="mb-0 small">
              <span class="badge bg-{{ $user->activo ? 'success' : 'secondary' }}">
                {{ $user->activo ? 'Activo' : 'Inactivo' }}
              </span>
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Columna derecha: bio + seguridad --}}
  <div class="col-md-8 col-xl-9">

    {{-- Bio --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="card-title fw-semibold mb-0">Sobre mí</h6>
          <a href="{{ route('admin.perfil.edit') }}" class="btn btn-sm btn-outline-secondary">
            <i data-lucide="edit-2" style="width:13px;height:13px;" class="me-1"></i> Editar
          </a>
        </div>
        @if($user->bio)
          <p class="text-muted mb-0">{{ $user->bio }}</p>
        @else
          <p class="text-muted fst-italic mb-0">No has añadido una descripción todavía.</p>
        @endif
      </div>
    </div>

    {{-- Seguridad --}}
    <div class="card">
      <div class="card-body">
        <h6 class="card-title fw-semibold mb-4">Seguridad</h6>

        <form action="{{ route('admin.perfil.password') }}" method="POST">
          @csrf @method('PUT')
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Contraseña actual</label>
              <input type="password" name="password_actual" class="form-control @error('password_actual') is-invalid @enderror"
                     placeholder="••••••••">
              @error('password_actual')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Nueva contraseña</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                     placeholder="Mínimo 8 caracteres">
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Confirmar contraseña</label>
              <input type="password" name="password_confirmation" class="form-control"
                     placeholder="Repite la contraseña">
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-warning">
              <i data-lucide="lock" style="width:15px;height:15px;" class="me-1"></i>
              Cambiar contraseña
            </button>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
@endsection
