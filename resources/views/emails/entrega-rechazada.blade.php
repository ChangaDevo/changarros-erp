@extends('emails.layout')
@section('content')
<span class="badge badge-warning">Cambios solicitados</span>
<h2>El cliente solicitó cambios en una entrega</h2>
<p>El cliente ha revisado la entrega y solicita ajustes. Por favor revisa las notas y atiende los cambios.</p>
<div class="details-box">
  <p><strong>Entrega:</strong> {{ $entrega->titulo }}</p>
  <p><strong>Proyecto:</strong> {{ $entrega->proyecto->nombre }}</p>
  <p><strong>Cliente:</strong> {{ $entrega->proyecto->cliente->nombre_empresa }}</p>
  @if($entrega->notas_cliente)
  <p><strong>Cambios solicitados:</strong> {{ $entrega->notas_cliente }}</p>
  @endif
</div>
<a href="{{ route('admin.proyectos.show', $entrega->proyecto_id) }}" class="btn btn-warning">Ver proyecto</a>
@endsection
