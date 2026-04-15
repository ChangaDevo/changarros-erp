@extends('emails.layout')
@section('content')
<span class="badge badge-info">Nuevo documento</span>
<h2>Tienes un documento para revisar</h2>
<p>Se ha compartido un documento contigo en el portal. Por favor revísalo y, si está listo, puedes aprobarlo y sellarlo digitalmente.</p>
<div class="details-box">
  <p><strong>Documento:</strong> {{ $documento->nombre }}</p>
  <p><strong>Proyecto:</strong> {{ $documento->proyecto->nombre }}</p>
  <p><strong>Tipo:</strong> {{ ucfirst($documento->tipo) }}</p>
</div>
<a href="{{ route('portal.proyectos.show', $documento->proyecto_id) }}" class="btn">Revisar en el portal</a>
@endsection
