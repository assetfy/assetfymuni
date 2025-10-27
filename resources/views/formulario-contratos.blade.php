@extends('layouts.landing')

@section('section')
    {{ __('Formulario Contrato') }}
@endsection

@section('content')
    @livewire('empresas.contratos.formulario-contratos')
@endsection
