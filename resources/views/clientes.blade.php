@extends('layouts.landing')

@section('section')
    {{ __('Clientes') }}
@endsection

@section('content')
    @livewire('empresas.clientes')
@endsection
