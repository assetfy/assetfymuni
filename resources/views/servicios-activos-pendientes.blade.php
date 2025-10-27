@extends('layouts.landing')

@section('section')
    {{ __('Servicios Pendientes para Activos') }}
@endsection

@section('content')
    @livewire('servicios.prestadora.servicios-activos-pendientes')
@endsection