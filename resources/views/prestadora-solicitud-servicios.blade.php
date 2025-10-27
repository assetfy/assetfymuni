@extends('layouts.landing')

@section('section')
    {{ __('Prestadora Servicios Solicitudes') }}
@endsection

@section('content')
    @livewire('servicios.prestadora.prestadora-solicitud-servicios')
@endsection
