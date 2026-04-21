<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Cotización: {{ $cotizacion->nombre }} — CHANGARROS</title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background: #f0f2f7;
      color: #1a1d23;
      min-height: 100vh;
    }

    /* ─── Header bar ─── */
    .cot-header {
      background: #0f1117;
      color: #fff;
      padding: 0;
    }
    .cot-header-inner {
      max-width: 860px;
      margin: 0 auto;
      padding: 2rem 1.5rem 2.5rem;
    }
    .brand-logo {
      font-size: 1.1rem;
      font-weight: 700;
      letter-spacing: .04em;
      color: #a78bfa;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 1.5rem;
    }
    .brand-logo span { color: #fff; }
    .cot-title {
      font-size: 1.9rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: .5rem;
      line-height: 1.2;
    }
    .cot-subtitle { color: #8b909a; font-size: .9rem; }
    .status-pill {
      display: inline-block;
      padding: .28rem .9rem;
      border-radius: 9999px;
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .07em;
    }

    /* ─── Main body ─── */
    .cot-body {
      max-width: 860px;
      margin: -1.5rem auto 3rem;
      padding: 0 1rem;
    }

    /* ─── Cards ─── */
    .cot-card {
      background: #fff;
      border-radius: .75rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
      margin-bottom: 1.25rem;
      overflow: hidden;
    }
    .cot-card-header {
      padding: 1rem 1.5rem;
      border-bottom: 1px solid #f0f2f7;
      font-weight: 600;
      font-size: .85rem;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: #6b7280;
    }
    .cot-card-body { padding: 1.5rem; }

    /* ─── Client info ─── */
    .client-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: .75rem 2rem;
    }
    .info-label { font-size: .75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .2rem; }
    .info-value { font-size: .92rem; font-weight: 500; color: #1a1d23; }

    /* ─── Items table ─── */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table thead tr { background: #f8fafc; }
    .items-table th {
      font-size: .73rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: #9ca3af;
      padding: .75rem 1rem;
      text-align: right;
      border-bottom: 2px solid #f0f2f7;
    }
    .items-table th:first-child { text-align: left; }
    .items-table td {
      padding: 1rem 1rem;
      border-bottom: 1px solid #f0f2f7;
      font-size: .9rem;
      text-align: right;
      color: #374151;
    }
    .items-table td:first-child { text-align: left; font-weight: 500; color: #1a1d23; }
    .items-table tbody tr:last-child td { border-bottom: none; }

    /* ─── Financial summary ─── */
    .fin-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: .6rem 0;
      border-bottom: 1px solid #f0f2f7;
    }
    .fin-row:last-child { border-bottom: none; }
    .fin-label { color: #6b7280; font-size: .9rem; }
    .fin-value { font-weight: 600; font-size: .9rem; color: #1a1d23; }
    .fin-total {
      background: #0f1117;
      border-radius: .6rem;
      padding: 1.2rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
    }
    .fin-total-label { color: #8b909a; font-size: .8rem; text-transform: uppercase; letter-spacing: .08em; }
    .fin-total-amount { color: #fff; font-size: 2rem; font-weight: 800; }

    /* ─── Action buttons ─── */
    .action-card { border-radius: .75rem; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); }

    /* ─── Status banners ─── */
    .banner { border-radius: .75rem; padding: 1.5rem; margin-bottom: 1.25rem; }
    .banner-success { background: #ecfdf5; border: 1.5px solid #6ee7b7; }
    .banner-danger  { background: #fef2f2; border: 1.5px solid #fca5a5; }
    .banner-warning { background: #fffbeb; border: 1.5px solid #fcd34d; }
    .banner-info    { background: #eff6ff; border: 1.5px solid #93c5fd; }

    /* ─── Share strip ─── */
    .share-strip {
      display: flex; gap: .75rem; flex-wrap: wrap;
    }
    .share-btn {
      display: inline-flex; align-items: center; gap: .5rem;
      padding: .45rem 1rem;
      border-radius: 9999px;
      font-size: .83rem; font-weight: 500;
      text-decoration: none;
      border: 1.5px solid transparent;
      cursor: pointer;
      transition: opacity .15s;
    }
    .share-btn:hover { opacity: .85; }
    .share-btn-wa  { background: #25d366; color: #fff; }
    .share-btn-em  { background: #fff; color: #374151; border-color: #d1d5db; }
    .share-btn-cp  { background: #fff; color: #374151; border-color: #d1d5db; }

    /* ─── Approve / Reject ─── */
    .action-zone { display: flex; gap: 1rem; flex-wrap: wrap; }
    .btn-approve {
      flex: 1; min-width: 160px;
      padding: .9rem;
      background: #16a34a; color: #fff;
      border: none; border-radius: .6rem;
      font-size: 1rem; font-weight: 600;
      cursor: pointer; transition: background .2s;
    }
    .btn-approve:hover { background: #15803d; }
    .btn-reject {
      flex: 1; min-width: 160px;
      padding: .9rem;
      background: #fff; color: #dc2626;
      border: 2px solid #dc2626; border-radius: .6rem;
      font-size: 1rem; font-weight: 600;
      cursor: pointer; transition: all .2s;
    }
    .btn-reject:hover { background: #fef2f2; }

    /* ─── Modals overlay ─── */
    .modal-overlay {
      display: none;
      position: fixed; inset: 0; z-index: 1000;
      background: rgba(0,0,0,.45);
      align-items: center; justify-content: center;
      padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
      background: #fff; border-radius: 1rem;
      padding: 2rem; max-width: 440px; width: 100%;
      box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }
    .modal-title { font-size: 1.2rem; font-weight: 700; margin-bottom: .5rem; }
    .modal-close { float: right; background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #9ca3af; margin-top: -.3rem; }

    /* ─── Footer ─── */
    .cot-footer { text-align: center; color: #9ca3af; font-size: .78rem; padding: 2rem 1rem 3rem; }
    .cot-footer a { color: #a78bfa; text-decoration: none; }

    @media(max-width: 600px) {
      .cot-title { font-size: 1.4rem; }
      .client-grid { grid-template-columns: 1fr; }
      .fin-total-amount { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

<!-- ─── HEADER ─── -->
<div class="cot-header">
  <div class="cot-header-inner">
    <a class="brand-logo" href="#">CHANGARROS</a>
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="cot-title">{{ $cotizacion->nombre }}</h1>
        <p class="cot-subtitle mb-2">Preparada para {{ $cotizacion->cliente->nombre_empresa }}</p>
        @php
          $badgeColors = [
            'borrador'  => '#6b7280',
            'enviada'   => '#3b82f6',
            'vista'     => '#06b6d4',
            'aprobada'  => '#16a34a',
            'rechazada' => '#dc2626',
            'vencida'   => '#d97706',
          ];
          $badgeColor = $badgeColors[$cotizacion->estado] ?? '#6b7280';
        @endphp
        <span class="status-pill" style="background: {{ $badgeColor }}20; color: {{ $badgeColor }}; border: 1px solid {{ $badgeColor }}40;">
          {{ ucfirst($cotizacion->estado) }}
        </span>
      </div>
      <div class="text-end">
        @if($cotizacion->fecha_vencimiento)
        <p class="cot-subtitle mb-0">Vence</p>
        <p style="color:#fff;font-weight:600;">{{ $cotizacion->fecha_vencimiento->format('d M Y') }}</p>
        @endif
        <p class="cot-subtitle">Cotización #{{ $cotizacion->id }}</p>
      </div>
    </div>
  </div>
</div>

<!-- ─── BODY ─── -->
<div class="cot-body">

  {{-- Flash messages --}}
  @if(session('success'))
  <div class="banner banner-success mt-3">
    <strong>✓ {{ session('success') }}</strong>
  </div>
  @endif
  @if(session('error'))
  <div class="banner banner-danger mt-3">
    <strong>{{ session('error') }}</strong>
  </div>
  @endif
  @if(session('info'))
  <div class="banner banner-info mt-3">
    <strong>{{ session('info') }}</strong>
  </div>
  @endif

  {{-- ── STATUS BANNERS ── --}}
  @if($cotizacion->estado === 'aprobada')
  <div class="banner banner-success">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">✓</div>
    <strong style="font-size:1.05rem;color:#15803d;">¡Cotización Aprobada!</strong>
    <p class="mt-1 mb-0" style="color:#166534;font-size:.9rem;">
      Aprobada el {{ $cotizacion->aprobado_at->format('d/m/Y') }} a las {{ $cotizacion->aprobado_at->format('H:i') }}
      @if($cotizacion->aprobado_nombre) por <strong>{{ $cotizacion->aprobado_nombre }}</strong>@endif.
      El equipo de CHANGARROS se pondrá en contacto pronto.
    </p>
  </div>
  @elseif($cotizacion->estado === 'rechazada')
  <div class="banner banner-danger">
    <strong style="color:#b91c1c;">Cotización Rechazada</strong>
    @if($cotizacion->razon_rechazo)
    <p class="mt-1 mb-0" style="color:#991b1b;font-size:.9rem;">
      Motivo: {{ $cotizacion->razon_rechazo }}
    </p>
    @endif
  </div>
  @elseif($cotizacion->estado === 'vencida')
  <div class="banner banner-warning">
    <strong style="color:#92400e;">Esta cotización ha vencido</strong>
    <p class="mt-1 mb-0" style="color:#78350f;font-size:.9rem;">
      Contáctanos para recibir una cotización actualizada.
    </p>
  </div>
  @endif

  {{-- ── CLIENT INFO ── --}}
  <div class="cot-card">
    <div class="cot-card-header">Información del Cliente</div>
    <div class="cot-card-body">
      <div class="client-grid">
        <div>
          <p class="info-label">Empresa</p>
          <p class="info-value">{{ $cotizacion->cliente->nombre_empresa }}</p>
        </div>
        <div>
          <p class="info-label">Contacto</p>
          <p class="info-value">{{ $cotizacion->cliente->nombre_contacto ?? '—' }}</p>
        </div>
        <div>
          <p class="info-label">Correo</p>
          <p class="info-value">{{ $cotizacion->cliente->email }}</p>
        </div>
        <div>
          <p class="info-label">Teléfono</p>
          <p class="info-value">{{ $cotizacion->cliente->telefono ?? '—' }}</p>
        </div>
        @if($cotizacion->cliente->rfc)
        <div>
          <p class="info-label">RFC</p>
          <p class="info-value font-monospace">{{ $cotizacion->cliente->rfc }}</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- ── LINE ITEMS ── --}}
  <div class="cot-card">
    <div class="cot-card-header">Servicios Cotizados</div>
    <div style="overflow-x:auto;">
      <table class="items-table">
        <thead>
          <tr>
            <th>Descripción</th>
            <th>Cant.</th>
            <th>Precio Unit.</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cotizacion->items as $item)
          <tr>
            <td>{{ $item->descripcion }}</td>
            <td>{{ number_format($item->cantidad, 2) }}</td>
            <td>${{ number_format($item->precio_unitario, 2) }}</td>
            <td>${{ number_format($item->total, 2) }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" style="text-align:center;color:#9ca3af;padding:2rem;">
              No hay servicios en esta cotización.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ── FINANCIAL SUMMARY ── --}}
  <div class="cot-card">
    <div class="cot-card-header">Resumen Financiero</div>
    <div class="cot-card-body">
      <div class="fin-row">
        <span class="fin-label">Subtotal</span>
        <span class="fin-value">${{ number_format($cotizacion->subtotal, 2) }}</span>
      </div>
      <div class="fin-row">
        <span class="fin-label">IVA ({{ number_format($cotizacion->iva_porcentaje, 0) }}%)</span>
        <span class="fin-value">${{ number_format($cotizacion->iva_monto, 2) }}</span>
      </div>
      <div class="fin-total">
        <div>
          <p class="fin-total-label mb-0">Total Inversión</p>
        </div>
        <div class="fin-total-amount">${{ number_format($cotizacion->total, 2) }}</div>
      </div>
    </div>
  </div>

  {{-- ── NOTES ── --}}
  @if($cotizacion->notas)
  <div class="cot-card">
    <div class="cot-card-header">Notas y Condiciones</div>
    <div class="cot-card-body">
      <p style="color:#374151;white-space:pre-wrap;margin:0;font-size:.9rem;line-height:1.6;">{{ $cotizacion->notas }}</p>
    </div>
  </div>
  @endif

  {{-- ── ACTION ZONE (approve / reject) ── --}}
  @if(in_array($cotizacion->estado, ['enviada', 'vista']))
  <div class="cot-card">
    <div class="cot-card-header">Tu Respuesta</div>
    <div class="cot-card-body">
      <p style="color:#6b7280;font-size:.9rem;margin-bottom:1.25rem;">
        Revisa los servicios y el costo total. Cuando estés listo, aprueba o rechaza esta cotización.
      </p>
      <div class="action-zone">
        <button class="btn-approve" onclick="openApprove()">
          ✓ Aprobar Cotización
        </button>
        <button class="btn-reject" onclick="openReject()">
          ✗ Rechazar
        </button>
      </div>
    </div>
  </div>
  @endif

  {{-- ── SHARE ── --}}
  <div class="cot-card">
    <div class="cot-card-header">Compartir</div>
    <div class="cot-card-body">
      <div class="share-strip">
        <a href="{{ $cotizacion->whatsapp_url }}" target="_blank" class="share-btn share-btn-wa">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
          </svg>
          WhatsApp
        </a>
        <a href="{{ $cotizacion->email_url }}" class="share-btn share-btn-em">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          Email
        </a>
        <button class="share-btn share-btn-cp" onclick="copyLink(this)">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
          </svg>
          <span id="copyLabel">Copiar Enlace</span>
        </button>
      </div>
    </div>
  </div>

</div><!-- /cot-body -->

<!-- ─── FOOTER ─── -->
<div class="cot-footer">
  <p>Preparado por <strong>CHANGARROS</strong> · Cotización generada el {{ $cotizacion->created_at->format('d M Y') }}</p>
  <p>¿Preguntas? Contáctanos · <a href="mailto:hola@changarros.com">hola@changarros.com</a></p>
</div>

<!-- ─── APPROVE MODAL ─── -->
<div class="modal-overlay" id="modalApprove">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModals()">×</button>
    <div class="modal-title" style="color:#15803d;">Aprobar Cotización</div>
    <p style="color:#6b7280;font-size:.9rem;margin-bottom:1.25rem;">
      Al aprobar, autorizas a CHANGARROS a iniciar los trabajos descritos en esta cotización por un total de
      <strong>${{ number_format($cotizacion->total, 2) }}</strong>.
    </p>
    <form method="POST" action="{{ route('cotizacion.aprobar', $cotizacion->token) }}">
      @csrf
      <div style="margin-bottom:1rem;">
        <label style="font-size:.85rem;font-weight:500;display:block;margin-bottom:.4rem;">Tu nombre</label>
        <input type="text" name="nombre" class="form-control"
               value="{{ $cotizacion->cliente->nombre_contacto }}"
               placeholder="Nombre del aprobador" required>
      </div>
      <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
        <button type="button" onclick="closeModals()"
                style="flex:1;padding:.75rem;background:#f9fafb;border:1px solid #d1d5db;border-radius:.5rem;font-weight:500;cursor:pointer;">
          Cancelar
        </button>
        <button type="submit"
                style="flex:2;padding:.75rem;background:#16a34a;color:#fff;border:none;border-radius:.5rem;font-weight:600;font-size:1rem;cursor:pointer;">
          ✓ Sí, Aprobar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ─── REJECT MODAL ─── -->
<div class="modal-overlay" id="modalReject">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModals()">×</button>
    <div class="modal-title" style="color:#dc2626;">Rechazar Cotización</div>
    <p style="color:#6b7280;font-size:.9rem;margin-bottom:1.25rem;">
      Por favor indícanos el motivo del rechazo para que podamos mejorar nuestra propuesta.
    </p>
    <form method="POST" action="{{ route('cotizacion.rechazar', $cotizacion->token) }}">
      @csrf
      <div style="margin-bottom:1rem;">
        <label style="font-size:.85rem;font-weight:500;display:block;margin-bottom:.4rem;">Motivo del rechazo</label>
        <textarea name="razon" class="form-control" rows="4"
                  placeholder="El precio está fuera de nuestro presupuesto, necesitamos más tiempo, etc."
                  required minlength="5"></textarea>
        @error('razon')
        <div style="color:#dc2626;font-size:.8rem;margin-top:.3rem;">{{ $message }}</div>
        @enderror
      </div>
      <div style="display:flex;gap:.75rem;margin-top:1.25rem;">
        <button type="button" onclick="closeModals()"
                style="flex:1;padding:.75rem;background:#f9fafb;border:1px solid #d1d5db;border-radius:.5rem;font-weight:500;cursor:pointer;">
          Cancelar
        </button>
        <button type="submit"
                style="flex:2;padding:.75rem;background:#dc2626;color:#fff;border:none;border-radius:.5rem;font-weight:600;font-size:1rem;cursor:pointer;">
          Rechazar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openApprove() {
  document.getElementById('modalApprove').classList.add('active');
}
function openReject() {
  document.getElementById('modalReject').classList.add('active');
}
function closeModals() {
  document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
}
// Close on backdrop click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function(e) {
    if (e.target === this) closeModals();
  });
});

function copyLink(btn) {
  navigator.clipboard.writeText(window.location.href).then(() => {
    document.getElementById('copyLabel').textContent = '¡Copiado!';
    setTimeout(() => {
      document.getElementById('copyLabel').textContent = 'Copiar Enlace';
    }, 2000);
  });
}

// Auto-open reject modal if there are errors
@if($errors->has('razon'))
  document.addEventListener('DOMContentLoaded', () => openReject());
@endif
</script>

</body>
</html>
