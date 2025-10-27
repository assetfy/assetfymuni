@extends('layouts.landing')

@section('section')
    {{ __('Bienes Pendientes') }}
@endsection

@section('content')
    @livewire('activos.bienes.bienes-pendientes')
@endsection
