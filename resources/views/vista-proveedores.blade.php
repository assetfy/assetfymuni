@extends('layouts.landing')

@section('section')
    {{ __('Vistas de Proveedores') }}
@endsection
@section('content')
    @livewire('servicios.vista-proveedores')
@endsection
