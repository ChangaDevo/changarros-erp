<script>
let itemIndex = document.querySelectorAll('#itemsBody .item-row').length;

function recalcularTotal() {
  let total = 0;
  document.querySelectorAll('#itemsBody .item-row').forEach(row => {
    const cant   = parseFloat(row.querySelector('.item-cant')?.value) || 0;
    const precio = parseFloat(row.querySelector('.item-precio')?.value) || 0;
    const sub    = cant * precio;
    total += sub;
    const subtotalEl = row.querySelector('.item-subtotal');
    if (subtotalEl) subtotalEl.textContent = '$' + sub.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  });
  document.getElementById('totalEstimado').textContent =
    '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  if (window.lucide) lucide.createIcons();
}

function agregarFila(desc = '', cant = 1, precio = 0) {
  const i = itemIndex++;
  const row = document.createElement('tr');
  row.className = 'item-row';
  row.innerHTML = `
    <td><input type="text" name="items[${i}][descripcion]" class="form-control form-control-sm" value="${desc}" placeholder="Descripción del servicio" required></td>
    <td><input type="number" name="items[${i}][cantidad]" class="form-control form-control-sm item-cant" value="${cant}" min="0.01" step="0.01" required></td>
    <td><input type="number" name="items[${i}][precio_unitario]" class="form-control form-control-sm item-precio" value="${precio}" min="0" step="0.01" required></td>
    <td class="align-middle item-subtotal small text-muted">$0.00</td>
    <td class="align-middle">
      <button type="button" class="btn btn-xs btn-outline-danger btn-remove-item">
        <i data-lucide="x" style="width:12px;height:12px;"></i>
      </button>
    </td>`;
  document.getElementById('itemsBody').appendChild(row);
  recalcularTotal();
}

document.getElementById('btnAgregarItem').addEventListener('click', () => agregarFila());

document.getElementById('itemsBody').addEventListener('input', recalcularTotal);

document.getElementById('itemsBody').addEventListener('click', e => {
  const btn = e.target.closest('.btn-remove-item');
  if (btn) {
    btn.closest('tr').remove();
    recalcularTotal();
  }
});

document.addEventListener('DOMContentLoaded', () => {
  recalcularTotal();
  if (window.lucide) lucide.createIcons();
});
</script>
