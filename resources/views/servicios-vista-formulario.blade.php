@extends('layouts.landing')

@section('section')
    {{ __('Servicios') }}
@endsection

@section('content')
    @livewire('servicios.servicios-vista-formulario')
@endsection