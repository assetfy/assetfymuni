@extends('layouts.landing')

@section('section')
    {{ __('actividad-empresa-vista') }}
@endsection

@section('content')
        @livewire('actividad.actividad-empresa-vista')
@endsection
