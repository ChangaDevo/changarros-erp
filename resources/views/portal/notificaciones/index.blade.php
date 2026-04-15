@extends('portal.layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <h4 class="mb-3 mb-md-0">Notificaciones</h4>
</div>

<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body p-0">
        @forelse($notificaciones as $notif)
        <div class="d-flex align-items-start px-4 py-3 border-bottom {{ $notif->estaLeida() ? '' : 'bg-primary-subtle' }}">
          <div class="me-3 flex-shrink-0 mt-1">
            <div class="rounded-circle bg-{{ $notif->tipo_color }}-subtle d-flex align-items-center justify-content-center"
                 style="width:38px;height:38px;">
              <i data-lucide="{{ $notif->tipo_icono }}" style="width:18px;height:18px;"
                 class="text-{{ $notif->tipo_color }}"></i>
            </div>
          </div>
          <div class="flex-grow-1">
            <p class="mb-1 fw-semibold small">{{ $notif->titulo }}</p>
            @if($notif->mensaje)
            <p class="mb-1 text-muted small">{{ $notif->mensaje }}</p>
            @endif
            <p class="mb-0 text-muted" style="font-size:11px;">{{ $notif->created_at->format('d/m/Y H:i') }} · {{ $notif->created_at->diffForHumans() }}</p>
          </div>
          @if($notif->url)
          <div class="ms-3 flex-shrink-0">
            <a href="{{ $notif->url }}" class="btn btn-sm btn-outline-primary">
              <i data-lucide="arrow-right" style="width:14px;height:14px;"></i>
            </a>
          </div>
          @endif
        </div>
        @empty
        <div class="text-center py-5 text-muted">
          <i data-lucide="bell-off" style="width:48px;height:48px;" class="mb-3"></i>
          <p>No tienes notificaciones aún.</p>
        </div>
        @endforelse
      </div>
      @if($notificaciones->hasPages())
      <div class="card-footer">{{ $notificaciones->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
