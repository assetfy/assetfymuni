@extends('layouts.landing')

@section('section')
    {{ __('Empresas') }}
@endsection

@section('content')
    @livewire('perfil.empresas.empresa')
@endsection