@extends('admin.layouts.app')
@section('title', 'Editar ' . $factura->folio)

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <h4 class="mb-0">Editar {{ $factura->folio }}</h4>
  <a href="{{ route('admin.facturas.show', $factura) }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" style="width:15px;height:15px;" class="me-1"></i>Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

@php $existingItems = $factura->items->toArray(); @endphp
@include('admin.facturas._form', [
  'action' => route('admin.facturas.update', $factura),
  'method' => 'PUT',
])
@endsection
