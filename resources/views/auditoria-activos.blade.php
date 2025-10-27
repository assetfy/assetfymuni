@extends('layouts.landing')

@section('section')
{{ __('Auditoría de Activos') }}
@endsection

@section('content')
<div class="text-first" style="padding-top: 20px; align-items: right">
    <a href="{{ session('previous_url', url()->previous()) }}" class="btn-3 text-white rounded-lg">
        Volver
    </a>
</div>
<livewire:ubicaciones.auditoria-activos :id_activo="$id_activo" />
@endsection