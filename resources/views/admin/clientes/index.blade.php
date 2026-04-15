@extends('admin.layouts.app')

@section('title', 'Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Clientes</h4>
  </div>
  <div>
    <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nuevo Cliente
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
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Proyectos</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clientes as $cliente)
              <tr>
                <td>{{ $cliente->id }}</td>
                <td>
                  <a href="{{ route('admin.clientes.show', $cliente) }}" class="fw-medium text-body">
                    {{ $cliente->nombre_empresa }}
                  </a>
                </td>
                <td>{{ $cliente->nombre_contacto }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->telefono ?? '-' }}</td>
                <td>
                  <span class="badge bg-secondary">{{ $cliente->proyectos_count }}</span>
                </td>
                <td>
                  @if($cliente->activo)
                    <span class="badge bg-success">Activo</span>
                  @else
                    <span class="badge bg-secondary">Inactivo</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-sm btn-outline-primary" title="Ver expediente">
                      <i data-lucide="eye" style="width:14px;height:14px;"></i>
                    </a>
                    <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                      <i data-lucide="edit" style="width:14px;height:14px;"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.clientes.destroy', $cliente) }}" class="d-inline"
                      onsubmit="return confirm('¿Eliminar cliente {{ addslashes($cliente->nombre_empresa) }}? Esta acción no se puede deshacer.')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                        <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  No hay clientes registrados. <a href="{{ route('admin.clientes.create') }}">Crear el primero</a>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($clientes->hasPages())
      <div class="card-footer">
        {{ $clientes->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
