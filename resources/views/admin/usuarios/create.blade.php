@extends('admin.layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Nuevo Usuario</h4>
  </div>
  <div>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i>Volver
    </a>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Datos del Usuario</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.usuarios.store') }}">
          @csrf

          <div class="row g-3">
            {{-- Nombre --}}
            <div class="col-md-6">
              <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
              <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" name="email" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Rol --}}
            <div class="col-md-6">
              <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
              <select class="form-select @error('role') is-invalid @enderror"
                id="role" name="role" required onchange="toggleClienteField(this.value)">
                <option value="">Seleccionar rol...</option>
                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>
                  Super Admin
                </option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                  Administrador (Estudio Creativo)
                </option>
                <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>
                  Cliente
                </option>
              </select>
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Estado --}}
            <div class="col-md-6">
              <label class="form-label d-block">Estado</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                  {{ old('activo', '1') ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Usuario activo</label>
              </div>
            </div>

            {{-- Cliente (solo visible si rol = client) --}}
            <div class="col-12" id="cliente-field" style="{{ old('role') === 'client' ? '' : 'display:none' }}">
              <label for="cliente_id" class="form-label">
                Cliente vinculado <span class="text-danger">*</span>
              </label>
              <select class="form-select @error('cliente_id') is-invalid @enderror"
                id="cliente_id" name="cliente_id">
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes as $cliente)
                  <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                    {{ $cliente->nombre_empresa }} — {{ $cliente->nombre_contacto }}
                  </option>
                @endforeach
              </select>
              @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">Este usuario accederá al portal del cliente seleccionado.</div>
            </div>

            {{-- Divider --}}
            <div class="col-12"><hr></div>

            {{-- Contraseña --}}
            <div class="col-md-6">
              <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                  id="password" name="password" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')">
                  <i data-lucide="eye" style="width:14px;height:14px;" id="icon-password"></i>
                </button>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="form-text">Mínimo 8 caracteres.</div>
            </div>

            {{-- Confirmar contraseña --}}
            <div class="col-md-6">
              <label for="password_confirmation" class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control"
                  id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password_confirmation')">
                  <i data-lucide="eye" style="width:14px;height:14px;" id="icon-password_confirmation"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="user-plus" style="width:16px;height:16px;" class="me-1"></i>Crear Usuario
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>

    {{-- Info de roles --}}
    <div class="card mt-3">
      <div class="card-header"><h6 class="card-title mb-0">Descripción de roles</h6></div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex align-items-start gap-2 py-3">
            <span class="badge mt-1" style="background:#6f42c1;">Super Admin</span>
            <div>
              <p class="mb-0 small">Acceso total al sistema. Es el único que puede crear, editar y eliminar usuarios. Gestiona todos los módulos.</p>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-start gap-2 py-3">
            <span class="badge bg-primary mt-1">Administrador</span>
            <div>
              <p class="mb-0 small">Acceso al panel de administración. Gestiona clientes, proyectos, pagos, cotizaciones y redes sociales. No puede gestionar usuarios.</p>
            </div>
          </li>
          <li class="list-group-item d-flex align-items-start gap-2 py-3">
            <span class="badge bg-success mt-1">Cliente</span>
            <div>
              <p class="mb-0 small">Acceso solo al portal del cliente. Puede revisar sus proyectos, aprobar entregas y documentos, y ver sus pagos.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function toggleClienteField(role) {
  document.getElementById('cliente-field').style.display = role === 'client' ? '' : 'none';
  document.getElementById('cliente_id').required = role === 'client';
}

function togglePass(fieldId) {
  const input = document.getElementById(fieldId);
  const icon = document.getElementById('icon-' + fieldId);
  if (input.type === 'password') {
    input.type = 'text';
    icon.setAttribute('data-lucide', 'eye-off');
  } else {
    input.type = 'password';
    icon.setAttribute('data-lucide', 'eye');
  }
  lucide.createIcons();
}
</script>
@endpush
