<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $marca->nombre }} — Brand Kit</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Lucide -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <style>
    :root {
      --brand-accent: #1a1a2e;
    }
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      color: #1a1a2e;
    }
    /* ── Header ── */
    .brand-header {
      background: var(--brand-accent);
      color: #fff;
      padding: 3rem 0 2rem;
    }
    .brand-logo-thumb {
      width: 90px; height: 90px;
      border-radius: 12px;
      object-fit: contain;
      background: rgba(255,255,255,.1);
      padding: 8px;
    }
    .brand-logo-placeholder {
      width: 90px; height: 90px;
      border-radius: 12px;
      background: rgba(255,255,255,.15);
      display: flex; align-items: center; justify-content: center;
    }

    /* ── Sections ── */
    .section-title {
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: #888;
      margin-bottom: 1rem;
    }

    /* ── Resource cards ── */
    .resource-card {
      background: #fff;
      border: 1px solid #e9ecef;
      border-radius: 12px;
      overflow: hidden;
      transition: box-shadow .2s, transform .15s;
    }
    .resource-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
    .resource-thumb {
      height: 130px;
      background: #f3f4f6;
      display: flex; align-items: center; justify-content: center;
      overflow: hidden;
    }
    .resource-thumb img {
      max-height: 115px; max-width: 100%;
      object-fit: contain; padding: .5rem;
    }

    /* ── Color swatch ── */
    .color-swatch {
      height: 80px;
      border-radius: 8px 8px 0 0;
    }

    /* ── Download btn ── */
    .btn-dl {
      background: #1a1a2e;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: .8rem;
      padding: .35rem .8rem;
      display: inline-flex; align-items: center; gap: .35rem;
      text-decoration: none;
      transition: opacity .2s;
    }
    .btn-dl:hover { opacity: .85; color: #fff; }

    /* ── Footer ── */
    .brand-footer {
      border-top: 1px solid #e9ecef;
      color: #999;
      font-size: .78rem;
    }

    @media (max-width: 576px) {
      .brand-header { padding: 2rem 0 1.5rem; }
    }
  </style>
</head>
<body>

  {{-- ══ Header ══════════════════════════════════════════════ --}}
  <div class="brand-header">
    <div class="container">
      <div class="d-flex align-items-center gap-3 gap-md-4">
        {{-- Logo thumb --}}
        @php $firstLogo = $logos->first(); @endphp
        @if($firstLogo && $firstLogo->url)
          <img src="{{ $firstLogo->url }}" alt="{{ $marca->nombre }}" class="brand-logo-thumb">
        @else
          <div class="brand-logo-placeholder">
            <i data-lucide="layers" style="width:36px;height:36px; color:rgba(255,255,255,.7);"></i>
          </div>
        @endif

        <div>
          <h1 class="fw-bold mb-0 fs-2">{{ $marca->nombre }}</h1>
          @if($marca->tagline)
            <p class="text-white-50 mb-0 mt-1">{{ $marca->tagline }}</p>
          @endif
          <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
            @if($marca->industria)
              <span class="badge border border-white border-opacity-25 text-white-75" style="font-size:.75rem;">
                {{ $marca->industria }}
              </span>
            @endif
            @if($marca->sitio_web)
              <a href="{{ $marca->sitio_web }}" target="_blank"
                 class="text-white-50 text-decoration-none small d-flex align-items-center gap-1">
                <i data-lucide="globe" style="width:13px;height:13px;"></i>
                {{ parse_url($marca->sitio_web, PHP_URL_HOST) }}
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ Main Content ═════════════════════════════════════════ --}}
  <div class="container py-5">

    @php
      $totalRecursos = $logos->count() + $tipografias->count() + $colores->count() + $templates->count() + $otros->count();
    @endphp

    @if($totalRecursos === 0)
      <div class="text-center py-5 text-muted">
        <i data-lucide="inbox" style="width:48px;height:48px;" class="opacity-40 mb-3 d-block mx-auto"></i>
        <p class="fs-5">Esta marca aún no tiene recursos publicados.</p>
      </div>
    @endif

    {{-- ── Logos ── --}}
    @if($logos->isNotEmpty())
    <section class="mb-5">
      <div class="section-title">
        <i data-lucide="image" style="width:13px;height:13px;" class="me-1"></i>Logos
      </div>
      <div class="row g-3">
        @foreach($logos as $recurso)
        <div class="col-6 col-sm-4 col-md-3">
          <div class="resource-card h-100">
            <div class="resource-thumb">
              @if($recurso->es_imagen && $recurso->url)
                <img src="{{ $recurso->url }}" alt="{{ $recurso->nombre }}">
              @else
                <i data-lucide="image" style="width:36px;height:36px;" class="text-muted opacity-40"></i>
              @endif
            </div>
            <div class="p-2">
              <div class="fw-semibold small mb-1 text-truncate">{{ $recurso->nombre }}</div>
              @if($recurso->variante)
                <span class="badge bg-light text-dark border" style="font-size:.65rem;">{{ $recurso->variante }}</span>
              @endif
              <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="text-muted" style="font-size:.68rem;">{{ $recurso->tamanio_formateado }}</span>
                @if($recurso->archivo_path)
                  <a href="{{ route('marca.publica.download', [$marca->token_publico, $recurso]) }}"
                     class="btn-dl">
                    <i data-lucide="download" style="width:12px;height:12px;"></i> Descargar
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- ── Tipografías ── --}}
    @if($tipografias->isNotEmpty())
    <section class="mb-5">
      <div class="section-title">
        <i data-lucide="type" style="width:13px;height:13px;" class="me-1"></i>Tipografías
      </div>
      <div class="row g-3">
        @foreach($tipografias as $recurso)
        <div class="col-6 col-sm-4 col-md-3">
          <div class="resource-card h-100">
            <div class="resource-thumb">
              @if($recurso->es_imagen && $recurso->url)
                <img src="{{ $recurso->url }}" alt="{{ $recurso->nombre }}">
              @else
                <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100">
                  <span style="font-size:2rem; font-weight:800; color:#ddd;">Aa</span>
                  <span class="text-muted" style="font-size:.65rem;">{{ strtoupper(pathinfo($recurso->archivo_nombre_original ?? '', PATHINFO_EXTENSION)) }}</span>
                </div>
              @endif
            </div>
            <div class="p-2">
              <div class="fw-semibold small mb-1 text-truncate">{{ $recurso->nombre }}</div>
              @if($recurso->variante)
                <span class="badge bg-light text-dark border" style="font-size:.65rem;">{{ $recurso->variante }}</span>
              @endif
              @if($recurso->descripcion)
                <p class="text-muted mb-1" style="font-size:.7rem;">{{ $recurso->descripcion }}</p>
              @endif
              <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="text-muted" style="font-size:.68rem;">{{ $recurso->tamanio_formateado }}</span>
                @if($recurso->archivo_path)
                  <a href="{{ route('marca.publica.download', [$marca->token_publico, $recurso]) }}"
                     class="btn-dl">
                    <i data-lucide="download" style="width:12px;height:12px;"></i> Descargar
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- ── Colores ── --}}
    @if($colores->isNotEmpty())
    <section class="mb-5">
      <div class="section-title">
        <i data-lucide="droplets" style="width:13px;height:13px;" class="me-1"></i>Paleta de Colores
      </div>
      <div class="row g-3">
        @foreach($colores as $recurso)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
          <div class="resource-card h-100">
            <div class="color-swatch" style="background-color:{{ $recurso->color_hex ?? '#eee' }};"></div>
            <div class="p-2">
              <div class="fw-semibold small mb-1">{{ $recurso->nombre }}</div>
              @if($recurso->color_hex)
                <span class="badge bg-light text-dark border font-monospace" style="font-size:.65rem;">
                  {{ $recurso->color_hex }}
                </span>
              @endif
              @if($recurso->descripcion)
                <p class="text-muted mt-1 mb-0" style="font-size:.68rem;">{{ $recurso->descripcion }}</p>
              @endif
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- ── Templates ── --}}
    @if($templates->isNotEmpty())
    <section class="mb-5">
      <div class="section-title">
        <i data-lucide="file-text" style="width:13px;height:13px;" class="me-1"></i>Templates Corporativos
      </div>
      <div class="row g-3">
        @foreach($templates as $recurso)
        <div class="col-6 col-sm-4 col-md-3">
          <div class="resource-card h-100">
            <div class="resource-thumb">
              @if($recurso->es_imagen && $recurso->url)
                <img src="{{ $recurso->url }}" alt="{{ $recurso->nombre }}">
              @else
                <i data-lucide="file-text" style="width:36px;height:36px;" class="text-muted opacity-40"></i>
              @endif
            </div>
            <div class="p-2">
              <div class="fw-semibold small mb-1 text-truncate">{{ $recurso->nombre }}</div>
              @if($recurso->variante)
                <span class="badge bg-light text-dark border" style="font-size:.65rem;">{{ $recurso->variante }}</span>
              @endif
              @if($recurso->descripcion)
                <p class="text-muted mb-1" style="font-size:.7rem;">{{ Str::limit($recurso->descripcion, 60) }}</p>
              @endif
              <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="text-muted" style="font-size:.68rem;">{{ $recurso->tamanio_formateado }}</span>
                @if($recurso->archivo_path)
                  <a href="{{ route('marca.publica.download', [$marca->token_publico, $recurso]) }}"
                     class="btn-dl">
                    <i data-lucide="download" style="width:12px;height:12px;"></i> Descargar
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- ── Otros ── --}}
    @if($otros->isNotEmpty())
    <section class="mb-5">
      <div class="section-title">
        <i data-lucide="paperclip" style="width:13px;height:13px;" class="me-1"></i>Otros Recursos
      </div>
      <div class="row g-3">
        @foreach($otros as $recurso)
        <div class="col-6 col-sm-4 col-md-3">
          <div class="resource-card h-100">
            <div class="resource-thumb">
              @if($recurso->es_imagen && $recurso->url)
                <img src="{{ $recurso->url }}" alt="{{ $recurso->nombre }}">
              @else
                <i data-lucide="paperclip" style="width:36px;height:36px;" class="text-muted opacity-40"></i>
              @endif
            </div>
            <div class="p-2">
              <div class="fw-semibold small mb-1 text-truncate">{{ $recurso->nombre }}</div>
              @if($recurso->descripcion)
                <p class="text-muted mb-1" style="font-size:.7rem;">{{ Str::limit($recurso->descripcion, 60) }}</p>
              @endif
              <div class="d-flex align-items-center justify-content-between mt-2">
                <span class="text-muted" style="font-size:.68rem;">{{ $recurso->tamanio_formateado }}</span>
                @if($recurso->archivo_path)
                  <a href="{{ route('marca.publica.download', [$marca->token_publico, $recurso]) }}"
                     class="btn-dl">
                    <i data-lucide="download" style="width:12px;height:12px;"></i> Descargar
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

  </div>{{-- /container --}}

  {{-- ══ Footer ══════════════════════════════════════════════ --}}
  <footer class="brand-footer py-4 mt-2">
    <div class="container text-center">
      <p class="mb-0">
        Kit de marca de <strong>{{ $marca->nombre }}</strong>
        @if($marca->cliente)
          · {{ $marca->cliente->nombre_empresa }}
        @endif
      </p>
      <p class="mb-0 mt-1 opacity-50" style="font-size:.7rem;">
        Generado con CHANGARROS · changarros.com
      </p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>lucide.createIcons();</script>
</body>
</html>
