@extends('layouts.landing')

@section('section')
    {{ __('Vistas de Aperturas') }}
@endsection
@section('content')
    @livewire('ubicaciones.vistas-aperturas')
@endsection