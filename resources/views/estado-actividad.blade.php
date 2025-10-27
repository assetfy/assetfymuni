@extends('layouts.landing')

@section('section')
    {{ __('Estado de Actividad') }}
@endsection

@section('content')
    @livewire('actividad.estado-actividad')
@endsection