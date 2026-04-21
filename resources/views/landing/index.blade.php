<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CHANGARROS — El ERP para estudios creativos</title>
  <meta name="description" content="Gestiona clientes, proyectos, facturas, tiempo y marcas desde un solo lugar. El ERP diseñado para estudios creativos y agencias.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

  <style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    :root {
      --glass-bg:     rgba(255,255,255,0.07);
      --glass-border: rgba(255,255,255,0.15);
      --glass-hover:  rgba(255,255,255,0.12);
      --accent:       #a78bfa;
      --accent2:      #60a5fa;
      --accent3:      #34d399;
      --text:         #f1f5f9;
      --text-muted:   rgba(241,245,249,0.55);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', sans-serif;
      background: #050816;
      color: var(--text);
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ══ ORBS (fondo animado) ═══════════════════════════════════ */
    .orb {
      position: fixed;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.25;
      pointer-events: none;
      animation: float 8s ease-in-out infinite;
    }
    .orb-1 { width:500px; height:500px; background:#7c3aed; top:-150px; left:-150px; animation-delay:0s; }
    .orb-2 { width:400px; height:400px; background:#2563eb; top:40%; right:-100px; animation-delay:-3s; }
    .orb-3 { width:350px; height:350px; background:#059669; bottom:-100px; left:30%; animation-delay:-6s; }
    @keyframes float {
      0%,100% { transform: translateY(0) scale(1); }
      50%      { transform: translateY(-30px) scale(1.05); }
    }

    /* ══ GLASS MIXIN ════════════════════════════════════════════ */
    .glass {
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      border-radius: 16px;
    }
    .glass:hover { background: var(--glass-hover); }

    /* ══ NAV ════════════════════════════════════════════════════ */
    nav {
      position: fixed; top:0; left:0; right:0; z-index:100;
      padding: 16px 0;
      background: rgba(5,8,22,0.6);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--glass-border);
      transition: background .3s;
    }
    .nav-inner {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .nav-logo {
      font-size: 1.4rem;
      font-weight: 800;
      color: #fff;
      text-decoration: none;
      letter-spacing: -.03em;
    }
    .nav-logo span { color: var(--accent); }
    .nav-links { display:flex; align-items:center; gap:32px; }
    .nav-links a {
      color: var(--text-muted);
      text-decoration: none;
      font-size: .9rem;
      font-weight: 500;
      transition: color .2s;
    }
    .nav-links a:hover { color: #fff; }
    .btn-nav {
      background: var(--accent);
      color: #fff !important;
      padding: 8px 20px;
      border-radius: 8px;
      font-weight: 600 !important;
      transition: opacity .2s !important;
    }
    .btn-nav:hover { opacity: .85; }

    @media(max-width:640px) {
      .nav-links { gap:16px; }
      .nav-links a:not(.btn-nav) { display:none; }
    }

    /* ══ SECTIONS ═══════════════════════════════════════════════ */
    section { padding: 100px 24px; max-width:1200px; margin:0 auto; }
    .section-label {
      font-size: .7rem;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 12px;
    }
    h2.section-title {
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 800;
      letter-spacing: -.04em;
      line-height: 1.1;
      margin-bottom: 16px;
    }
    .section-sub {
      color: var(--text-muted);
      font-size: 1.1rem;
      max-width: 560px;
    }

    /* ══ HERO ═══════════════════════════════════════════════════ */
    #hero {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding-top: 120px;
      padding-bottom: 80px;
      max-width: 100%;
    }
    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 16px;
      border-radius: 999px;
      font-size: .8rem;
      font-weight: 600;
      color: var(--accent);
      margin-bottom: 28px;
    }
    .hero-badge-dot { width:6px; height:6px; background:var(--accent); border-radius:50%; animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{opacity:1}50%{opacity:.4} }

    h1.hero-title {
      font-size: clamp(2.8rem, 7vw, 5.5rem);
      font-weight: 900;
      letter-spacing: -.05em;
      line-height: 1.0;
      margin-bottom: 24px;
      max-width: 900px;
    }
    .hero-title .grad {
      background: linear-gradient(135deg, var(--accent) 0%, var(--accent2) 50%, var(--accent3) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .hero-sub {
      font-size: clamp(1rem, 2vw, 1.25rem);
      color: var(--text-muted);
      max-width: 620px;
      margin: 0 auto 40px;
    }
    .hero-ctas { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; }
    .btn-primary-gl {
      display: inline-flex; align-items:center; gap:8px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      color: #fff;
      padding: 14px 32px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 0 40px rgba(167,139,250,.3);
    }
    .btn-primary-gl:hover { transform: translateY(-2px); box-shadow: 0 8px 40px rgba(167,139,250,.5); }
    .btn-ghost-gl {
      display: inline-flex; align-items:center; gap:8px;
      background: var(--glass-bg);
      backdrop-filter: blur(12px);
      border: 1px solid var(--glass-border);
      color: var(--text);
      padding: 14px 32px;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      text-decoration: none;
      transition: background .2s, transform .2s;
    }
    .btn-ghost-gl:hover { background: var(--glass-hover); transform: translateY(-2px); }

    /* Hero mockup */
    .hero-mockup {
      margin-top: 64px;
      width: 100%;
      max-width: 900px;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid var(--glass-border);
      box-shadow: 0 40px 100px rgba(0,0,0,.6), 0 0 60px rgba(167,139,250,.15);
      background: var(--glass-bg);
      backdrop-filter: blur(10px);
    }
    .mockup-bar {
      display:flex; align-items:center; gap:8px;
      padding: 12px 16px;
      border-bottom: 1px solid var(--glass-border);
      background: rgba(255,255,255,.04);
    }
    .mockup-dot { width:10px; height:10px; border-radius:50%; }
    .mockup-url {
      flex:1; background:rgba(255,255,255,.06); border-radius:6px;
      padding:4px 12px; font-size:.75rem; color:var(--text-muted);
      margin: 0 12px;
    }
    .mockup-body {
      display:grid; grid-template-columns:180px 1fr;
      min-height:320px;
    }
    .mockup-sidebar {
      border-right:1px solid var(--glass-border);
      padding: 16px 12px;
      display:flex; flex-direction:column; gap:4px;
    }
    .mockup-nav-item {
      display:flex; align-items:center; gap:8px;
      padding:8px 10px; border-radius:7px;
      font-size:.72rem; color:var(--text-muted);
    }
    .mockup-nav-item.active {
      background:rgba(167,139,250,.15); color:var(--accent);
    }
    .mockup-nav-dot { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }
    .mockup-content { padding:20px; }
    .mockup-kpi-row { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px; }
    .mockup-kpi {
      background:rgba(255,255,255,.05); border:1px solid var(--glass-border);
      border-radius:10px; padding:12px;
    }
    .mockup-kpi-val { font-size:1.2rem; font-weight:700; }
    .mockup-kpi-lbl { font-size:.65rem; color:var(--text-muted); margin-top:2px; }
    .mockup-table { background:rgba(255,255,255,.03); border-radius:8px; overflow:hidden; }
    .mockup-table-row {
      display:flex; justify-content:space-between; align-items:center;
      padding:8px 12px; border-bottom:1px solid rgba(255,255,255,.05);
      font-size:.7rem;
    }
    .mockup-badge {
      padding:2px 8px; border-radius:99px; font-size:.6rem; font-weight:600;
    }

    @media(max-width:600px) { .mockup-body { grid-template-columns:1fr; } .mockup-sidebar { display:none; } }

    /* ══ FEATURES ════════════════════════════════════════════════ */
    #features { padding-top: 60px; }
    .features-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap:24px;
      margin-top: 60px;
    }
    .feature-card {
      padding: 32px;
      border-radius: 20px;
      background: var(--glass-bg);
      backdrop-filter: blur(16px);
      border: 1px solid var(--glass-border);
      transition: transform .3s, background .3s, box-shadow .3s;
      position:relative; overflow:hidden;
    }
    .feature-card::before {
      content:''; position:absolute; top:0; left:0; right:0; height:2px;
      background: linear-gradient(90deg, transparent, var(--card-accent, var(--accent)), transparent);
      opacity: 0; transition: opacity .3s;
    }
    .feature-card:hover { transform:translateY(-6px); background:var(--glass-hover); box-shadow:0 20px 60px rgba(0,0,0,.4); }
    .feature-card:hover::before { opacity:1; }

    .feature-icon {
      width:48px; height:48px;
      display:flex; align-items:center; justify-content:center;
      border-radius:12px;
      margin-bottom:20px;
      font-size:1.4rem;
    }
    .feature-card h3 { font-size:1.1rem; font-weight:700; margin-bottom:10px; }
    .feature-card p  { font-size:.9rem; color:var(--text-muted); line-height:1.7; }
    .feature-tag {
      display:inline-flex; margin-top:16px;
      padding:4px 10px; border-radius:999px; font-size:.7rem; font-weight:600;
    }

    /* ══ FOR WHO ═════════════════════════════════════════════════ */
    #quien { text-align:center; }
    .quien-grid {
      display:grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap:20px; margin-top:52px;
    }
    .quien-card {
      padding:36px 28px; border-radius:20px; text-align:center;
      background:var(--glass-bg); backdrop-filter:blur(16px);
      border:1px solid var(--glass-border);
      transition:transform .3s;
    }
    .quien-card:hover { transform:translateY(-4px); }
    .quien-emoji { font-size:2.5rem; margin-bottom:16px; }
    .quien-card h3 { font-size:1.1rem; font-weight:700; margin-bottom:8px; }
    .quien-card p  { font-size:.85rem; color:var(--text-muted); }

    /* ══ STATS ═══════════════════════════════════════════════════ */
    #stats { text-align:center; padding-top:0; }
    .stats-wrap {
      display:grid;
      grid-template-columns:repeat(auto-fit, minmax(180px,1fr));
      gap:2px;
      background:var(--glass-border);
      border-radius:20px; overflow:hidden;
      border:1px solid var(--glass-border);
    }
    .stat-item {
      padding:40px 24px;
      background:var(--glass-bg);
      backdrop-filter:blur(16px);
      text-align:center;
    }
    .stat-num {
      font-size:3rem; font-weight:900; letter-spacing:-.04em;
      background:linear-gradient(135deg,var(--accent),var(--accent2));
      -webkit-background-clip:text; -webkit-text-fill-color:transparent;
      background-clip:text;
    }
    .stat-lbl { font-size:.85rem; color:var(--text-muted); margin-top:6px; }

    /* ══ PRICING ═════════════════════════════════════════════════ */
    #pricing { text-align:center; }
    .pricing-grid {
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
      gap:24px; margin-top:52px;
      align-items:start;
    }
    .pricing-card {
      padding:36px 32px; border-radius:24px;
      background:var(--glass-bg); backdrop-filter:blur(16px);
      border:1px solid var(--glass-border);
      text-align:left; position:relative; overflow:hidden;
      transition:transform .3s;
    }
    .pricing-card:hover { transform:translateY(-4px); }
    .pricing-card.featured {
      background:rgba(167,139,250,.12);
      border-color:rgba(167,139,250,.4);
      box-shadow:0 0 60px rgba(167,139,250,.2);
    }
    .pricing-badge {
      position:absolute; top:20px; right:20px;
      background:linear-gradient(135deg,var(--accent),var(--accent2));
      color:#fff; font-size:.7rem; font-weight:700; padding:4px 12px;
      border-radius:999px; text-transform:uppercase; letter-spacing:.08em;
    }
    .pricing-tier { font-size:.75rem; font-weight:700; letter-spacing:.15em; text-transform:uppercase; color:var(--accent); margin-bottom:12px; }
    .pricing-price {
      display:flex; align-items:flex-end; gap:4px; margin-bottom:4px;
    }
    .pricing-currency { font-size:1.2rem; font-weight:700; margin-bottom:8px; color:var(--text-muted); }
    .pricing-amount { font-size:3.5rem; font-weight:900; letter-spacing:-.05em; line-height:1; }
    .pricing-period { font-size:.85rem; color:var(--text-muted); margin-bottom:20px; }
    .pricing-desc { font-size:.9rem; color:var(--text-muted); margin-bottom:28px; padding-bottom:28px; border-bottom:1px solid var(--glass-border); }
    .pricing-features { list-style:none; display:flex; flex-direction:column; gap:12px; margin-bottom:32px; }
    .pricing-features li {
      display:flex; align-items:center; gap:10px; font-size:.875rem;
    }
    .pricing-features li .check { color:var(--accent3); flex-shrink:0; }
    .pricing-features li .cross { color:rgba(255,255,255,.25); flex-shrink:0; }
    .btn-pricing {
      display:block; text-align:center; padding:14px;
      border-radius:10px; font-weight:700; text-decoration:none;
      transition:transform .2s, box-shadow .2s;
    }
    .btn-pricing-outline {
      border:1px solid var(--glass-border); color:var(--text);
      background:var(--glass-bg);
    }
    .btn-pricing-outline:hover { background:var(--glass-hover); }
    .btn-pricing-fill {
      background:linear-gradient(135deg,var(--accent),var(--accent2));
      color:#fff; box-shadow:0 0 30px rgba(167,139,250,.3);
    }
    .btn-pricing-fill:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(167,139,250,.5); }

    /* ══ CTA FINAL ═══════════════════════════════════════════════ */
    #cta-final {
      text-align:center;
      padding: 120px 24px;
      max-width:100%;
    }
    .cta-box {
      max-width:700px; margin:0 auto;
      padding:64px 48px; border-radius:28px;
      background:var(--glass-bg); backdrop-filter:blur(20px);
      border:1px solid var(--glass-border);
      box-shadow:0 0 80px rgba(167,139,250,.12);
      position:relative; overflow:hidden;
    }
    .cta-box::before {
      content:''; position:absolute; inset:0;
      background:radial-gradient(ellipse at 50% 0%, rgba(167,139,250,.15) 0%, transparent 70%);
      pointer-events:none;
    }
    .cta-box h2 { font-size:clamp(1.8rem,4vw,2.8rem); font-weight:900; letter-spacing:-.04em; margin-bottom:16px; }
    .cta-box p  { color:var(--text-muted); margin-bottom:36px; font-size:1.05rem; }

    /* ══ FOOTER ══════════════════════════════════════════════════ */
    footer {
      border-top:1px solid var(--glass-border);
      padding: 40px 24px;
      text-align:center;
      color:var(--text-muted);
      font-size:.85rem;
    }
    footer a { color:var(--text-muted); text-decoration:none; }
    footer a:hover { color:#fff; }
    .footer-inner { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
    .footer-logo { font-weight:800; font-size:1.1rem; color:#fff; }
    .footer-logo span { color:var(--accent); }
    .footer-links { display:flex; gap:24px; }

    /* ══ SCROLL REVEAL ════════════════════════════════════════════ */
    .reveal { opacity:0; transform:translateY(30px); transition:opacity .6s ease, transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
  </style>
</head>
<body>

  <!-- Orbs de fondo -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <!-- ══ NAV ══════════════════════════════════════════════════ -->
  <nav>
    <div class="nav-inner">
      <a href="/" class="nav-logo">CHANGARROS<span>.</span></a>
      <div class="nav-links">
        <a href="#features">Características</a>
        <a href="#pricing">Precios</a>
        <a href="{{ route('admin.login') }}" class="btn-nav">Entrar al panel</a>
      </div>
    </div>
  </nav>

  <!-- ══ HERO ═════════════════════════════════════════════════ -->
  <section id="hero">
    <div class="glass hero-badge">
      <span class="hero-badge-dot"></span>
      Plataforma todo-en-uno para creativos
    </div>

    <h1 class="hero-title">
      El ERP que los<br>
      <span class="grad">estudios creativos</span><br>
      merecían
    </h1>

    <p class="hero-sub">
      Gestiona clientes, proyectos, cotizaciones, facturas, tiempo y marcas.
      Todo en un solo lugar, todo tuyo, sin complicaciones.
    </p>

    <div class="hero-ctas">
      <a href="{{ route('admin.login') }}" class="btn-primary-gl">
        <i data-lucide="zap" style="width:18px;height:18px;"></i>
        Empieza ahora
      </a>
      <a href="#features" class="btn-ghost-gl">
        Ver características
        <i data-lucide="arrow-down" style="width:16px;height:16px;"></i>
      </a>
    </div>

    <!-- Mini mockup del dashboard -->
    <div class="hero-mockup reveal">
      <div class="mockup-bar">
        <div class="mockup-dot" style="background:#ff5f57;"></div>
        <div class="mockup-dot" style="background:#ffbd2e;"></div>
        <div class="mockup-dot" style="background:#28c840;"></div>
        <div class="mockup-url">changarros.com/admin/dashboard</div>
      </div>
      <div class="mockup-body">
        <div class="mockup-sidebar">
          @foreach(['Dashboard','Clientes','Proyectos','Facturas','Tiempo','Marcas'] as $i => $item)
          <div class="mockup-nav-item {{ $i === 0 ? 'active' : '' }}">
            <div class="mockup-nav-dot"></div> {{ $item }}
          </div>
          @endforeach
        </div>
        <div class="mockup-content">
          <div class="mockup-kpi-row">
            <div class="mockup-kpi">
              <div class="mockup-kpi-val" style="color:#a78bfa;">12</div>
              <div class="mockup-kpi-lbl">Proyectos activos</div>
            </div>
            <div class="mockup-kpi">
              <div class="mockup-kpi-val" style="color:#60a5fa;">$48,200</div>
              <div class="mockup-kpi-lbl">Cobrado este mes</div>
            </div>
            <div class="mockup-kpi">
              <div class="mockup-kpi-val" style="color:#34d399;">87%</div>
              <div class="mockup-kpi-lbl">Margen promedio</div>
            </div>
          </div>
          <div class="mockup-table">
            @foreach([['Campaña Q2','Enviada','#a78bfa'],['Identidad Visual','En revisión','#60a5fa'],['Web Corporativa','Finalizado','#34d399']] as $row)
            <div class="mockup-table-row">
              <span>{{ $row[0] }}</span>
              <span class="mockup-badge" style="background:{{ $row[2] }}22;color:{{ $row[2] }};">{{ $row[1] }}</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ FEATURES ══════════════════════════════════════════════ -->
  <section id="features" style="padding-top:60px;">
    <div class="section-label">Características</div>
    <h2 class="section-title">Todo lo que necesitas,<br>nada que no necesitas</h2>
    <p class="section-sub">Cada módulo fue diseñado pensando en los flujos reales de una agencia creativa.</p>

    <div class="features-grid">

      @php
      $features = [
        ['icon'=>'briefcase','color'=>'#a78bfa','bg'=>'rgba(167,139,250,.15)',
         'title'=>'Gestión de Proyectos',
         'desc'=>'Controla cada proyecto con entregas, documentos y aprobaciones. Tu cliente aprueba o solicita cambios desde su propio portal, sin necesidad de WhatsApp.',
         'tag'=>'Portal del cliente incluido','tag_color'=>'rgba(167,139,250,.2)','tag_text'=>'#a78bfa'],

        ['icon'=>'file-text','color'=>'#60a5fa','bg'=>'rgba(96,165,250,.15)',
         'title'=>'Cotizaciones & Facturas',
         'desc'=>'Genera cotizaciones profesionales, conviértelas en facturas y envíalas por correo con PDF adjunto en un solo clic. Con IVA, descuentos y firma.',
         'tag'=>'PDF automático','tag_color'=>'rgba(96,165,250,.2)','tag_text'=>'#60a5fa'],

        ['icon'=>'clock','color'=>'#f472b6','bg'=>'rgba(244,114,182,.15)',
         'title'=>'Control de Tiempo',
         'desc'=>'Timer en vivo para registrar cada hora trabajada. Al final del mes sabes exactamente qué proyectos son rentables y cuáles te están comiendo el tiempo.',
         'tag'=>'Reporte de rentabilidad','tag_color'=>'rgba(244,114,182,.2)','tag_text'=>'#f472b6'],

        ['icon'=>'send','color'=>'#34d399','bg'=>'rgba(52,211,153,.15)',
         'title'=>'Mailing Masivo',
         'desc'=>'Crea campañas de correo con diseño HTML, importa contactos desde CSV y personaliza cada mensaje con variables {nombre}, {empresa}. Todo desde el panel.',
         'tag'=>'Variables personalizadas','tag_color'=>'rgba(52,211,153,.2)','tag_text'=>'#34d399'],

        ['icon'=>'layers','color'=>'#fb923c','bg'=>'rgba(251,146,60,.15)',
         'title'=>'Marcas & Branding',
         'desc'=>'Sube logos, tipografías, paleta de colores y templates por cliente. Comparte el kit completo con un enlace público que puedes activar o desactivar cuando quieras.',
         'tag'=>'Página pública por cliente','tag_color'=>'rgba(251,146,60,.2)','tag_text'=>'#fb923c'],

        ['icon'=>'trending-up','color'=>'#e879f9','bg'=>'rgba(232,121,249,.15)',
         'title'=>'Rentabilidad Real',
         'desc'=>'Dashboard financiero con gráficas de horas por semana, distribución por tipo de actividad y margen de ganancia por proyecto. Toma decisiones con datos.',
         'tag'=>'Gráficas en tiempo real','tag_color'=>'rgba(232,121,249,.2)','tag_text'=>'#e879f9'],
      ];
      @endphp

      @foreach($features as $f)
      <div class="feature-card reveal" style="--card-accent:{{ $f['color'] }}">
        <div class="feature-icon" style="background:{{ $f['bg'] }}; color:{{ $f['color'] }};">
          <i data-lucide="{{ $f['icon'] }}" style="width:24px;height:24px;"></i>
        </div>
        <h3>{{ $f['title'] }}</h3>
        <p>{{ $f['desc'] }}</p>
        <span class="feature-tag" style="background:{{ $f['tag_color'] }};color:{{ $f['tag_text'] }};">
          {{ $f['tag'] }}
        </span>
      </div>
      @endforeach

    </div>
  </section>

  <!-- ══ PARA QUIÉN ═════════════════════════════════════════════ -->
  <section id="quien" style="padding-top:60px;">
    <div class="section-label">¿Para quién?</div>
    <h2 class="section-title">Hecho para quienes<br>crean cosas</h2>

    <div class="quien-grid">
      <div class="quien-card reveal">
        <div class="quien-emoji">🎨</div>
        <h3>Diseñadores Freelance</h3>
        <p>Deja de perder tiempo en hojas de cálculo. Cotiza, factura y controla tu tiempo desde un solo lugar. Cobra lo que vales.</p>
      </div>
      <div class="quien-card reveal">
        <div class="quien-emoji">🏢</div>
        <h3>Estudios Creativos</h3>
        <p>Gestiona a todo tu equipo, cada admin con sus propios clientes. Proyectos, entregas, aprobaciones y rentabilidad sin mezclar cuentas.</p>
      </div>
      <div class="quien-card reveal">
        <div class="quien-emoji">📣</div>
        <h3>Agencias de Marketing</h3>
        <p>Calendarios de publicaciones, mailing masivo, branding por cliente y portal de aprobaciones. Todo lo que necesita una agencia moderna.</p>
      </div>
    </div>
  </section>

  <!-- ══ STATS ══════════════════════════════════════════════════ -->
  <section id="stats" style="padding-top:60px;">
    <div class="stats-wrap reveal">
      @php
      $stats = [
        ['num'=>'8+',  'lbl'=>'Módulos integrados'],
        ['num'=>'100%','lbl'=>'En tu servidor, tus datos'],
        ['num'=>'0',   'lbl'=>'Comisiones por transacción'],
        ['num'=>'∞',   'lbl'=>'Clientes que puedes gestionar'],
      ];
      @endphp
      @foreach($stats as $s)
      <div class="stat-item">
        <div class="stat-num">{{ $s['num'] }}</div>
        <div class="stat-lbl">{{ $s['lbl'] }}</div>
      </div>
      @endforeach
    </div>
  </section>

  <!-- ══ PRICING ════════════════════════════════════════════════ -->
  <section id="pricing">
    <div class="section-label">Precios</div>
    <h2 class="section-title">Sin sorpresas,<br>sin comisiones</h2>
    <p class="section-sub">Paga una vez por plan o por mes. Sin cobros ocultos, sin límites por transacción.</p>

    <div class="pricing-grid">

      <!-- Freelance -->
      <div class="pricing-card reveal">
        <div class="pricing-tier">Freelance</div>
        <div class="pricing-price">
          <span class="pricing-currency">$</span>
          <span class="pricing-amount">499</span>
        </div>
        <div class="pricing-period">/ mes · MXN</div>
        <p class="pricing-desc">Para diseñadores y creativos independientes que quieren trabajar con orden.</p>
        <ul class="pricing-features">
          @foreach(['1 usuario admin','Hasta 10 clientes activos','Proyectos y entregas ilimitados','Cotizaciones y Facturas','Control de tiempo','Portal del cliente'] as $feat)
          <li>
            <i data-lucide="check" style="width:15px;height:15px;" class="check"></i>
            {{ $feat }}
          </li>
          @endforeach
          @foreach(['Múltiples usuarios','Mailing masivo'] as $feat)
          <li style="opacity:.4;">
            <i data-lucide="x" style="width:15px;height:15px;" class="cross"></i>
            {{ $feat }}
          </li>
          @endforeach
        </ul>
        <a href="{{ route('admin.login') }}" class="btn-pricing btn-pricing-outline">Comenzar</a>
      </div>

      <!-- Estudio (featured) -->
      <div class="pricing-card featured reveal">
        <div class="pricing-badge">Más popular</div>
        <div class="pricing-tier">Estudio</div>
        <div class="pricing-price">
          <span class="pricing-currency">$</span>
          <span class="pricing-amount">999</span>
        </div>
        <div class="pricing-period">/ mes · MXN</div>
        <p class="pricing-desc">Para estudios creativos con equipo. Cada admin gestiona sus propios clientes de forma independiente.</p>
        <ul class="pricing-features">
          @foreach(['Hasta 5 usuarios admin','Clientes ilimitados','Todo del plan Freelance','Mailing masivo (hasta 5k contactos)','Módulo de Marcas & Branding','Rentabilidad por proyecto','Soporte prioritario'] as $feat)
          <li>
            <i data-lucide="check" style="width:15px;height:15px;" class="check"></i>
            {{ $feat }}
          </li>
          @endforeach
        </ul>
        <a href="{{ route('admin.login') }}" class="btn-pricing btn-pricing-fill">Comenzar ahora</a>
      </div>

      <!-- Agencia -->
      <div class="pricing-card reveal">
        <div class="pricing-tier">Agencia</div>
        <div class="pricing-price">
          <span class="pricing-currency">$</span>
          <span class="pricing-amount">1,999</span>
        </div>
        <div class="pricing-period">/ mes · MXN</div>
        <p class="pricing-desc">Para agencias con múltiples equipos, alto volumen y necesidades personalizadas.</p>
        <ul class="pricing-features">
          @foreach(['Usuarios admin ilimitados','Todo del plan Estudio','Mailing sin límite de contactos','Social Media Calendar avanzado','Integración Meta Tokens','Personalización de marca','Instalación y onboarding incluidos'] as $feat)
          <li>
            <i data-lucide="check" style="width:15px;height:15px;" class="check"></i>
            {{ $feat }}
          </li>
          @endforeach
        </ul>
        <a href="mailto:hola@changarros.com" class="btn-pricing btn-pricing-outline">Hablar con ventas</a>
      </div>

    </div>
  </section>

  <!-- ══ CTA FINAL ══════════════════════════════════════════════ -->
  <section id="cta-final">
    <div class="cta-box reveal">
      <div class="section-label" style="text-align:center;">¿Listo?</div>
      <h2>Empieza a gestionar<br>como los grandes</h2>
      <p>Sin instalaciones complicadas, sin tarjeta de crédito para probar.<br>Tu panel listo en minutos.</p>
      <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
        <a href="{{ route('admin.login') }}" class="btn-primary-gl">
          <i data-lucide="arrow-right" style="width:18px;height:18px;"></i>
          Entrar al panel
        </a>
        <a href="mailto:hola@changarros.com" class="btn-ghost-gl">
          Contactar ventas
        </a>
      </div>
    </div>
  </section>

  <!-- ══ FOOTER ═════════════════════════════════════════════════ -->
  <footer>
    <div class="footer-inner">
      <div class="footer-logo">CHANGARROS<span>.</span></div>
      <div class="footer-links">
        <a href="#features">Características</a>
        <a href="#pricing">Precios</a>
        <a href="mailto:hola@changarros.com">Contacto</a>
        <a href="{{ route('admin.login') }}">Panel</a>
      </div>
      <div>© {{ date('Y') }} CHANGARROS · Todos los derechos reservados</div>
    </div>
  </footer>

  <script>
    lucide.createIcons();

    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('visible'); }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Nav scroll effect
    window.addEventListener('scroll', () => {
      document.querySelector('nav').style.background =
        window.scrollY > 40 ? 'rgba(5,8,22,0.85)' : 'rgba(5,8,22,0.6)';
    });
  </script>
</body>
</html>
