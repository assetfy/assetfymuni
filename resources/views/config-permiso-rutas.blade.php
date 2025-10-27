@extends('layouts.landing')

@section('section')
    {{ __('Permisos rutas configuracion') }}
@endsection

@section('content')
    @livewire('permisos-roles.config-permiso-rutas')
@endsection
