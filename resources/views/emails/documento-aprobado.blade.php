@extends('emails.layout')
@section('content')
<span class="badge badge-success">Documento aprobado</span>
<h2>El cliente aprobó y selló un documento</h2>
<p>El cliente ha aprobado el documento. Ha sido sellado digitalmente con fecha y hora de aprobación.</p>
<div class="details-box">
  <p><strong>Documento:</strong> {{ $documento->nombre }}</p>
  <p><strong>Proyecto:</strong> {{ $documento->proyecto->nombre }}</p>
  <p><strong>Cliente:</strong> {{ $documento->proyecto->cliente->nombre_empresa }}</p>
  @if($documento->sellado_at)
  <p><strong>Sellado el:</strong> {{ $documento->sellado_at->format('d/m/Y H:i') }}</p>
  @endif
</div>
<a href="{{ route('admin.proyectos.show', $documento->proyecto_id) }}" class="btn btn-success">Ver proyecto</a>
@endsection
