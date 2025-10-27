@extends('layouts.landing')

@section('section')
    {{ __('dashboard-activos') }}
@endsection

@section('content')
    @livewire('activos.dashboard-activos', [
        'id_tipo' => request()->route('id_tipo'),
        'id_ubicacion' => request()->route('id_ubicacion'),
        'hideCreateButton' => request()->has('hideCreateButton') ? true : false,
        'hideFilters' => request()->has('hideFilters') ? true : false,
        'showQrButton' => request()->has('showQrButton') ? true : false
    ])
@endsection
