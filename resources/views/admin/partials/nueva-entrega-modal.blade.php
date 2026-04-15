<div class="modal fade" id="modalEntrega" tabindex="-1" aria-labelledby="modalEntregaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.entregas.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEntregaLabel">
            <i data-lucide="upload" style="width:18px;height:18px;" class="me-2"></i>Nueva Entrega
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-8">
              <label class="form-label">Título <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="titulo" required placeholder="Ej: Propuesta de Logo v1">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select" name="tipo" required>
                <option value="diseno_inicial">Diseño Inicial</option>
                <option value="avance">Avance</option>
                <option value="revision">Revisión</option>
                <option value="entrega_final">Entrega Final</option>
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="2" placeholder="Describe brevemente esta entrega..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha de Entrega</label>
            <input type="date" class="form-control" name="fecha_entrega" value="{{ date('Y-m-d') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Archivos (imágenes, PDFs, videos)</label>
            <input type="file" class="form-control" name="archivos[]" multiple
              accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,.mp4,.mov">
            <div class="form-text">Puede subir múltiples archivos. Máximo 100MB por archivo.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">URL de Video (YouTube, Vimeo, etc.)</label>
            <input type="url" class="form-control" name="video_url" placeholder="https://youtube.com/watch?v=...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="send" style="width:16px;height:16px;" class="me-2"></i>Enviar al Cliente
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
