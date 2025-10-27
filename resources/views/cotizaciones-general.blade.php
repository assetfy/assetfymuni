@extends('layouts.landing')

@section('section')
    {{ __('Cotizaciones') }}
@endsection

@section('content')
     @livewire('servicios.cotizaciones.cotizaciones-general')
@endsection
