@extends('layouts.landing')

@section('section')
    {{ __('Solicitudes Servicios') }}
@endsection

@section('content')
    @livewire('servicios.solicitudes.solicitudes-cerradas')
@endsection