@extends('layouts.landing')

@section('section')
    {{ __('Formulario de Empresa') }}
@endsection

@section('content')
    @livewire('empresas.empresa-formulario-registro')
@endsection