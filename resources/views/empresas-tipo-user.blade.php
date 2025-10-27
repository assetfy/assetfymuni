@extends('layouts.landing')

@section('section')
    {{ __('Tipo User') }}
@endsection

@section('content')
    @livewire('empresas.EmpresasUsuarios.empresas-tipo-user')
@endsection