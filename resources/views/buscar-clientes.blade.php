@extends('layouts.landing')

@section('section')
    {{ __('Buscar Cliente') }}
@endsection

@section('content')
    @livewire('empresas.buscar-clientes')
@endsection
