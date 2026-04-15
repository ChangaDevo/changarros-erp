@extends('emails.layout')
@section('content')
<span class="badge badge-info">Nueva entrega</span>
<h2>Tienes una nueva entrega para revisar</h2>
<p>Hola, el equipo ha subido una nueva entrega en tu proyecto. Por favor revísala y apruébala o solicita cambios desde tu portal.</p>
<div class="details-box">
  <p><strong>Entrega:</strong> {{ $entrega->titulo }}</p>
  <p><strong>Proyecto:</strong> {{ $entrega->proyecto->nombre }}</p>
  @if($entrega->descripcion)
  <p><strong>Descripción:</strong> {{ $entrega->descripcion }}</p>
  @endif
  @if($entrega->fecha_entrega)
  <p><strong>Fecha:</strong> {{ $entrega->fecha_entrega->format('d/m/Y') }}</p>
  @endif
</div>
<a href="{{ route('portal.proyectos.show', $entrega->proyecto_id) }}" class="btn">Ver entrega en el portal</a>
@endsection
