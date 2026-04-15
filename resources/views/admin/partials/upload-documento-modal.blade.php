<div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.documentos.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDocumentoLabel">
            <i data-lucide="file-plus" style="width:18px;height:18px;" class="me-2"></i>Subir Documento
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nombre del Documento <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre" required placeholder="Ej: Contrato de Servicios">
          </div>
          <div class="mb-3">
            <label class="form-label">Tipo <span class="text-danger">*</span></label>
            <select class="form-select" name="tipo" required>
              <option value="contrato">Contrato</option>
              <option value="cotizacion">Cotización</option>
              <option value="avance">Avance</option>
              <option value="entrega">Entrega</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Archivo <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="archivo" required
              accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.mp4,.mov">
            <div class="form-text">Máximo 50MB. Formatos: PDF, imágenes, video.</div>
          </div>
          <div class="mb-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="visible_cliente" name="visible_cliente" value="1">
              <label class="form-check-label" for="visible_cliente">
                Visible para el cliente en el portal
              </label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea class="form-control" name="notas" rows="2" placeholder="Notas internas sobre este documento..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="upload" style="width:16px;height:16px;" class="me-2"></i>Subir Documento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
