<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; background:#f3f4f6; margin:0; padding:0; }
    .container { max-width:580px; margin:32px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:#1a1a2e; color:#fff; padding:28px 36px; }
    .header h1 { margin:0; font-size:22px; font-weight:700; }
    .header p  { margin:4px 0 0; font-size:13px; opacity:.7; }
    .body { padding:28px 36px; }
    .info-box { background:#f9fafb; border-radius:8px; padding:16px 20px; margin-bottom:20px; }
    .info-row { display:flex; justify-content:space-between; margin-bottom:6px; font-size:13px; }
    .info-row .label { color:#6b7280; }
    .info-row .value { font-weight:600; color:#1a1a2e; }
    .total-box { background:#1a1a2e; color:#fff; border-radius:8px; padding:16px 20px; text-align:center; margin-bottom:24px; }
    .total-box .amount { font-size:28px; font-weight:700; }
    .total-box .label  { font-size:12px; opacity:.7; margin-top:2px; }
    .mensaje { background:#eff6ff; border-left:3px solid #3b82f6; padding:12px 16px; border-radius:0 6px 6px 0; margin-bottom:20px; font-size:13px; color:#1e40af; line-height:1.6; }
    .footer { background:#f9fafb; padding:16px 36px; text-align:center; font-size:11px; color:#9ca3af; border-top:1px solid #e5e7eb; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; }
    .badge-enviada   { background:#dbeafe; color:#1d4ed8; }
    .badge-borrador  { background:#e5e7eb; color:#6b7280; }
    .badge-pagada    { background:#d1fae5; color:#059669; }
  </style>
</head>
<body>
  <div class="container">

    <div class="header">
      <h1>{{ $factura->tipo_label }} {{ $factura->folio }}</h1>
      <p>{{ $factura->creadoPor->name ?? config('app.name') }}</p>
    </div>

    <div class="body">

      <p style="font-size:14px;color:#374151;margin-bottom:20px;">
        Hola <strong>{{ $factura->cliente->nombre_contacto }}</strong>,<br><br>
        Adjunto encontrarás tu {{ strtolower($factura->tipo_label) }}
        <strong>{{ $factura->folio }}</strong>. Por favor revísala y no dudes en contactarnos si tienes alguna duda.
      </p>

      @if($mensajePersonal)
      <div class="mensaje">
        {{ $mensajePersonal }}
      </div>
      @endif

      {{-- Resumen --}}
      <div class="info-box">
        <div class="info-row">
          <span class="label">Folio</span>
          <span class="value">{{ $factura->folio }}</span>
        </div>
        <div class="info-row">
          <span class="label">Fecha de emisión</span>
          <span class="value">{{ $factura->fecha_emision->format('d/m/Y') }}</span>
        </div>
        @if($factura->fecha_vencimiento)
        <div class="info-row">
          <span class="label">Vencimiento</span>
          <span class="value">{{ $factura->fecha_vencimiento->format('d/m/Y') }}</span>
        </div>
        @endif
        @if($factura->metodo_pago)
        <div class="info-row">
          <span class="label">Método de pago</span>
          <span class="value">{{ $factura->metodo_pago }}</span>
        </div>
        @endif
        <div class="info-row">
          <span class="label">Estado</span>
          <span><span class="badge badge-{{ $factura->estado }}">{{ $factura->estado_label }}</span></span>
        </div>
      </div>

      {{-- Total --}}
      <div class="total-box">
        <div class="amount">${{ number_format($factura->total, 2) }} {{ $factura->moneda }}</div>
        <div class="label">Total a pagar</div>
      </div>

      <p style="font-size:13px;color:#6b7280;text-align:center;">
        El documento PDF está adjunto a este correo.
      </p>

    </div>

    <div class="footer">
      Enviado por <strong>{{ $factura->creadoPor->name ?? config('app.name') }}</strong>
      vía CHANGARROS · changarros.com
    </div>

  </div>
</body>
</html>
