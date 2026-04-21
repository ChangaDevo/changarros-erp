<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Portal de Cliente - CHANGARROS</title>

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
  @vite(['resources/sass/app.scss', 'resources/css/custom.css'])
</head>
<body data-base-url="{{ url('/') }}">
  <script>
    var splash = document.createElement("div");
    splash.innerHTML = `<div class="splash-screen"><div class="logo"></div><div class="spinner"></div></div>`;
    document.body.insertBefore(splash, document.body.firstChild);
    document.addEventListener("DOMContentLoaded", function () { document.body.classList.add("loaded"); });
  </script>

  <div class="main-wrapper" id="app">
    <div class="page-wrapper full-page">
      <div class="page-content container-xxl d-flex align-items-center justify-content-center">
        <div class="row w-100 mx-0 auth-page">
          <div class="col-md-8 col-xl-6 mx-auto">
            <div class="card">
              <div class="row">
                <div class="col-md-4 pe-md-0">
                  <div class="auth-side-wrapper d-flex flex-column align-items-center justify-content-center p-4"
                    style="background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);">
                    <div class="text-center text-white">
                      <i data-lucide="briefcase" style="width:48px;height:48px;opacity:0.9;" class="mb-3"></i>
                      <h4 class="fw-bold mb-2">Portal de Clientes</h4>
                      <p class="small opacity-75">CHANGARROS</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-8 ps-md-0">
                  <div class="auth-form-wrapper px-4 py-5">
                    <a href="#" class="nobleui-logo d-block mb-2">CHANGARROS</a>
                    <h5 class="text-secondary fw-normal mb-4">Accede a tu portal de cliente</h5>

                    @if($errors->any())
                      <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                          <p class="mb-0">{{ $error }}</p>
                        @endforeach
                      </div>
                    @endif

                    <form method="POST" action="{{ route('portal.login.submit') }}">
                      @csrf
                      <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                          id="email" name="email" value="{{ old('email') }}"
                          placeholder="tu@correo.com" required autofocus>
                        @error('email')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                          id="password" name="password" placeholder="Contraseña" required>
                        @error('password')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="mb-3">
                        <div class="form-check">
                          <input type="checkbox" class="form-check-input" id="remember" name="remember">
                          <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                      </div>
                      <div>
                        <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0">
                          Ingresar al Portal
                        </button>
                      </div>
                      <p class="mt-3 text-secondary small">
                        ¿Eres administrador? <a href="{{ route('admin.login') }}">Panel de admin</a>
                      </p>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @vite(['resources/js/app.js'])
  <script src="{{ asset('build/plugins/lucide/lucide.min.js') }}"></script>
  @vite(['resources/js/pages/template.js'])
</body>
</html>
