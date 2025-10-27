@extends('layouts.landing')

@section('section')
    {{ __('Ubicaciones') }}
@endsection

@section('content')
    @livewire('ubicaciones.ubicaciones-general')
@endsection