@extends('admin.layouts.app')
@section('title', 'Nueva Factura / Recibo')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <h4 class="mb-0">Nuevo Documento</h4>
  <a href="{{ route('admin.facturas.index') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" style="width:15px;height:15px;" class="me-1"></i>Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

@php $factura = null; $existingItems = []; @endphp
@include('admin.facturas._form', [
  'action' => route('admin.facturas.store'),
  'method' => null,
])
@endsection
