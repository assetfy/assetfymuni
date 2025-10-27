@extends('layouts.landing')

@section('section')
    {{ __('Servicios') }}
@endsection

@section('content')
    @livewire('servicios.prestadora.servicios-realizar-servicios',compact('servicio'))
@endsection
