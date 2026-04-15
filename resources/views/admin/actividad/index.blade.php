@extends('admin.layouts.app')

@section('title', 'Registro de Actividad')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Registro de Actividad</h4>
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
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Descripción</th>
                <th>Modelo</th>
                <th>IP</th>
              </tr>
            </thead>
            <tbody>
              @forelse($actividades as $actividad)
              <tr>
                <td class="small text-muted text-nowrap">
                  {{ $actividad->created_at ? $actividad->created_at->format('d/m/Y H:i') : '-' }}
                </td>
                <td>
                  @if($actividad->usuario)
                    <span class="fw-medium small">{{ $actividad->usuario->name }}</span>
                    <br><span class="text-muted x-small">{{ $actividad->usuario->email }}</span>
                  @else
                    <span class="text-muted small">Sistema</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-primary font-monospace">{{ $actividad->accion }}</span>
                </td>
                <td class="small">{{ $actividad->descripcion }}</td>
                <td class="small text-muted">
                  @if($actividad->modelo_tipo && $actividad->modelo_id)
                    {{ $actividad->modelo_tipo }} #{{ $actividad->modelo_id }}
                  @else
                    -
                  @endif
                </td>
                <td class="small text-muted font-monospace">{{ $actividad->ip_address ?? '-' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Sin actividad registrada.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($actividades->hasPages())
      <div class="card-footer">
        {{ $actividades->links() }}
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
