@extends('layouts.landing')

@section('section')
    {{ __('rutas') }}
@endsection

@section('content')
    @livewire('permisosRoles.rutas')
@endsection
