@extends('layouts.landing')

@section('section')
    {{ __('Mis ordenes Pendientes') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.ordenes-gestora-pendientes')
@endsection
