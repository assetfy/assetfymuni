@extends('layouts.landing')

@section('section')
    {{ __('actividades-extra') }}
@endsection

@section('content')
    @livewire('actividad.actividades-extra')
@endsection
