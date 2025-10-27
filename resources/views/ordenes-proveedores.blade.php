@extends('layouts.landing')

@section('section')
    {{ __('Ordenes Proveedores') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-proveedores.ordenes-proveedores')
@endsection
