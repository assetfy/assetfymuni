@extends('layouts.landing')

@section('section')
{{ __('Activos Servicios Subcategorias') }}
@endsection

@section('content')
<div class="text-first" style="padding-top: 20px; align-items: right">
    <a href="{{ session('previous_url', url()->previous()) }}" class="btn-3 text-white rounded-lg">
        Volver
    </a>
</div>
<livewire:servicios.activos.servicios-activos :id_activo="$id_activo" />
@endsection