@extends('layouts.landing')

@section('section')
    {{ __('Solicitudes Servicios') }}
@endsection

@section('content')
    @livewire('servicios.solicitud-servicios')
@endsection