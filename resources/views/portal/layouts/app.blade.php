<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="Changarrito OS - Portal de Cliente">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="_token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Portal') - Changarrito OS</title>

  @vite(['resources/js/pages/color-modes.js'])
  <script>
    (function() {
      const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      document.documentElement.setAttribute('data-bs-theme', theme);
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

  <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}">
  <link href="{{ asset('splash-screen.css') }}" rel="stylesheet" />
  <link href="{{ asset('build/plugins/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" />

  @stack('plugin-styles')

  @vite(['resources/sass/app.scss', 'resources/css/custom.css'])

  @stack('style')
</head>
<body data-base-url="{{ url('/') }}">

  <script>
    var splash = document.createElement("div");
    splash.innerHTML = `<div class="splash-screen"><div class="logo"></div><div class="spinner"></div></div>`;
    document.body.insertBefore(splash, document.body.firstChild);
    document.addEventListener("DOMContentLoaded", function () { document.body.classList.add("loaded"); });
  </script>

  <div class="main-wrapper" id="app">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="sidebar-header">
        <a href="{{ route('portal.dashboard') }}" class="sidebar-brand">
          Changarrito<span>OS</span>
        </a>
        <div class="sidebar-toggler not-active">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="sidebar-body">
        @if(auth()->user()->cliente)
        <div class="px-3 py-3 border-bottom mb-2">
          <p class="text-muted x-small mb-0">Portal de Cliente</p>
          <p class="fw-bold small mb-0">{{ auth()->user()->cliente->nombre_empresa }}</p>
        </div>
        @endif
        <ul class="nav" id="sidebarNav">
          <li class="nav-item nav-category">Mi Portal</li>
          <li class="nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
            <a href="{{ route('portal.dashboard') }}" class="nav-link">
              <i class="link-icon" data-lucide="home"></i>
              <span class="link-title">Inicio</span>
            </a>
          </li>

          @if(auth()->user()->cliente)
          <li class="nav-item nav-category">Mis Proyectos</li>
          @foreach(auth()->user()->cliente->proyectos as $p)
          <li class="nav-item {{ request()->routeIs('portal.proyectos.show') && request()->route('proyecto')->id === $p->id ? 'active' : '' }}">
            <a href="{{ route('portal.proyectos.show', $p) }}" class="nav-link">
              <i class="link-icon" data-lucide="briefcase"></i>
              <span class="link-title">{{ Str::limit($p->nombre, 22) }}</span>
            </a>
          </li>
          @endforeach
          @endif

          <li class="nav-item nav-category">Social Media</li>
          <li class="nav-item {{ request()->routeIs('portal.publicaciones.*') ? 'active' : '' }}">
            <a href="{{ route('portal.publicaciones.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="calendar-days"></i>
              <span class="link-title">Publicaciones</span>
            </a>
          </li>

          <li class="nav-item nav-category">Finanzas</li>
          <li class="nav-item {{ request()->routeIs('portal.pagos.*') ? 'active' : '' }}">
            <a href="{{ route('portal.pagos.index') }}" class="nav-link">
              <i class="link-icon" data-lucide="credit-card"></i>
              <span class="link-title">Mis Pagos</span>
            </a>
          </li>

          <li class="nav-item nav-category">Ayuda</li>
          <li class="nav-item {{ request()->routeIs('portal.manual') ? 'active' : '' }}">
            <a href="{{ route('portal.manual') }}" class="nav-link">
              <i class="link-icon" data-lucide="book-open"></i>
              <span class="link-title">Manual de Usuario</span>
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
            <li class="nav-item dropdown me-1">
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
                  <a href="{{ route('portal.notificaciones.index') }}" class="small text-muted">Ver todas</a>
                </div>
              </div>
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="w-30px h-30px ms-1 rounded-circle" src="https://placehold.co/30x30" alt="profile">
              </a>
              <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                  <div class="mb-2">
                    <img class="w-60px h-60px rounded-circle" src="https://placehold.co/60x60" alt="">
                  </div>
                  <div class="text-center">
                    <p class="fs-16px fw-bolder">{{ auth()->user()->name }}</p>
                    <p class="fs-12px text-secondary">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->cliente)
                      <span class="badge bg-secondary small">{{ auth()->user()->cliente->nombre_empresa }}</span>
                    @endif
                  </div>
                </div>
                <ul class="list-unstyled p-1">
                  <li>
                    <form method="POST" action="{{ route('portal.logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item py-2 text-body ms-0 w-100 text-start">
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
        <p class="text-muted mb-2 mb-md-0">Copyright &copy; {{ date('Y') }} <a href="#" target="_blank">Changarrito Estudio Creativo</a>.</p>
        <p class="text-muted">Portal de Clientes</p>
      </footer>
      <!-- End Footer -->
    </div>
  </div>

  @vite(['resources/js/app.js'])
  <script src="{{ asset('build/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('build/plugins/lucide/lucide.min.js') }}"></script>
  <script src="{{ asset('build/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

  @stack('plugin-scripts')

  @vite(['resources/js/pages/template.js'])

  @stack('scripts')
  @stack('modals')

<script>
(function () {
  const CSRF     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
  const badge    = document.getElementById('bellBadge');
  const list     = document.getElementById('notifList');
  const emptyMsg = document.getElementById('notifEmpty');

  function renderNotif(items) {
    list.querySelectorAll('.notif-item').forEach(el => el.remove());
    if (items.length === 0) { emptyMsg.style.display = ''; return; }
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
    fetch('{{ route("portal.notificaciones.recientes") }}', {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
      const total = data.total ?? 0;
      badge.textContent = total > 99 ? '99+' : total;
      badge.style.display = total > 0 ? '' : 'none';
      renderNotif(data.items ?? []);
    }).catch(() => {});
  }

  document.getElementById('bellDropdown').addEventListener('click', fetchNotifs);

  document.getElementById('btnLeerTodas').addEventListener('click', function (e) {
    e.preventDefault();
    fetch('{{ route("portal.notificaciones.leer-todas") }}', {
      method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(() => {
      badge.style.display = 'none';
      list.querySelectorAll('.notif-item').forEach(el => el.remove());
      emptyMsg.style.display = '';
    });
  });

  fetchNotifs();
  setInterval(fetchNotifs, 60000);
})();
</script>
</body>
</html>
