@extends('admin.layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Editar Usuario</h4>
    <p class="text-muted small mb-0">{{ $usuario->email }}</p>
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
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
             style="width:36px;height:36px;font-size:14px;
                    background:{{ $usuario->role === 'superadmin' ? '#6f42c1' : ($usuario->role === 'admin' ? '#0d6efd' : '#198754') }}">
          {{ strtoupper(substr($usuario->name, 0, 1)) }}
        </div>
        <h5 class="card-title mb-0">{{ $usuario->name }}</h5>
        @if($usuario->id === auth()->id())
          <span class="badge bg-secondary ms-1">Tu cuenta</span>
        @endif
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
          @csrf
          @method('PUT')

          <div class="row g-3">
            {{-- Nombre --}}
            <div class="col-md-6">
              <label for="name" class="form-label">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Email --}}
            <div class="col-md-6">
              <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Rol --}}
            <div class="col-md-6">
              <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
              <select class="form-select @error('role') is-invalid @enderror"
                id="role" name="role" required onchange="toggleClienteField(this.value)"
                @if($usuario->id === auth()->id()) disabled @endif>
                <option value="superadmin" {{ old('role', $usuario->role) === 'superadmin' ? 'selected' : '' }}>
                  Super Admin
                </option>
                <option value="admin" {{ old('role', $usuario->role) === 'admin' ? 'selected' : '' }}>
                  Administrador (Estudio Creativo)
                </option>
                <option value="client" {{ old('role', $usuario->role) === 'client' ? 'selected' : '' }}>
                  Cliente
                </option>
              </select>
              @if($usuario->id === auth()->id())
                {{-- Si está deshabilitado, mandar el valor igual --}}
                <input type="hidden" name="role" value="{{ $usuario->role }}">
                <div class="form-text text-warning">No puedes cambiar tu propio rol.</div>
              @endif
              @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Estado --}}
            <div class="col-md-6">
              <label class="form-label d-block">Estado</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1"
                  {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                  @if($usuario->id === auth()->id()) disabled @endif>
                <label class="form-check-label" for="activo">Usuario activo</label>
              </div>
              @if($usuario->id === auth()->id())
                <input type="hidden" name="activo" value="1">
                <div class="form-text text-warning">No puedes desactivar tu propia cuenta.</div>
              @endif
            </div>

            {{-- Cliente (solo si rol = client) --}}
            <div class="col-12" id="cliente-field"
              style="{{ old('role', $usuario->role) === 'client' ? '' : 'display:none' }}">
              <label for="cliente_id" class="form-label">
                Cliente vinculado <span class="text-danger">*</span>
              </label>
              <select class="form-select @error('cliente_id') is-invalid @enderror"
                id="cliente_id" name="cliente_id">
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes as $cliente)
                  <option value="{{ $cliente->id }}"
                    {{ old('cliente_id', $usuario->cliente_id) == $cliente->id ? 'selected' : '' }}>
                    {{ $cliente->nombre_empresa }} — {{ $cliente->nombre_contacto }}
                  </option>
                @endforeach
              </select>
              @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Cambiar contraseña --}}
            <div class="col-12"><hr><p class="text-muted small mb-2">Dejar en blanco para no cambiar la contraseña.</p></div>

            <div class="col-md-6">
              <label for="password" class="form-label">Nueva contraseña</label>
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

            <div class="col-md-6">
              <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
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
              <i data-lucide="save" style="width:16px;height:16px;" class="me-1"></i>Guardar Cambios
            </button>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>

    {{-- Peligro: eliminar --}}
    @if($usuario->id !== auth()->id())
    <div class="card mt-3 border-danger">
      <div class="card-header text-danger"><h6 class="card-title mb-0">Zona de peligro</h6></div>
      <div class="card-body">
        <p class="small text-muted mb-3">Eliminar este usuario es permanente e irreversible.</p>
        <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}"
          onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm"
            @if($usuario->isSuperAdmin()) disabled @endif>
            <i data-lucide="trash-2" style="width:14px;height:14px;" class="me-1"></i>Eliminar Usuario
          </button>
          @if($usuario->isSuperAdmin())
            <span class="text-muted small ms-2">No se puede eliminar un Super Administrador.</span>
          @endif
        </form>
      </div>
    </div>
    @endif
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

// Init on load
document.addEventListener('DOMContentLoaded', function() {
  const role = document.getElementById('role');
  if (role) toggleClienteField(role.value);
});
</script>
@endpush
