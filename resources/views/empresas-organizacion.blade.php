@extends('layouts.landing')

@section('section')
    {{ __('Organizacion') }}
@endsection

@section('content')
    @livewire('empresas.empresas-organizacion')
@endsection
