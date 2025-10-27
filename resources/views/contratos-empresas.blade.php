@extends('layouts.landing')

@section('section')
    {{ __('Contratos') }}
@endsection

@section('content')
    @livewire('empresas.contratos.contratos-empresas')
@endsection
