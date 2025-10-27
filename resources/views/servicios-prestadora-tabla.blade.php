@extends('layouts.landing')

@section('section')
    {{ __('Tabla de Servicios') }}
@endsection

@section('content')
    @livewire('estado.servicios-prestadora-tabla')
@endsection