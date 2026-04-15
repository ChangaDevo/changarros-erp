<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.pagos.store') }}">
        @csrf
        <input type="hidden" name="proyecto_id" value="{{ $proyecto->id }}">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPagoLabel">
            <i data-lucide="dollar-sign" style="width:18px;height:18px;" class="me-2"></i>Generar Cobro
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Concepto <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="concepto" required
              placeholder="Ej: Anticipo 50%, Segunda etapa...">
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Monto ($) <span class="text-danger">*</span></label>
              <input type="number" class="form-control" name="monto" required step="0.01" min="0"
                placeholder="0.00">
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Vencimiento</label>
              <input type="date" class="form-control" name="fecha_vencimiento">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Notas</label>
            <textarea class="form-control" name="notas" rows="2"
              placeholder="Instrucciones de pago, referencia bancaria, etc."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">
            <i data-lucide="file-text" style="width:16px;height:16px;" class="me-2"></i>Generar Cobro
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
