@extends('layouts.landing')

@section('section')
    {{ __('Usuarios') }}
@endsection

@section('content')
    @livewire('empresas.EmpresasUsuarios.usuarios')
@endsection