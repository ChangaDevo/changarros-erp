@props([
    'comentarios',
    'storeRoute',
    'comentableType',
    'comentableId',
    'deleteRoute' => null,
    'currentUserId' => null,
    'isSuperAdmin' => false,
])

<div class="mt-3 border-top pt-3">
  <p class="text-muted small fw-semibold mb-2">
    <i data-lucide="message-circle" style="width:14px;height:14px;" class="me-1"></i>
    Comentarios ({{ $comentarios->count() }})
  </p>

  {{-- Lista de comentarios --}}
  @forelse($comentarios as $comentario)
  <div class="d-flex gap-2 mb-2">
    <div class="flex-shrink-0">
      <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
           style="width:30px;height:30px;font-size:12px;">
        {{ strtoupper(substr($comentario->autor->name ?? 'U', 0, 1)) }}
      </div>
    </div>
    <div class="flex-grow-1">
      <div class="bg-light rounded p-2">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <span class="small fw-semibold">{{ $comentario->autor->name ?? 'Usuario' }}</span>
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted" style="font-size:11px;">{{ $comentario->created_at->diffForHumans() }}</span>
            @if($deleteRoute && ($comentario->user_id === $currentUserId || $isSuperAdmin))
            <form method="POST" action="{{ route($deleteRoute, $comentario) }}"
                  onsubmit="return confirm('¿Eliminar este comentario?')" class="d-inline">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-link p-0 text-danger" style="font-size:11px;" title="Eliminar">
                <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
              </button>
            </form>
            @endif
          </div>
        </div>
        <p class="mb-0 small">{{ $comentario->contenido }}</p>
      </div>
    </div>
  </div>
  @empty
  <p class="text-muted small fst-italic mb-2">Sin comentarios aún.</p>
  @endforelse

  {{-- Formulario nuevo comentario --}}
  <form method="POST" action="{{ route($storeRoute) }}" class="mt-2">
    @csrf
    <input type="hidden" name="comentable_type" value="{{ $comentableType }}">
    <input type="hidden" name="comentable_id" value="{{ $comentableId }}">
    <div class="d-flex gap-2">
      <textarea name="contenido" class="form-control form-control-sm" rows="1"
                placeholder="Escribe un comentario..." maxlength="2000" required
                style="resize:none;" oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
      <button type="submit" class="btn btn-sm btn-primary flex-shrink-0">
        <i data-lucide="send" style="width:14px;height:14px;"></i>
      </button>
    </div>
  </form>
</div>
