@extends('admin.layouts.app')
@section('title', 'Editar Perfil')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Editar Perfil</h4>
    <p class="text-muted mb-0">Actualiza tu información personal</p>
  </div>
  <a href="{{ route('admin.perfil.show') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i> Volver al perfil
  </a>
</div>

<form action="{{ route('admin.perfil.update') }}" method="POST" enctype="multipart/form-data" id="formPerfil">
  @csrf @method('PUT')

  <div class="row g-4">

    {{-- Columna izquierda: foto --}}
    <div class="col-md-4 col-xl-3">
      <div class="card text-center">
        <div class="card-body py-4">

          {{-- Avatar preview --}}
          <div class="position-relative d-inline-block mb-3" id="avatarWrapper">

            <div id="avatarImg" class="{{ $user->foto_url ? '' : 'd-none' }}">
              <img src="{{ $user->foto_url ?? '' }}" alt="{{ $user->name }}"
                   id="previewImg"
                   class="rounded-circle border"
                   style="width:110px;height:110px;object-fit:cover;">
            </div>

            <div id="avatarLetras" class="{{ $user->foto_url ? 'd-none' : '' }}
                  rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                 style="width:110px;height:110px;font-size:2.2rem;font-weight:700;">
              {{ $user->iniciales }}
            </div>

            {{-- Botón cámara encima --}}
            <label for="fotoInput" class="position-absolute bottom-0 end-0 bg-white border rounded-circle
                   d-flex align-items-center justify-content-center cursor-pointer shadow-sm"
                   style="width:34px;height:34px;cursor:pointer;" title="Cambiar foto">
              <i data-lucide="camera" style="width:16px;height:16px;color:#6c757d;"></i>
            </label>
          </div>

          <input type="file" id="fotoInput" name="foto_perfil" accept="image/jpg,image/jpeg,image/png,image/webp" class="d-none">
          @error('foto_perfil')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror

          <p class="text-muted small mt-2 mb-3">JPG, PNG o WEBP · Máx. 2MB</p>

          @if($user->foto_url)
            <form action="{{ route('admin.perfil.foto.destroy') }}" method="POST" id="formEliminarFoto">
              @csrf @method('DELETE')
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarEliminarFoto()">
                <i data-lucide="trash-2" style="width:13px;height:13px;" class="me-1"></i> Quitar foto
              </button>
            </form>
          @endif

          <hr class="my-3">

          <p class="fw-semibold mb-0">{{ $user->name }}</p>
          <p class="text-muted small">{{ $user->role_label }}</p>

        </div>
      </div>
    </div>

    {{-- Columna derecha: datos --}}
    <div class="col-md-8 col-xl-9">
      <div class="card">
        <div class="card-body">
          <h6 class="card-title fw-semibold mb-4">Información personal</h6>

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}"
                     class="form-control @error('name') is-invalid @enderror"
                     placeholder="Tu nombre">
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}"
                     class="form-control @error('email') is-invalid @enderror"
                     placeholder="tu@correo.com">
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Teléfono</label>
              <div class="input-group">
                <span class="input-group-text"><i data-lucide="phone" style="width:14px;height:14px;"></i></span>
                <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                       class="form-control @error('telefono') is-invalid @enderror"
                       placeholder="+52 614 000 0000">
              </div>
              @error('telefono') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Cargo / Puesto</label>
              <div class="input-group">
                <span class="input-group-text"><i data-lucide="briefcase" style="width:14px;height:14px;"></i></span>
                <input type="text" name="cargo" value="{{ old('cargo', $user->cargo) }}"
                       class="form-control @error('cargo') is-invalid @enderror"
                       placeholder="Ej. Diseñador, Director...">
              </div>
              @error('cargo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold">Bio / Descripción</label>
              <textarea name="bio" rows="4"
                        class="form-control @error('bio') is-invalid @enderror"
                        placeholder="Cuéntanos un poco sobre ti..."
                        maxlength="500">{{ old('bio', $user->bio) }}</textarea>
              <div class="d-flex justify-content-between mt-1">
                @error('bio')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @else
                  <span></span>
                @enderror
                <small class="text-muted" id="bioCount">0 / 500</small>
              </div>
            </div>

          </div>

          <hr class="my-4">

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i data-lucide="save" style="width:15px;height:15px;" class="me-1"></i>
              Guardar cambios
            </button>
            <a href="{{ route('admin.perfil.show') }}" class="btn btn-outline-secondary">Cancelar</a>
          </div>

        </div>
      </div>
    </div>

  </div>
</form>
@endsection

@push('scripts')
<script>
// ── Preview de foto ────────────────────────────────────
document.getElementById('fotoInput').addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;

  if (file.size > 2 * 1024 * 1024) {
    alert('La imagen no puede superar los 2MB.');
    this.value = '';
    return;
  }

  const reader = new FileReader();
  reader.onload = function (e) {
    // Mostrar img, ocultar letras
    const imgEl    = document.getElementById('previewImg');
    const avatarImg    = document.getElementById('avatarImg');
    const avatarLetras = document.getElementById('avatarLetras');

    imgEl.src = e.target.result;
    avatarImg.classList.remove('d-none');
    avatarLetras.classList.add('d-none');
  };
  reader.readAsDataURL(file);
});

// ── Confirmar eliminar foto ────────────────────────────
function confirmarEliminarFoto() {
  if (confirm('¿Seguro que quieres eliminar tu foto de perfil?')) {
    document.getElementById('formEliminarFoto').submit();
  }
}

// ── Contador bio ──────────────────────────────────────
const bioTextarea = document.querySelector('textarea[name="bio"]');
const bioCount    = document.getElementById('bioCount');
function updateCount() {
  bioCount.textContent = (bioTextarea.value.length) + ' / 500';
}
bioTextarea.addEventListener('input', updateCount);
updateCount();
</script>
@endpush
