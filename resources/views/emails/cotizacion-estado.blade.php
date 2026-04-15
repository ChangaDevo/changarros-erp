@extends('emails.layout')
@section('content')
@if($estado === 'aprobada')
  <span class="badge badge-success">Cotización aprobada</span>
  <h2>¡El cliente aprobó la cotización!</h2>
  <p>Excelente noticia. El cliente ha aceptado la cotización. Puedes proceder a crear el proyecto o avanzar con el siguiente paso.</p>
@else
  <span class="badge badge-danger">Cotización rechazada</span>
  <h2>El cliente rechazó la cotización</h2>
  <p>El cliente ha revisado la cotización y ha decidido no proceder en este momento.</p>
@endif
<div class="details-box">
  <p><strong>Cotización:</strong> {{ $cotizacion->nombre }}</p>
  <p><strong>Cliente:</strong> {{ $cotizacion->cliente->nombre_empresa }}</p>
  <p><strong>Total:</strong> ${{ number_format($cotizacion->total_con_iva, 2) }}</p>
  @if($cotizacion->razon_rechazo)
  <p><strong>Motivo:</strong> {{ $cotizacion->razon_rechazo }}</p>
  @endif
</div>
<a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}"
   class="{{ $estado === 'aprobada' ? 'btn btn-success' : 'btn' }}">Ver cotización</a>
@endsection
