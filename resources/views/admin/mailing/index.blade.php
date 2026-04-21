@extends('admin.layouts.app')
@section('title', 'Mailing')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Mailing</h4>
    <p class="text-muted mb-0">Gestión de campañas de correo masivo</p>
  </div>
  <a href="{{ route('admin.mailing.create') }}" class="btn btn-primary">
    <i data-lucide="plus" style="width:16px;height:16px;" class="me-1"></i> Nueva campaña
  </a>
</div>

<div class="card">
  <div class="card-body p-0">
    @if($campanas->isEmpty())
      <div class="text-center py-5">
        <i data-lucide="send" style="width:48px;height:48px;color:#dee2e6;" class="mb-3 d-block mx-auto"></i>
        <p class="text-muted mb-3">No hay campañas todavía</p>
        <a href="{{ route('admin.mailing.create') }}" class="btn btn-primary btn-sm">Crear primera campaña</a>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Campaña</th>
              <th>Cliente</th>
              <th>Estado</th>
              <th>Progreso</th>
              <th>Contactos</th>
              <th>Creada</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($campanas as $campana)
            <tr>
              <td>
                <div class="fw-semibold">{{ $campana->titulo }}</div>
                <small class="text-muted">{{ $campana->asunto }}</small>
              </td>
              <td>{{ $campana->cliente->nombre_empresa ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $campana->estado_badge }}">
                  {{ ucfirst($campana->estado) }}
                </span>
              </td>
              <td style="min-width:140px;">
                <div class="d-flex align-items-center gap-2">
                  <div class="progress flex-grow-1" style="height:6px;">
                    <div class="progress-bar bg-{{ $campana->estado_badge }}"
                         style="width:{{ $campana->porcentaje_enviado }}%"></div>
                  </div>
                  <small class="text-muted">{{ $campana->porcentaje_enviado }}%</small>
                </div>
              </td>
              <td>
                <span class="text-success">{{ $campana->total_enviados }}</span>
                / {{ $campana->total_contactos }}
                @if($campana->total_errores > 0)
                  <span class="text-danger ms-1">({{ $campana->total_errores }} errores)</span>
                @endif
              </td>
              <td><small class="text-muted">{{ $campana->created_at->format('d/m/Y') }}</small></td>
              <td class="text-end">
                <a href="{{ route('admin.mailing.show', $campana) }}" class="btn btn-sm btn-outline-primary">
                  <i data-lucide="eye" style="width:14px;height:14px;"></i>
                </a>
                @if($campana->estado !== 'enviada')
                <a href="{{ route('admin.mailing.edit', $campana) }}" class="btn btn-sm btn-outline-secondary">
                  <i data-lucide="edit-2" style="width:14px;height:14px;"></i>
                </a>
                @endif
                <form action="{{ route('admin.mailing.destroy', $campana) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('¿Eliminar campaña?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $campanas->links() }}</div>
    @endif
  </div>
</div>
@endsection
