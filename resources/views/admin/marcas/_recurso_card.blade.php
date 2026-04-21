{{-- Variable esperada: $recurso (MarcaRecurso) --}}
<div class="col-sm-6 col-md-4">
  <div class="card border h-100">
    {{-- Preview --}}
    @if($recurso->es_imagen && $recurso->url)
      <div class="d-flex align-items-center justify-content-center bg-light"
           style="height:110px; overflow:hidden; border-radius:.375rem .375rem 0 0;">
        <img src="{{ $recurso->url }}" alt="{{ $recurso->nombre }}"
             style="max-height:100px; max-width:100%; object-fit:contain; padding:.5rem;">
      </div>
    @else
      <div class="d-flex align-items-center justify-content-center bg-light text-muted"
           style="height:110px; border-radius:.375rem .375rem 0 0;">
        <i data-lucide="{{ $recurso->tipo_icono }}" style="width:36px;height:36px;" class="opacity-50"></i>
      </div>
    @endif

    <div class="card-body p-2 d-flex flex-column">
      <div class="fw-semibold small mb-1 text-truncate" title="{{ $recurso->nombre }}">
        {{ $recurso->nombre }}
      </div>
      @if($recurso->variante)
        <span class="badge bg-light text-dark border small mb-1" style="font-size:.68rem;">{{ $recurso->variante }}</span>
      @endif
      @if($recurso->descripcion)
        <p class="text-muted mb-1" style="font-size:.72rem;">{{ Str::limit($recurso->descripcion, 50) }}</p>
      @endif
      <div class="text-muted mt-auto" style="font-size:.7rem;">{{ $recurso->tamanio_formateado }}</div>
    </div>

    <div class="card-footer p-1 d-flex gap-1">
      @if($recurso->archivo_path)
        <a href="{{ route('admin.marcas.recursos.download', [$marca, $recurso]) }}"
           class="btn btn-outline-secondary btn-sm flex-fill" title="Descargar">
          <i data-lucide="download" style="width:12px;height:12px;"></i>
        </a>
      @endif
      <form method="POST" action="{{ route('admin.marcas.recursos.destroy', [$marca, $recurso]) }}"
            class="flex-fill" onsubmit="return confirm('¿Eliminar «{{ addslashes($recurso->nombre) }}»?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger btn-sm w-100" title="Eliminar">
          <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
        </button>
      </form>
    </div>
  </div>
</div>
