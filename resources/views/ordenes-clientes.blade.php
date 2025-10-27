@extends('layouts.landing')

@section('section')
    {{ __('Ordenes de Clientes') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.ordenes-clientes')
@endsection
