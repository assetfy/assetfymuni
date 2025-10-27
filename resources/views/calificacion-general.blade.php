@extends('layouts.landing')

@section('section')
    {{ __('Card') }}
@endsection

@section('content')
     @livewire('calificaciones.calificacion-general')
@endsection
