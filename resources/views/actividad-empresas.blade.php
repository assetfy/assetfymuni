@extends('layouts.landing')

@section('section')
    {{ __('actividad-empresas') }}
@endsection

@section('content')
        @livewire('actividad.actividad-empresas')
@endsection
