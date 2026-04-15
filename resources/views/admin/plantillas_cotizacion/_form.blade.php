<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="card-title mb-0">Información de la Plantilla</h6>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nombre <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $plantilla->nombre) }}" required
            placeholder="Ej: Pack Redes Sociales Básico">
          @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
            rows="2" placeholder="Descripción corta de esta plantilla...">{{ old('descripcion', $plantilla->descripcion) }}</textarea>
          @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0">Ítems de la Plantilla</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarItem">
          <i data-lucide="plus" style="width:14px;height:14px;" class="me-1"></i>Agregar ítem
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0" id="tablaItems">
            <thead class="table-light">
              <tr>
                <th style="width:45%">Descripción</th>
                <th style="width:15%">Cantidad</th>
                <th style="width:25%">Precio unitario</th>
                <th style="width:15%">Subtotal</th>
                <th style="width:5%"></th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              @forelse($plantilla->items ?? [] as $i => $item)
              <tr class="item-row">
                <td><input type="text" name="items[{{ $i }}][descripcion]" class="form-control form-control-sm" value="{{ $item->descripcion }}" required></td>
                <td><input type="number" name="items[{{ $i }}][cantidad]" class="form-control form-control-sm item-cant" value="{{ $item->cantidad }}" min="0.01" step="0.01" required></td>
                <td><input type="number" name="items[{{ $i }}][precio_unitario]" class="form-control form-control-sm item-precio" value="{{ $item->precio_unitario }}" min="0" step="0.01" required></td>
                <td class="align-middle item-subtotal small text-muted">${{ number_format($item->cantidad * $item->precio_unitario, 2) }}</td>
                <td class="align-middle">
                  <button type="button" class="btn btn-xs btn-outline-danger btn-remove-item">
                    <i data-lucide="x" style="width:12px;height:12px;"></i>
                  </button>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-3 py-2 border-top text-end">
          <span class="text-muted small">Total estimado: </span>
          <strong id="totalEstimado">$0.00</strong>
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">
        <i data-lucide="save" style="width:16px;height:16px;" class="me-2"></i>Guardar Plantilla
      </button>
      <a href="{{ route('admin.plantillas-cotizacion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </div>
</div>
