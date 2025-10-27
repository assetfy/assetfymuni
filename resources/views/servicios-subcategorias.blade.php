@extends('layouts.landing')

@section('section')
    {{ __('Servicios Subcategorias') }}
@endsection

@section('content')
    @livewire('servicios.subcategorias.servicios-subcategorias')
@endsection