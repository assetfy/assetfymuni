@extends('layouts.landing')

@section('section')
    {{ __('Vistas de Clientes') }}
@endsection
@section('content')
    @livewire('empresas.vista-clientes')
@endsection
