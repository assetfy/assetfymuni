@extends('layouts.landing')

@section('section')
    {{ __('Tipos de Usuarios') }}
@endsection

@section('content')
    @livewire('usuarios.usuarios-servicios')
@endsection
