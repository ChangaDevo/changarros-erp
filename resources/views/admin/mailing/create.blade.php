@extends('admin.layouts.app')
@section('title', 'Nueva Campaña')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <h4 class="fw-bold mb-1">Nueva campaña de mailing</h4>
    <p class="text-muted mb-0">Crea y diseña tu campaña de correo masivo</p>
  </div>
  <a href="{{ route('admin.mailing.index') }}" class="btn btn-outline-secondary">
    <i data-lucide="arrow-left" style="width:16px;height:16px;" class="me-1"></i> Volver
  </a>
</div>

<form action="{{ route('admin.mailing.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  @include('admin.mailing._form', ['campana' => new \App\Models\CampanaEmail()])
</form>
@endsection
