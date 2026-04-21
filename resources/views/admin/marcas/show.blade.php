@extends('admin.layouts.app')

@section('title', $marca->nombre)

@section('content')

{{-- ── Header ───────────────────────────────────────────── --}}
<div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <a href="{{ route('admin.marcas.index') }}" class="text-muted text-decoration-none small">
        <i data-lucide="arrow-left" style="width:14px;height:14px;" class="me-1"></i>Marcas
      </a>
    </div>
    <h4 class="fw-bold mb-1">{{ $marca->nombre }}</h4>
    <span class="text-muted">{{ $marca->cliente->nombre_empresa ?? '—' }}</span>
    @if($marca->tagline)
      <span class="text-muted ms-2">· <em>{{ $marca->tagline }}</em></span>
    @endif
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a href="{{ route('admin.marcas.edit', $marca) }}" class="btn btn-outline-secondary btn-sm">
      <i data-lucide="edit" style="width:14px;height:14px;" class="me-1"></i>Editar
    </a>
    <a href="{{ route('admin.marcas.exportar-zip', $marca) }}" class="btn btn-outline-dark btn-sm">
      <i data-lucide="archive" style="width:14px;height:14px;" class="me-1"></i>Exportar ZIP
    </a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i data-lucide="check-circle" style="width:16px;height:16px;" class="me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i data-lucide="alert-circle" style="width:16px;height:16px;" class="me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row g-4">

  {{-- ── Columna principal: tabs de recursos ─────────────── --}}
  <div class="col-lg-8">

    {{-- Tab Nav --}}
    <ul class="nav nav-tabs mb-0" id="marcaTabs" role="tablist" style="border-bottom:none;">
      @php
        $tabs = [
          'logos'       => ['icon'=>'image',       'label'=>'Logos',        'count'=>$logos->count()],
          'tipografias' => ['icon'=>'type',         'label'=>'Tipografías',  'count'=>$tipografias->count()],
          'colores'     => ['icon'=>'droplets',     'label'=>'Colores',      'count'=>$colores->count()],
          'templates'   => ['icon'=>'file-text',    'label'=>'Templates',    'count'=>$templates->count()],
          'otros'       => ['icon'=>'paperclip',    'label'=>'Otros',        'count'=>$otros->count()],
        ];
        $activeTab = request('tab', 'logos');
      @endphp
      @foreach($tabs as $key => $tab)
      <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeTab === $key ? 'active' : '' }} d-flex align-items-center gap-1"
                id="tab-{{ $key }}"
                data-bs-toggle="tab" data-bs-target="#pane-{{ $key }}"
                type="button" role="tab">
          <i data-lucide="{{ $tab['icon'] }}" style="width:14px;height:14px;"></i>
          {{ $tab['label'] }}
          @if($tab['count'] > 0)
            <span class="badge bg-secondary ms-1">{{ $tab['count'] }}</span>
          @endif
        </button>
      </li>
      @endforeach
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white" id="marcaTabsContent">

      {{-- LOGOS --}}
      <div class="tab-pane fade {{ $activeTab === 'logos' ? 'show active' : '' }}" id="pane-logos" role="tabpanel">
        @include('admin.marcas._upload_form', ['tipo' => 'logo', 'label' => 'Logo'])
        <div class="row g-3 mt-2">
          @forelse($logos as $recurso)
            @include('admin.marcas._recurso_card', ['recurso' => $recurso])
          @empty
            @include('admin.marcas._empty_state', ['label' => 'logos'])
          @endforelse
        </div>
      </div>

      {{-- TIPOGRAFÍAS --}}
      <div class="tab-pane fade {{ $activeTab === 'tipografias' ? 'show active' : '' }}" id="pane-tipografias" role="tabpanel">
        @include('admin.marcas._upload_form', ['tipo' => 'tipografia', 'label' => 'Tipografía'])
        <div class="row g-3 mt-2">
          @forelse($tipografias as $recurso)
            @include('admin.marcas._recurso_card', ['recurso' => $recurso])
          @empty
            @include('admin.marcas._empty_state', ['label' => 'tipografías'])
          @endforelse
        </div>
      </div>

      {{-- COLORES --}}
      <div class="tab-pane fade {{ $activeTab === 'colores' ? 'show active' : '' }}" id="pane-colores" role="tabpanel">
        {{-- Upload form especial para colores --}}
        <div class="card border mb-3">
          <div class="card-header py-2 d-flex align-items-center justify-content-between"
               style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#form-color">
            <span class="small fw-semibold">
              <i data-lucide="plus-circle" style="width:14px;height:14px;" class="me-1"></i>Agregar Color
            </span>
            <i data-lucide="chevron-down" style="width:14px;height:14px;" class="text-muted"></i>
          </div>
          <div class="collapse" id="form-color">
            <div class="card-body">
              <form method="POST" action="{{ route('admin.marcas.recursos.store', $marca) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo" value="color">
                <div class="row g-2">
                  <div class="col-md-4">
                    <label class="form-label small mb-1">Nombre del color <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" name="nombre" required placeholder="ej. Azul Primario">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small mb-1">Hex</label>
                    <div class="d-flex gap-1">
                      <input type="color" class="form-control form-control-color form-control-sm" name="color_hex" id="colorPicker" value="#000000" style="width:44px;">
                      <input type="text" class="form-control form-control-sm font-monospace" id="colorHexText" placeholder="#000000" maxlength="7">
                    </div>
                  </div>
                  <div class="col-md-5">
                    <label class="form-label small mb-1">Descripción (opcional)</label>
                    <input type="text" class="form-control form-control-sm" name="descripcion" placeholder="ej. Color principal de botones">
                  </div>
                </div>
                <div class="mt-2">
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i data-lucide="save" style="width:13px;height:13px;" class="me-1"></i>Guardar Color
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Paleta --}}
        @if($colores->isEmpty())
          @include('admin.marcas._empty_state', ['label' => 'colores'])
        @else
          <div class="row g-3">
            @foreach($colores as $recurso)
            <div class="col-sm-6 col-md-4">
              <div class="card h-100 border">
                <div class="rounded-top" style="height:80px; background-color:{{ $recurso->color_hex ?? '#eee' }};"></div>
                <div class="card-body p-2">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="small">{{ $recurso->nombre }}</strong>
                    <form method="POST" action="{{ route('admin.marcas.recursos.destroy', [$marca, $recurso]) }}" class="d-inline"
                          onsubmit="return confirm('¿Eliminar color?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link btn-sm p-0 text-danger">
                        <i data-lucide="x" style="width:14px;height:14px;"></i>
                      </button>
                    </form>
                  </div>
                  @if($recurso->color_hex)
                    <span class="badge bg-light text-dark border font-monospace">{{ $recurso->color_hex }}</span>
                  @endif
                  @if($recurso->descripcion)
                    <p class="text-muted small mb-0 mt-1">{{ $recurso->descripcion }}</p>
                  @endif
                </div>
              </div>
            </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- TEMPLATES --}}
      <div class="tab-pane fade {{ $activeTab === 'templates' ? 'show active' : '' }}" id="pane-templates" role="tabpanel">
        @include('admin.marcas._upload_form', ['tipo' => 'template', 'label' => 'Template'])
        <div class="row g-3 mt-2">
          @forelse($templates as $recurso)
            @include('admin.marcas._recurso_card', ['recurso' => $recurso])
          @empty
            @include('admin.marcas._empty_state', ['label' => 'templates'])
          @endforelse
        </div>
      </div>

      {{-- OTROS --}}
      <div class="tab-pane fade {{ $activeTab === 'otros' ? 'show active' : '' }}" id="pane-otros" role="tabpanel">
        @include('admin.marcas._upload_form', ['tipo' => 'otro', 'label' => 'Archivo'])
        <div class="row g-3 mt-2">
          @forelse($otros as $recurso)
            @include('admin.marcas._recurso_card', ['recurso' => $recurso])
          @empty
            @include('admin.marcas._empty_state', ['label' => 'archivos'])
          @endforelse
        </div>
      </div>

    </div>
  </div>{{-- /col-lg-8 --}}

  {{-- ── Sidebar: info + acceso + enlace ─────────────────── --}}
  <div class="col-lg-4">

    {{-- Acceso cliente --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">
          <i data-lucide="shield" style="width:15px;height:15px;" class="me-1"></i>Acceso del Cliente
        </h6>
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            @if($marca->acceso_cliente)
              <span class="badge bg-success fs-6 px-3">
                <i data-lucide="eye" style="width:13px;height:13px;" class="me-1"></i>Público
              </span>
            @else
              <span class="badge bg-secondary fs-6 px-3">
                <i data-lucide="eye-off" style="width:13px;height:13px;" class="me-1"></i>Privado
              </span>
            @endif
          </div>
          <form method="POST" action="{{ route('admin.marcas.toggle-acceso', $marca) }}">
            @csrf
            <button type="submit" class="btn btn-sm {{ $marca->acceso_cliente ? 'btn-outline-secondary' : 'btn-success' }}">
              {{ $marca->acceso_cliente ? 'Desactivar acceso' : 'Activar acceso' }}
            </button>
          </form>
        </div>
        <p class="text-muted small mb-0">
          @if($marca->acceso_cliente)
            El cliente puede ver y descargar los recursos de esta marca usando el enlace público.
          @else
            El cliente no tiene acceso a la vista pública. Activa el acceso cuando la marca esté lista.
          @endif
        </p>
      </div>
    </div>

    {{-- Enlace público --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="fw-semibold mb-2">
          <i data-lucide="link" style="width:15px;height:15px;" class="me-1"></i>Enlace Público
        </h6>
        <div class="input-group mb-2">
          <input type="text" class="form-control form-control-sm font-monospace"
                 id="linkPublico" value="{{ $marca->link_publico }}" readonly>
          <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copiarLink()" title="Copiar">
            <i data-lucide="copy" style="width:13px;height:13px;"></i>
          </button>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ $marca->link_publico }}" target="_blank" class="btn btn-outline-primary btn-sm flex-fill {{ !$marca->acceso_cliente ? 'disabled' : '' }}">
            <i data-lucide="external-link" style="width:13px;height:13px;" class="me-1"></i>Abrir
          </a>
          <a href="{{ route('admin.marcas.exportar-zip', $marca) }}" class="btn btn-outline-dark btn-sm flex-fill">
            <i data-lucide="archive" style="width:13px;height:13px;" class="me-1"></i>ZIP
          </a>
        </div>
        @if(!$marca->acceso_cliente)
          <p class="text-warning small mt-2 mb-0">
            <i data-lucide="alert-triangle" style="width:12px;height:12px;" class="me-1"></i>
            Activa el acceso para que el enlace funcione.
          </p>
        @endif
      </div>
    </div>

    {{-- Info de la marca --}}
    <div class="card">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">
          <i data-lucide="info" style="width:15px;height:15px;" class="me-1"></i>Detalles
        </h6>
        <dl class="row small mb-0">
          @if($marca->industria)
          <dt class="col-5 text-muted">Industria</dt>
          <dd class="col-7">{{ $marca->industria }}</dd>
          @endif
          @if($marca->sitio_web)
          <dt class="col-5 text-muted">Web</dt>
          <dd class="col-7">
            <a href="{{ $marca->sitio_web }}" target="_blank" class="text-truncate d-block" style="max-width:140px;">
              {{ $marca->sitio_web }}
            </a>
          </dd>
          @endif
          @if($marca->descripcion)
          <dt class="col-5 text-muted">Descripción</dt>
          <dd class="col-7">{{ $marca->descripcion }}</dd>
          @endif
          <dt class="col-5 text-muted">Creada</dt>
          <dd class="col-7">{{ $marca->created_at->format('d/m/Y') }}</dd>
          @if($marca->creadoPor)
          <dt class="col-5 text-muted">Por</dt>
          <dd class="col-7">{{ $marca->creadoPor->name }}</dd>
          @endif
        </dl>
      </div>
    </div>

  </div>{{-- /col-lg-4 --}}

</div>{{-- /row --}}
@endsection

@push('scripts')
<script>
// Sync color picker ↔ hex text input
const picker  = document.getElementById('colorPicker');
const hexText = document.getElementById('colorHexText');
if (picker && hexText) {
  // Init text from picker
  hexText.value = picker.value;

  picker.addEventListener('input',  () => { hexText.value = picker.value; });
  hexText.addEventListener('input', () => {
    const v = hexText.value.trim();
    if (/^#[0-9A-Fa-f]{6}$/.test(v)) picker.value = v;
  });

  // On color form submit, copy hexText → picker (which has name="color_hex")
  const colorForm = picker.closest('form');
  if (colorForm) {
    colorForm.addEventListener('submit', () => {
      const v = hexText.value.trim();
      if (/^#[0-9A-Fa-f]{6}$/.test(v)) picker.value = v;
    });
  }
}

function copiarLink() {
  const input = document.getElementById('linkPublico');
  input.select();
  document.execCommand('copy');
  // feedback visual
  const btn = input.nextElementSibling;
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<i data-lucide="check" style="width:13px;height:13px;"></i>';
  btn.classList.replace('btn-outline-secondary','btn-success');
  if (window.lucide) lucide.createIcons();
  setTimeout(() => {
    btn.innerHTML = originalHtml;
    btn.classList.replace('btn-success','btn-outline-secondary');
    if (window.lucide) lucide.createIcons();
  }, 2000);
}
</script>
@endpush
