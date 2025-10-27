@extends('layouts.landing')

@section('section')
    {{ __('Autorizacion') }}
@endsection

@section('content')
    @livewire('empresas.autorizacion-actividad')
@endsection
