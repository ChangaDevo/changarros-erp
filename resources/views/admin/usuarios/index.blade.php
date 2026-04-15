@extends('admin.layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Gestión de Usuarios</h4>
    <p class="text-muted small mb-0">Solo el Super Administrador puede crear, editar o eliminar usuarios.</p>
  </div>
  <div>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
      <i data-lucide="user-plus" style="width:16px;height:16px;" class="me-2"></i>Nuevo Usuario
    </a>
  </div>
</div>

<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Cliente vinculado</th>
                <th>Estado</th>
                <th>Creado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($usuarios as $usuario)
              <tr>
                <td>{{ $usuario->id }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:32px;height:32px;min-width:32px;font-size:13px;
                                background:{{ $usuario->role === 'superadmin' ? '#6f42c1' : ($usuario->role === 'admin' ? '#0d6efd' : '#198754') }}">
                      {{ strtoupper(substr($usuario->name, 0, 1)) }}
                    </div>
                    <span class="fw-medium">{{ $usuario->name }}</span>
                    @if($usuario->id === auth()->id())
                      <span class="badge bg-secondary" style="font-size:10px;">Tú</span>
                    @endif
                  </div>
                </td>
                <td>{{ $usuario->email }}</td>
                <td>
                  @if($usuario->role === 'superadmin')
                    <span class="badge" style="background:#6f42c1;">
                      <i data-lucide="shield" style="width:11px;height:11px;" class="me-1"></i>Super Admin
                    </span>
                  @elseif($usuario->role === 'admin')
                    <span class="badge bg-primary">
                      <i data-lucide="settings" style="width:11px;height:11px;" class="me-1"></i>Administrador
                    </span>
                  @else
                    <span class="badge bg-success">
                      <i data-lucide="user" style="width:11px;height:11px;" class="me-1"></i>Cliente
                    </span>
                  @endif
                </td>
                <td>
                  @if($usuario->cliente)
                    <a href="{{ route('admin.clientes.show', $usuario->cliente) }}" class="text-body">
                      {{ $usuario->cliente->nombre_empresa }}
                    </a>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td>
                  @if($usuario->activo)
                    <span class="badge bg-success">Activo</span>
                  @else
                    <span class="badge bg-secondary">Inactivo</span>
                  @endif
                </td>
                <td class="text-muted small">{{ $usuario->created_at->format('d/m/Y') }}</td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary" title="Editar usuario">
                      <i data-lucide="edit" style="width:14px;height:14px;"></i>
                    </a>
                    @if($usuario->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="d-inline"
                      onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($usuario->name) }}? Esta acción no se puede deshacer.')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"
                        @if($usuario->role === 'superadmin') disabled title="No se puede eliminar el Super Administrador" @endif>
                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                      </button>
                    </form>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  No hay usuarios registrados.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($usuarios->hasPages())
      <div class="card-footer">
        {{ $usuarios->links() }}
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Resumen de roles --}}
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:#6f42c1;">
          <i data-lucide="shield" class="text-white" style="width:20px;height:20px;"></i>
        </div>
        <div>
          <p class="mb-0 fw-bold" style="font-size:22px;">{{ $usuarios->getCollection()->where('role','superadmin')->count() }}</p>
          <p class="text-muted small mb-0">Super Admin</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary" style="width:44px;height:44px;">
          <i data-lucide="settings" class="text-white" style="width:20px;height:20px;"></i>
        </div>
        <div>
          <p class="mb-0 fw-bold" style="font-size:22px;">{{ $usuarios->getCollection()->where('role','admin')->count() }}</p>
          <p class="text-muted small mb-0">Administradores</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success" style="width:44px;height:44px;">
          <i data-lucide="users" class="text-white" style="width:20px;height:20px;"></i>
        </div>
        <div>
          <p class="mb-0 fw-bold" style="font-size:22px;">{{ $usuarios->getCollection()->where('role','client')->count() }}</p>
          <p class="text-muted small mb-0">Clientes</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
