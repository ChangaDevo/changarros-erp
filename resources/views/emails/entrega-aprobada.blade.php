@extends('emails.layout')
@section('content')
<span class="badge badge-success">Entrega aprobada</span>
<h2>El cliente aprobó una entrega</h2>
<p>Buenas noticias. El cliente ha aprobado la siguiente entrega del proyecto.</p>
<div class="details-box">
  <p><strong>Entrega:</strong> {{ $entrega->titulo }}</p>
  <p><strong>Proyecto:</strong> {{ $entrega->proyecto->nombre }}</p>
  <p><strong>Cliente:</strong> {{ $entrega->proyecto->cliente->nombre_empresa }}</p>
  @if($entrega->notas_cliente)
  <p><strong>Nota del cliente:</strong> {{ $entrega->notas_cliente }}</p>
  @endif
</div>
<a href="{{ route('admin.proyectos.show', $entrega->proyecto_id) }}" class="btn btn-success">Ver proyecto</a>
@endsection
