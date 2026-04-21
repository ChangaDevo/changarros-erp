<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="ESPIRAL ERP - Panel de Administración">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="_token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Dashboard') - ESPIRAL ERP</title>

  <!-- color-modes:js -->
  @vite(['resources/js/pages/color-modes.js'])
  <script>
    (function() {
      const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', theme);
    })();
  </script>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

  <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">

  <!-- Splash Screen -->
  <link href="{{ asset('splash-screen.css') }}" rel="stylesheet" />

  <!-- plugin css -->
  <link href="{{ asset('build/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />

  @stack('plugin-styles')

  @vite(['resources/sass/app.scss', 'resources/css/custom.css'])

  @stack('style')
</head>
<body data-base-url="{{ url('/') }}">

  <script>
    var splash = document.createElement("div");
    splash.innerHTML = `
      <div class="splash-screen">
        <div class="logo"></div>
        <div class="spinner"></div>
      </div>`;
    document.body.insertBefore(splash, document.body.firstChild);
    document.addEventListener("DOMContentLoaded", function () {
      document.body.classList.add("loaded");
    });
  </script>

  <div class="main-wrapper" id="app">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
          Espiral<span>ERP</span>
        </a>
        <div class="sidebar-toggler not-active">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="sidebar-body">
        <ul class="nav" id="sidebarNav">
          <li class="nav-item nav-category">Principal</li>
          <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
              <i class="link-icon" data-lucide="home"></i>
              <span class="link-title">Dashboard</span>
            </a>
          </li>

          {{-- ══ Gestión ══ --}}
          <li class="nav-item nav-category">Gestión</li>
          <li class="nav-item {{ request()->routeIs('admin.clientes.*') ? 'active' : '' }}">
            <a href="{{ route('admin.clientes.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="users"></i>
              <span class="link-title">Clientes</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.proyectos.*') ? 'active' : '' }}">
            <a href="{{ route('admin.proyectos.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="briefcase"></i>
              <span class="link-title">Proyectos</span>
            </a>
          </li>

          {{-- ══ Finanzas ══ --}}
          <li class="nav-item nav-category">Finanzas</li>
          <li class="nav-item {{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}">
            <a href="{{ route('admin.pagos.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="credit-card"></i>
              <span class="link-title">Pagos</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.cotizaciones.*') ? 'active' : '' }}">
            <a href="{{ route('admin.cotizaciones.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="receipt"></i>
              <span class="link-title">Cotizaciones</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.plantillas-cotizacion.*') ? 'active' : '' }}">
            <a href="{{ route('admin.plantillas-cotizacion.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="layout-template"></i>
              <span class="link-title">Plantillas</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.facturas.*') ? 'active' : '' }}">
            <a href="{{ route('admin.facturas.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="file-text"></i>
              <span class="link-title">Facturas & Recibos</span>
            </a>
          </li>

          {{-- ══ Marketing ══ --}}
          <li class="nav-item nav-category">Marketing</li>
          <li class="nav-item {{ request()->routeIs('admin.mailing.*') ? 'active' : '' }}">
            <a href="{{ route('admin.mailing.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="send"></i>
              <span class="link-title">Mailing</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.marcas.*') ? 'active' : '' }}">
            <a href="{{ route('admin.marcas.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="layers"></i>
              <span class="link-title">Marcas</span>
            </a>
          </li>

          {{-- ══ Productividad ══ --}}
          <li class="nav-item nav-category">Productividad</li>
          <li class="nav-item {{ request()->routeIs('admin.tiempo.*') ? 'active' : '' }}">
            <a href="{{ route('admin.tiempo.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="clock"></i>
              <span class="link-title">Tiempo</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.rentabilidad.*') ? 'active' : '' }}">
            <a href="{{ route('admin.rentabilidad.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="trending-up"></i>
              <span class="link-title">Rentabilidad</span>
            </a>
          </li>

          {{-- ══ Social Media ══ --}}
          <li class="nav-item nav-category">Social Media</li>
          <li class="nav-item {{ request()->routeIs('admin.publicaciones.*') ? 'active' : '' }}">
            <a href="{{ route('admin.publicaciones.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="calendar-days"></i>
              <span class="link-title">Publicaciones</span>
            </a>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.meta-tokens.*') ? 'active' : '' }}">
            <a href="{{ route('admin.meta-tokens.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="key-round"></i>
              <span class="link-title">Tokens de Meta</span>
            </a>
          </li>

          <li class="nav-item nav-category">Sistema</li>
          @if(auth()->user()->isSuperAdmin())
          <li class="nav-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
            <a href="{{ route('admin.usuarios.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="shield-check"></i>
              <span class="link-title">Usuarios</span>
            </a>
          </li>
          @endif
          <li class="nav-item {{ request()->routeIs('admin.actividad.*') ? 'active' : '' }}">
            <a href="{{ route('admin.actividad.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="activity"></i>
              <span class="link-title">Actividad</span>
            </a>
          </li>
        </ul>
      </div>
    </nav>
    <!-- End Sidebar -->

    <div class="page-wrapper">
      <!-- Header -->
      <nav class="navbar">
        <div class="navbar-content">
          <div class="logo-mini-wrapper">
            <img src="{{ url('build/images/logo-mini-light.png') }}" class="logo-mini logo-mini-light" alt="logo">
            <img src="{{ url('build/images/logo-mini-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo">
          </div>

          <ul class="navbar-nav ms-auto">
            <li class="theme-switcher-wrapper nav-item">
              <input type="checkbox" value="" id="theme-switcher">
              <label for="theme-switcher">
                <div class="box">
                  <div class="ball"></div>
                  <div class="icons">
                    <i class="link-icon" data-lucide="sun"></i>
                    <i class="link-icon" data-lucide="moon"></i>
                  </div>
                </div>
              </label>
            </li>

            {{-- Bell Notificaciones --}}
            <li class="nav-item dropdown me-1" id="bellNavItem">
              <a class="nav-link position-relative px-2" href="#" id="bellDropdown"
                 role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i data-lucide="bell" style="width:20px;height:20px;"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                      style="font-size:.55rem;display:none;" id="bellBadge">0</span>
              </a>
              <div class="dropdown-menu dropdown-menu-end p-0 shadow"
                   style="width:320px;max-height:420px;overflow-y:auto;" aria-labelledby="bellDropdown">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                  <span class="fw-semibold small">Notificaciones</span>
                  <button class="btn btn-link btn-sm p-0 text-muted small" id="btnLeerTodas">Marcar todas leídas</button>
                </div>
                <div id="notifList">
                  <div class="text-center text-muted py-4 small" id="notifEmpty">
                    <i data-lucide="bell-off" style="width:24px;height:24px;" class="mb-2"></i>
                    <p class="mb-0">Sin notificaciones nuevas</p>
                  </div>
                </div>
                <div class="text-center py-2 border-top">
                  <a href="{{ route('admin.notificaciones.index') }}" class="small text-muted">Ver todas</a>
                </div>
              </div>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle p-0 ms-2" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @if(auth()->user()->foto_url)
                  <img class="rounded-circle border" src="{{ auth()->user()->foto_url }}"
                       alt="{{ auth()->user()->name }}"
                       style="width:34px;height:34px;object-fit:cover;">
                @else
                  <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold"
                       style="width:34px;height:34px;font-size:.8rem;">
                    {{ auth()->user()->iniciales }}
                  </div>
                @endif
              </a>
              <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="profileDropdown" style="min-width:220px;">
                <div class="d-flex flex-column align-items-center border-bottom px-4 py-3">
                  <div class="mb-2">
                    @if(auth()->user()->foto_url)
                      <img class="rounded-circle border" src="{{ auth()->user()->foto_url }}"
                           alt="{{ auth()->user()->name }}"
                           style="width:60px;height:60px;object-fit:cover;">
                    @else
                      <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fw-bold"
                           style="width:60px;height:60px;font-size:1.4rem;">
                        {{ auth()->user()->iniciales }}
                      </div>
                    @endif
                  </div>
                  <div class="text-center">
                    <p class="fs-16px fw-bolder mb-0">{{ auth()->user()->name }}</p>
                    @if(auth()->user()->cargo)
                      <p class="fs-11px text-muted mb-1">{{ auth()->user()->cargo }}</p>
                    @else
                      <p class="fs-12px text-secondary mb-1">{{ auth()->user()->email }}</p>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                      <span class="badge" style="background:#6f42c1;">Super Admin</span>
                    @else
                      <span class="badge bg-primary">Administrador</span>
                    @endif
                  </div>
                </div>
                <ul class="list-unstyled p-1 mb-0">
                  <li>
                    <a href="{{ route('admin.perfil.show') }}" class="dropdown-item py-2 d-flex align-items-center">
                      <i class="me-2 icon-md" data-lucide="user"></i>
                      <span>Mi Perfil</span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('admin.perfil.edit') }}" class="dropdown-item py-2 d-flex align-items-center">
                      <i class="me-2 icon-md" data-lucide="settings"></i>
                      <span>Editar Perfil</span>
                    </a>
                  </li>
                  <li><hr class="dropdown-divider my-1"></li>
                  <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item py-2 text-danger ms-0 w-100 text-start d-flex align-items-center">
                        <i class="me-2 icon-md" data-lucide="log-out"></i>
                        <span>Cerrar Sesión</span>
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </li>
          </ul>

          <a href="#" class="sidebar-toggler">
            <i data-lucide="menu"></i>
          </a>
        </div>
      </nav>
      <!-- End Header -->

      <div class="page-content container-xxl">

        <!-- Flash Messages -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i data-lucide="check-circle" class="me-2" style="width:16px;height:16px;"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i data-lucide="alert-circle" class="me-2" style="width:16px;height:16px;"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i data-lucide="alert-circle" class="me-2" style="width:16px;height:16px;"></i>
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @yield('content')
      </div>

      <!-- Footer -->
      <footer class="footer d-flex flex-column flex-md-row align-items-center justify-content-between px-4 py-3 border-top small">
        <p class="text-muted mb-2 mb-md-0">Copyright &copy; {{ date('Y') }} <a href="#" target="_blank">ESPIRAL ERP</a>.</p>
        <p class="text-muted">ESPIRAL ERP v1.0</p>
      </footer>
      <!-- End Footer -->
    </div>
  </div>

  <!-- base js -->
  @vite(['resources/js/app.js'])
  <script src="{{ asset('build/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('build/plugins/lucide/lucide.min.js') }}"></script>
  <script src="{{ asset('build/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
  <!-- end base js -->

  @stack('plugin-scripts')

  @vite(['resources/js/pages/template.js'])

  @stack('scripts')
  @stack('modals')

<script>
(function () {
  const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const badge     = document.getElementById('bellBadge');
  const list      = document.getElementById('notifList');
  const emptyMsg  = document.getElementById('notifEmpty');

  function renderNotif(items) {
    // Elimina ítems existentes (pero no el emptyMsg)
    list.querySelectorAll('.notif-item').forEach(el => el.remove());
    if (items.length === 0) {
      emptyMsg.style.display = '';
      return;
    }
    emptyMsg.style.display = 'none';
    items.forEach(n => {
      const d = document.createElement('a');
      d.href = n.url || '#';
      d.className = 'notif-item d-flex align-items-start px-3 py-2 border-bottom text-decoration-none text-reset';
      d.innerHTML = `
        <div class="me-2 flex-shrink-0 mt-1">
          <span class="badge rounded-pill bg-${n.color}-subtle text-${n.color} p-1">
            <i data-lucide="${n.icono}" style="width:14px;height:14px;"></i>
          </span>
        </div>
        <div class="flex-grow-1">
          <p class="mb-0 small fw-semibold">${n.titulo}</p>
          ${n.mensaje ? `<p class="mb-0 text-muted" style="font-size:11px;">${n.mensaje}</p>` : ''}
          <p class="mb-0 text-muted" style="font-size:10px;">${n.tiempo}</p>
        </div>`;
      list.insertBefore(d, emptyMsg);
    });
    if (window.lucide) lucide.createIcons();
  }

  function fetchNotifs() {
    fetch('{{ route("admin.notificaciones.recientes") }}', {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
      const total = data.total ?? 0;
      badge.textContent = total > 99 ? '99+' : total;
      badge.style.display = total > 0 ? '' : 'none';
      renderNotif(data.items ?? []);
    })
    .catch(() => {});
  }

  // Cargar al abrir el dropdown
  document.getElementById('bellDropdown').addEventListener('click', fetchNotifs);

  // Marcar todas leídas
  document.getElementById('btnLeerTodas').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('{{ route("admin.notificaciones.leer-todas") }}', {
      method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => {
      badge.style.display = 'none';
      list.querySelectorAll('.notif-item').forEach(el => el.remove());
      emptyMsg.style.display = '';
    });
  });

  // Cargar cada 60s automáticamente
  fetchNotifs();
  setInterval(fetchNotifs, 60000);
})();
</script>
</body>
</html>
