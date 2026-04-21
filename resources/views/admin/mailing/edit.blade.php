@extends('admin.layouts.app')
@section('title', 'Editar Campaña')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Editar campaña</h4>
    <p class="text-muted mb-0">{{ $mailing->titulo }}</p>
  </div>
  <a href="{{ route('admin.mailing.show', $mailing) }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i> Volver
  </a>
</div>

<form action="{{ route('admin.mailing.update', $mailing) }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')
  @include('admin.mailing._form', ['campana' => $mailing])
</form>
@endsection
