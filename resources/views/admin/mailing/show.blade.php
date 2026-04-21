@extends('admin.layouts.app')
@section('title', $mailing->titulo)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $mailing->titulo }}</h4>
    <p class="text-muted mb-0">{{ $mailing->cliente->nombre_empresa ?? '—' }} · {{ $mailing->asunto }}</p>
  </div>
  <div class="d-flex gap-2">
    @if($mailing->estado !== 'enviada')
      <a href="{{ route('admin.mailing.edit', $mailing) }}" class="btn btn-outline-secondary">
        <i data-lucide="edit-2" style="width:15px;height:15px;" class="me-1"></i> Editar
      </a>
    @endif
    <a href="{{ route('admin.mailing.index') }}" class="btn btn-outline-secondary">
      <i data-lucide="arrow-left" style="width:15px;height:15px;" class="me-1"></i> Volver
    </a>
  </div>
</div>

<div class="row g-4">

  {{-- Columna izquierda: stats + envío --}}
  <div class="col-lg-4">

    {{-- Estado y stats --}}
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="card-title fw-semibold mb-0">Estado</h6>
          <span class="badge bg-{{ $mailing->estado_badge }} fs-6">{{ ucfirst($mailing->estado) }}</span>
        </div>

        {{-- Progress bar --}}
        <div class="mb-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Progreso de envío</span>
            <span>{{ $mailing->porcentaje_enviado }}%</span>
          </div>
          <div class="progress" style="height:10px;">
            <div class="progress-bar bg-{{ $mailing->estado_badge }}"
                 style="width:{{ $mailing->porcentaje_enviado }}%"></div>
          </div>
        </div>

        <div class="row g-2 text-center">
          <div class="col-4">
            <div class="p-2 rounded bg-light">
              <div class="fw-bold fs-5">{{ $mailing->total_contactos }}</div>
              <div class="text-muted" style="font-size:11px;">Total</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded bg-success bg-opacity-10">
              <div class="fw-bold fs-5 text-success">{{ $mailing->total_enviados }}</div>
              <div class="text-muted" style="font-size:11px;">Enviados</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded bg-danger bg-opacity-10">
              <div class="fw-bold fs-5 text-danger">{{ $mailing->total_errores }}</div>
              <div class="text-muted" style="font-size:11px;">Errores</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Info remitente --}}
    <div class="card mb-4">
      <div class="card-body">
        <h6 class="card-title fw-semibold mb-3">Detalles</h6>
        <div class="mb-2">
          <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Remitente</p>
          <p class="mb-0 small">{{ $mailing->remitente_nombre }}</p>
          <p class="mb-0 small text-muted">{{ $mailing->remitente_email }}</p>
        </div>
        <div class="mb-2 mt-3">
          <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Asunto</p>
          <p class="mb-0 small">{{ $mailing->asunto }}</p>
        </div>
        <div class="mt-3">
          <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Creada</p>
          <p class="mb-0 small">{{ $mailing->created_at->format('d/m/Y H:i') }}</p>
        </div>
        @if($mailing->enviado_at)
        <div class="mt-3">
          <p class="text-muted mb-0" style="font-size:11px;text-transform:uppercase;font-weight:600;">Enviada</p>
          <p class="mb-0 small">{{ $mailing->enviado_at->format('d/m/Y H:i') }}</p>
        </div>
        @endif
      </div>
    </div>

    {{-- Botón de envío --}}
    @if($mailing->estado !== 'enviada' && $mailing->total_contactos > 0)
      <div class="card border-primary">
        <div class="card-body text-center">
          <i data-lucide="send" class="text-primary mb-2" style="width:32px;height:32px;"></i>
          <h6 class="fw-semibold mb-1">¿Listo para enviar?</h6>
          <p class="text-muted small mb-3">
            Se enviarán <strong>{{ $mailing->total_contactos - $mailing->total_enviados }}</strong> correos
            desde <strong>{{ $mailing->remitente_email }}</strong>
          </p>
          <form action="{{ route('admin.mailing.enviar', $mailing) }}" method="POST"
                onsubmit="return confirm('¿Confirmas el envío masivo a {{ $mailing->total_contactos }} contactos? Esta acción no se puede deshacer.')">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
              <i data-lucide="send" style="width:15px;height:15px;" class="me-1"></i>
              Enviar campaña
            </button>
          </form>
        </div>
      </div>
    @elseif($mailing->estado === 'enviada')
      <div class="alert alert-success d-flex align-items-center gap-2">
        <i data-lucide="check-circle" style="width:20px;height:20px;flex-shrink:0;"></i>
        Campaña enviada el {{ $mailing->enviado_at?->format('d/m/Y H:i') }}
      </div>
    @endif

  </div>

  {{-- Columna derecha: preview + contactos --}}
  <div class="col-lg-8">

    {{-- Preview del email --}}
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
          <i data-lucide="eye" style="width:15px;height:15px;" class="me-1"></i>
          Vista previa del correo
        </h6>
        <div class="d-flex gap-2 align-items-center">
          <span class="text-muted small">Mostrando con datos del primer contacto</span>
          <button class="btn btn-sm btn-outline-secondary" onclick="recargarPreview()">
            <i data-lucide="refresh-cw" style="width:13px;height:13px;"></i>
          </button>
          <a href="{{ route('admin.mailing.preview', $mailing) }}" target="_blank" class="btn btn-sm btn-outline-primary">
            <i data-lucide="external-link" style="width:13px;height:13px;" class="me-1"></i>Pantalla completa
          </a>
        </div>
      </div>
      <div class="card-body p-0">
        <div style="height:480px;border-radius:0 0 8px 8px;overflow:hidden;">
          <iframe id="previewIframe"
                  src="{{ route('admin.mailing.preview', $mailing) }}"
                  style="width:100%;height:100%;border:none;"></iframe>
        </div>
      </div>
    </div>

    {{-- Tabla de contactos --}}
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
          <i data-lucide="users" style="width:15px;height:15px;" class="me-1"></i>
          Contactos ({{ $mailing->total_contactos }})
        </h6>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nombre</th>
              <th>Email</th>
              <th>Empresa</th>
              <th>Estado</th>
              <th>Enviado</th>
            </tr>
          </thead>
          <tbody>
            @forelse($contactos as $contacto)
            <tr>
              <td>{{ $contacto->nombre }} {{ $contacto->apellido }}</td>
              <td class="small text-muted">{{ $contacto->email }}</td>
              <td class="small">{{ $contacto->empresa ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $contacto->estado === 'enviado' ? 'success' : ($contacto->estado === 'error' ? 'danger' : 'secondary') }}">
                  {{ ucfirst($contacto->estado) }}
                </span>
              </td>
              <td class="small text-muted">
                {{ $contacto->enviado_at?->format('d/m/Y H:i') ?? '—' }}
                @if($contacto->error_mensaje)
                  <span title="{{ $contacto->error_mensaje }}" class="text-danger ms-1">
                    <i data-lucide="alert-circle" style="width:13px;height:13px;"></i>
                  </span>
                @endif
              </td>
            </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-3">Sin contactos</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($contactos->hasPages())
        <div class="p-3">{{ $contactos->links() }}</div>
      @endif
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
function recargarPreview() {
  document.getElementById('previewIframe').src = document.getElementById('previewIframe').src;
}
</script>
@endpush
