@extends('layouts.landing')

@section('section')
    {{ __('Configuracion General') }}
@endsection

@section('content')
    @livewire('configuraciones.configuracion-general')
@endsection
