@extends('admin.layouts.app')

@section('title', 'Expediente - ' . $cliente->nombre_empresa)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-1">{{ $cliente->nombre_empresa }}</h4>
    <p class="text-muted mb-0">Expediente Digital del Cliente</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-outline-secondary">
      <i data-lucide="edit" style="width:16px;height:16px;" class="me-2"></i>Editar
    </a>
    <a href="{{ route('admin.proyectos.create') }}?cliente_id={{ $cliente->id }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nuevo Proyecto
    </a>
  </div>
</div>

<div class="row">
  <!-- Client Info -->
  <div class="col-lg-4 grid-margin">
    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="user" style="width:16px;height:16px;" class="me-2"></i>Información
        </h6>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-5 text-muted small">Empresa</dt>
          <dd class="col-7 fw-medium">{{ $cliente->nombre_empresa }}</dd>

          <dt class="col-5 text-muted small">Contacto</dt>
          <dd class="col-7">{{ $cliente->nombre_contacto }}</dd>

          <dt class="col-5 text-muted small">Email</dt>
          <dd class="col-7">
            <a href="mailto:{{ $cliente->email }}" class="text-primary small">{{ $cliente->email }}</a>
          </dd>

          <dt class="col-5 text-muted small">Teléfono</dt>
          <dd class="col-7">{{ $cliente->telefono ?? '-' }}</dd>

          <dt class="col-5 text-muted small">RFC</dt>
          <dd class="col-7">{{ $cliente->rfc ?? '-' }}</dd>

          <dt class="col-5 text-muted small">Estado</dt>
          <dd class="col-7">
            @if($cliente->activo)
              <span class="badge bg-success">Activo</span>
            @else
              <span class="badge bg-secondary">Inactivo</span>
            @endif
          </dd>

          @if($cliente->direccion)
          <dt class="col-5 text-muted small">Dirección</dt>
          <dd class="col-7 small">{{ $cliente->direccion }}</dd>
          @endif

          @if($cliente->notas)
          <dt class="col-5 text-muted small">Notas</dt>
          <dd class="col-7 small text-muted">{{ $cliente->notas }}</dd>
          @endif
        </dl>
      </div>
    </div>

    <!-- Portal Access -->
    <div class="card mt-3">
      <div class="card-header">
        <h6 class="card-title mb-0">
          <i data-lucide="key" style="width:16px;height:16px;" class="me-2"></i>Acceso al Portal
        </h6>
      </div>
      <div class="card-body">
        @forelse($cliente->usuarios as $usuario)
        <div class="d-flex align-items-center mb-2">
          <div class="me-2">
            <img class="w-30px h-30px rounded-circle" src="https://placehold.co/30x30" alt="">
          </div>
          <div>
            <p class="mb-0 fw-medium small">{{ $usuario->name }}</p>
            <p class="mb-0 text-muted x-small">{{ $usuario->email }}</p>
          </div>
        </div>
        @empty
        <p class="text-muted small mb-2">Sin acceso al portal configurado.</p>
        @endforelse
        <div class="mt-2">
          <p class="text-muted small mb-0">
            <i data-lucide="info" style="width:12px;height:12px;" class="me-1"></i>
            Para agregar acceso, cree el cliente nuevamente con la opción habilitada o gestione desde la base de datos.
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Projects -->
  <div class="col-lg-8">
    <div class="card grid-margin">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">
          <i data-lucide="briefcase" style="width:16px;height:16px;" class="me-2"></i>
          Proyectos ({{ $cliente->proyectos->count() }})
        </h6>
        <a href="{{ route('admin.proyectos.create') }}" class="btn btn-sm btn-outline-primary">
          <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>Nuevo
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Monto</th>
                <th>Documentos</th>
                <th>Pagos</th>
                <th>Entregas</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($cliente->proyectos as $proyecto)
              <tr>
                <td>
                  <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="fw-medium text-body">
                    {{ $proyecto->nombre }}
                  </a>
                </td>
                <td>
                  <span class="badge bg-{{ $proyecto->estado_badge }}">{{ $proyecto->estado_label }}</span>
                </td>
                <td>{{ $proyecto->monto_total ? '$' . number_format($proyecto->monto_total, 2) : '-' }}</td>
                <td>
                  <span class="badge bg-secondary">{{ $proyecto->documentos->count() }}</span>
                </td>
                <td>
                  <span class="badge bg-{{ $proyecto->pagos->where('estado', 'pendiente')->count() > 0 ? 'warning' : 'secondary' }}">
                    {{ $proyecto->pagos->count() }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-{{ $proyecto->entregas->where('estado', 'enviado')->count() > 0 ? 'info' : 'secondary' }}">
                    {{ $proyecto->entregas->count() }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="btn btn-sm btn-outline-primary">
                    <i data-lucide="eye" style="width:14px;height:14px;"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  Sin proyectos.
                  <a href="{{ route('admin.proyectos.create') }}">Crear proyecto</a>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
