<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $titulo ?? 'Notificación' }}</title>
  <style>
    body { margin:0; padding:0; background:#f4f5f7; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color:#333; }
    .wrapper { max-width:580px; margin:32px auto; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
    .header  { background:#1a1d23; padding:24px 32px; }
    .header-logo { color:#fff; font-size:20px; font-weight:700; text-decoration:none; }
    .header-logo span { color:#6c63ff; }
    .body    { padding:32px; }
    .badge   { display:inline-block; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600; margin-bottom:16px; }
    .badge-success { background:#d1fae5; color:#065f46; }
    .badge-warning { background:#fef3c7; color:#92400e; }
    .badge-info    { background:#dbeafe; color:#1e40af; }
    .badge-danger  { background:#fee2e2; color:#991b1b; }
    h2 { margin:0 0 8px; font-size:22px; color:#1a1d23; }
    p  { margin:0 0 16px; line-height:1.6; color:#555; }
    .details-box { background:#f8f9fa; border-left:4px solid #6c63ff; border-radius:4px; padding:16px; margin:20px 0; }
    .details-box p { margin:4px 0; color:#333; font-size:14px; }
    .btn { display:inline-block; padding:12px 28px; background:#6c63ff; color:#fff !important; text-decoration:none; border-radius:6px; font-weight:600; font-size:15px; margin:8px 0; }
    .btn-success { background:#059669; }
    .btn-warning { background:#d97706; }
    .footer { background:#f4f5f7; padding:20px 32px; text-align:center; }
    .footer p { margin:0; font-size:12px; color:#999; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <a href="{{ config('app.url') }}" class="header-logo">espiral<span>ERP</span></a>
  </div>
  <div class="body">
    @yield('content')
  </div>
  <div class="footer">
    <p>Este correo fue enviado automáticamente por espiralERP.<br>Por favor no responder a este mensaje.</p>
  </div>
</div>
</body>
</html>
