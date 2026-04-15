@extends('admin.layouts.app')

@section('title', 'Proyectos')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Proyectos</h4>
  </div>
  <div>
    <a href="{{ route('admin.proyectos.create') }}" class="btn btn-primary">
      <i data-lucide="plus" style="width:16px;height:16px;" class="me-2"></i>Nuevo Proyecto
    </a>
  </div>
</div>

@forelse($clientes_con_proyectos as $cliente)
<div class="card mb-4">
  {{-- Cabecera del cliente --}}
  <div class="card-header d-flex align-items-center gap-2 py-2">
    <i data-lucide="building-2" style="width:16px;height:16px;" class="text-muted flex-shrink-0"></i>
    <a href="{{ route('admin.clientes.show', $cliente) }}" class="fw-semibold text-body text-decoration-none me-1">
      {{ $cliente->nombre_empresa }}
    </a>
    @if($cliente->es_cliente_interno)
      <span class="badge bg-info" style="font-size:10px;">Cliente Interno</span>
    @endif
    <span class="text-muted small">· {{ $cliente->nombre_contacto }}</span>
    <div class="ms-auto d-flex gap-2 align-items-center">
      <span class="badge bg-secondary">{{ $cliente->proyectos->count() }} {{ Str::plural('proyecto', $cliente->proyectos->count()) }}</span>
      <a href="{{ route('admin.proyectos.create', ['cliente_id' => $cliente->id]) }}" class="btn btn-xs btn-outline-primary py-0 px-2" title="Nuevo proyecto para este cliente">
        <i data-lucide="plus" style="width:12px;height:12px;"></i>
      </a>
    </div>
  </div>

  {{-- Proyectos del cliente --}}
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th class="ps-3">Proyecto</th>
            <th>Estado</th>
            <th>Cotizaciones</th>
            <th>Entregas</th>
            <th>Monto Total</th>
            <th>Entrega Est.</th>
            <th>Acceso compartido</th>
            <th class="pe-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cliente->proyectos as $proyecto)
          <tr>
            <td class="ps-3">
              <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="fw-medium text-body">
                {{ $proyecto->nombre }}
              </a>
              @if($proyecto->notas)
                <i data-lucide="message-square" style="width:12px;height:12px;" class="text-muted ms-1" title="{{ Str::limit($proyecto->notas, 80) }}"></i>
              @endif
            </td>
            <td>
              <span class="badge bg-{{ $proyecto->estado_badge }}">{{ $proyecto->estado_label }}</span>
            </td>
            <td>
              @if($proyecto->cotizaciones->isNotEmpty())
                @foreach($proyecto->cotizaciones as $cot)
                  <a href="{{ route('admin.cotizaciones.edit', $cot) }}"
                     class="badge bg-{{ $cot->estado_badge }} text-decoration-none me-1"
                     title="{{ $cot->nombre }}">
                    ${{ number_format($cot->total, 0) }}
                  </a>
                @endforeach
              @else
                <a href="{{ route('admin.cotizaciones.create', ['proyecto_id' => $proyecto->id, 'cliente_id' => $proyecto->cliente_id]) }}"
                   class="btn btn-xs btn-outline-secondary py-0" title="Crear cotización para este proyecto">
                  <i data-lucide="plus" style="width:11px;height:11px;"></i> Cotizar
                </a>
              @endif
            </td>
            <td class="small text-muted">
              {{ $proyecto->entregas_count }}
              @if($proyecto->entregas_count > 0)
                <span class="text-muted">/{{ $proyecto->pagos_count }} pagos</span>
              @endif
            </td>
            <td class="small">
              {{ $proyecto->monto_total ? '$' . number_format($proyecto->monto_total, 2) : '—' }}
            </td>
            <td class="small">
              @if($proyecto->fecha_entrega_estimada)
                <span class="{{ $proyecto->fecha_entrega_estimada->isPast() && !in_array($proyecto->estado, ['finalizado']) ? 'text-danger fw-bold' : 'text-muted' }}">
                  {{ $proyecto->fecha_entrega_estimada->format('d/m/Y') }}
                </span>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
            <td>
              @if($proyecto->usuariosCompartidos->isNotEmpty())
                <div class="d-flex gap-1 flex-wrap">
                  @foreach($proyecto->usuariosCompartidos as $u)
                    <span class="badge bg-secondary" style="font-size:10px;" title="{{ $u->pivot->rol }}">
                      {{ strtoupper(substr($u->name, 0, 1)) }} {{ Str::limit($u->name, 10) }}
                    </span>
                  @endforeach
                </div>
              @else
                <span class="text-muted small">—</span>
              @endif
            </td>
            <td class="pe-3">
              <div class="d-flex gap-1">
                <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="btn btn-sm btn-outline-primary" title="Ver proyecto">
                  <i data-lucide="eye" style="width:14px;height:14px;"></i>
                </a>
                <a href="{{ route('admin.proyectos.edit', $proyecto) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                  <i data-lucide="edit" style="width:14px;height:14px;"></i>
                </a>
                <form method="POST" action="{{ route('admin.proyectos.destroy', $proyecto) }}" class="d-inline"
                  onsubmit="return confirm('¿Eliminar proyecto {{ addslashes($proyecto->nombre) }}?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@empty
<div class="card">
  <div class="card-body text-center py-5 text-muted">
    <i data-lucide="inbox" style="width:40px;height:40px;" class="mb-3"></i>
    <p class="mb-0">No hay proyectos registrados. <a href="{{ route('admin.proyectos.create') }}">Crear el primero</a></p>
  </div>
</div>
@endforelse
@endsection
