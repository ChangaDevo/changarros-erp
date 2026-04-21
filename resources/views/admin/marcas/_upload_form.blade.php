{{--
  Variables esperadas:
    $tipo  => 'logo' | 'tipografia' | 'template' | 'otro'
    $label => texto para el botón/header
--}}
<div class="card border mb-3">
  <div class="card-header py-2 d-flex align-items-center justify-content-between"
       style="cursor:pointer;" data-bs-toggle="collapse" data-bs-target="#form-{{ $tipo }}">
    <span class="small fw-semibold">
      <i data-lucide="plus-circle" style="width:14px;height:14px;" class="me-1"></i>Subir {{ $label }}
    </span>
    <i data-lucide="chevron-down" style="width:14px;height:14px;" class="text-muted"></i>
  </div>
  <div class="collapse" id="form-{{ $tipo }}">
    <div class="card-body">
      <form method="POST"
            action="{{ route('admin.marcas.recursos.store', $marca) }}"
            enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="tipo" value="{{ $tipo }}">

        <div class="row g-2 mb-2">
          <div class="col-md-5">
            <label class="form-label small mb-1">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-sm" name="nombre" required
                   placeholder="ej. Logo principal horizontal">
          </div>
          <div class="col-md-3">
            <label class="form-label small mb-1">Variante</label>
            <input type="text" class="form-control form-control-sm" name="variante"
                   placeholder="ej. Dark, Blanco…">
          </div>
          <div class="col-md-4">
            <label class="form-label small mb-1">Descripción</label>
            <input type="text" class="form-control form-control-sm" name="descripcion"
                   placeholder="Opcional">
          </div>
        </div>

        <div class="mb-2">
          <label class="form-label small mb-1">Archivo <span class="text-danger">*</span></label>
          <input type="file" class="form-control form-control-sm" name="archivo"
                 accept=".png,.jpg,.jpeg,.svg,.gif,.webp,.pdf,.ai,.eps,.psd,.ttf,.otf,.woff,.woff2,.zip,.docx,.xlsx"
                 required>
          <div class="form-text">PNG, JPG, SVG, PDF, AI, EPS, PSD, TTF, OTF, WOFF, ZIP — máx. 50 MB</div>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">
          <i data-lucide="upload" style="width:13px;height:13px;" class="me-1"></i>Subir {{ $label }}
        </button>
      </form>
    </div>
  </div>
</div>
