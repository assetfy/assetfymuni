@extends('layouts.landing')

@section('section')
    {{ __('Carga masiva Usuario') }}
@endsection

@section('content')
    @livewire('empresas.usuarios-empresa-carga-masiva')
@endsection
