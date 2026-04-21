<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a2e; background:#fff; }

    .page { padding: 40px 50px; }

    /* ── Header ── */
    .header { display:table; width:100%; margin-bottom:32px; }
    .header-left  { display:table-cell; vertical-align:top; width:60%; }
    .header-right { display:table-cell; vertical-align:top; width:40%; text-align:right; }

    .empresa-nombre { font-size:22px; font-weight:700; color:#1a1a2e; margin-bottom:4px; }
    .empresa-sub    { font-size:10px; color:#6b7280; line-height:1.6; }

    .doc-tipo  { font-size:28px; font-weight:700; color:#1a1a2e; text-transform:uppercase; letter-spacing:1px; }
    .doc-folio { font-size:13px; color:#6b7280; margin-top:2px; }
    .doc-estado {
      display:inline-block; margin-top:6px;
      padding: 3px 10px; border-radius:12px; font-size:10px; font-weight:700; text-transform:uppercase;
    }
    .estado-borrador  { background:#e5e7eb; color:#6b7280; }
    .estado-enviada   { background:#dbeafe; color:#1d4ed8; }
    .estado-pagada    { background:#d1fae5; color:#059669; }
    .estado-cancelada { background:#fee2e2; color:#dc2626; }

    /* ── Divider ── */
    .divider { border:none; border-top:2px solid #1a1a2e; margin:0 0 24px; }

    /* ── Info grid ── */
    .info-grid { display:table; width:100%; margin-bottom:28px; }
    .info-col  { display:table-cell; vertical-align:top; width:33.33%; padding-right:16px; }
    .info-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#9ca3af; margin-bottom:4px; }
    .info-value { font-size:12px; color:#1a1a2e; line-height:1.5; }

    /* ── Items table ── */
    .items-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
    .items-table thead tr { background:#1a1a2e; color:#fff; }
    .items-table thead th { padding:9px 12px; font-size:10px; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
    .items-table thead th:last-child  { text-align:right; }
    .items-table thead th:nth-child(2),
    .items-table thead th:nth-child(3) { text-align:center; }
    .items-table tbody tr { border-bottom:1px solid #f3f4f6; }
    .items-table tbody tr:nth-child(even) { background:#f9fafb; }
    .items-table tbody td { padding:9px 12px; font-size:11px; vertical-align:top; }
    .items-table tbody td:nth-child(2),
    .items-table tbody td:nth-child(3) { text-align:center; }
    .items-table tbody td:last-child  { text-align:right; font-weight:600; }
    .unidad { font-size:9px; color:#9ca3af; display:block; }

    /* ── Totales ── */
    .totales-wrap { display:table; width:100%; margin-bottom:28px; }
    .totales-spacer { display:table-cell; width:55%; }
    .totales-box    { display:table-cell; width:45%; }
    .totales-row { display:table; width:100%; border-bottom:1px solid #f3f4f6; }
    .totales-row > div { display:table-cell; padding:6px 0; font-size:11px; }
    .totales-row .t-label { color:#6b7280; }
    .totales-row .t-value { text-align:right; font-weight:600; }
    .totales-row.total-final { border-top:2px solid #1a1a2e; border-bottom:none; }
    .totales-row.total-final .t-label,
    .totales-row.total-final .t-value { font-size:14px; font-weight:700; color:#1a1a2e; }

    /* ── Notas ── */
    .notas-box { background:#f9fafb; border-left:3px solid #1a1a2e; padding:12px 16px; margin-bottom:16px; border-radius:0 4px 4px 0; }
    .notas-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:4px; }
    .notas-text  { font-size:11px; color:#374151; line-height:1.6; }

    /* ── Footer ── */
    .footer { border-top:1px solid #e5e7eb; padding-top:12px; margin-top:16px; }
    .footer-text { font-size:9px; color:#9ca3af; text-align:center; }
  </style>
</head>
<body>
<div class="page">

  {{-- ── Header ── --}}
  <div class="header">
    <div class="header-left">
      <div class="empresa-nombre">{{ $factura->creadoPor->name ?? config('app.name') }}</div>
      <div class="empresa-sub">
        {{ $factura->creadoPor->email ?? '' }}<br>
        {{ $factura->creadoPor->telefono ?? '' }}
      </div>
    </div>
    <div class="header-right">
      <div class="doc-tipo">{{ $factura->tipo_label }}</div>
      <div class="doc-folio">{{ $factura->folio }}</div>
      <div>
        <span class="doc-estado estado-{{ $factura->estado }}">{{ $factura->estado_label }}</span>
      </div>
    </div>
  </div>

  <hr class="divider">

  {{-- ── Info ── --}}
  <div class="info-grid">
    <div class="info-col">
      <div class="info-label">Facturar a</div>
      <div class="info-value">
        <strong>{{ $factura->cliente->nombre_empresa }}</strong><br>
        {{ $factura->cliente->nombre_contacto }}<br>
        {{ $factura->cliente->email }}<br>
        @if($factura->cliente->rfc) RFC: {{ $factura->cliente->rfc }} @endif
      </div>
    </div>
    <div class="info-col">
      <div class="info-label">Detalles</div>
      <div class="info-value">
        Emisión: {{ $factura->fecha_emision->format('d/m/Y') }}<br>
        @if($factura->fecha_vencimiento)
          Vencimiento: {{ $factura->fecha_vencimiento->format('d/m/Y') }}<br>
        @endif
        @if($factura->metodo_pago)
          Método: {{ $factura->metodo_pago }}<br>
        @endif
        Moneda: {{ $factura->moneda }}
      </div>
    </div>
    @if($factura->proyecto)
    <div class="info-col">
      <div class="info-label">Proyecto</div>
      <div class="info-value">{{ $factura->proyecto->nombre }}</div>
    </div>
    @endif
  </div>

  {{-- ── Items ── --}}
  <table class="items-table">
    <thead>
      <tr>
        <th style="width:50%;">Descripción</th>
        <th style="width:12%;">Cant.</th>
        <th style="width:15%;">Unidad</th>
        <th style="width:10%;">P. Unit.</th>
        <th style="width:13%;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($factura->items as $item)
      <tr>
        <td>{{ $item->descripcion }}</td>
        <td>{{ number_format($item->cantidad, 2) }}</td>
        <td>{{ $item->unidad }}</td>
        <td>${{ number_format($item->precio_unitario, 2) }}</td>
        <td>${{ number_format($item->subtotal, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- ── Totales ── --}}
  <div class="totales-wrap">
    <div class="totales-spacer"></div>
    <div class="totales-box">
      <div class="totales-row">
        <div class="t-label">Subtotal</div>
        <div class="t-value">${{ number_format($factura->subtotal, 2) }}</div>
      </div>
      @if($factura->descuento > 0)
      <div class="totales-row">
        <div class="t-label">Descuento</div>
        <div class="t-value" style="color:#dc2626;">-${{ number_format($factura->descuento, 2) }}</div>
      </div>
      @endif
      @if($factura->impuesto_porcentaje > 0)
      <div class="totales-row">
        <div class="t-label">IVA ({{ $factura->impuesto_porcentaje }}%)</div>
        <div class="t-value">${{ number_format($factura->impuesto_monto, 2) }}</div>
      </div>
      @endif
      <div class="totales-row total-final">
        <div class="t-label">TOTAL {{ $factura->moneda }}</div>
        <div class="t-value">${{ number_format($factura->total, 2) }}</div>
      </div>
    </div>
  </div>

  {{-- ── Notas ── --}}
  @if($factura->notas)
  <div class="notas-box">
    <div class="notas-title">Notas</div>
    <div class="notas-text">{{ $factura->notas }}</div>
  </div>
  @endif
  @if($factura->condiciones)
  <div class="notas-box">
    <div class="notas-title">Términos y Condiciones</div>
    <div class="notas-text">{{ $factura->condiciones }}</div>
  </div>
  @endif

  {{-- ── Footer ── --}}
  <div class="footer">
    <div class="footer-text">
      Documento generado el {{ now()->format('d/m/Y H:i') }} · ESPIRAL ERP · espiraljrz.com
    </div>
  </div>

</div>
</body>
</html>
