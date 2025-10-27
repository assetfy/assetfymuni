@extends('layouts.landing')

@section('section')
    {{ __('Servicios Actividades Economicas') }}
@endsection

@section('content')
    @livewire('servicios.actividadeseconomicas.servicios-actividades-economicas')
@endsection