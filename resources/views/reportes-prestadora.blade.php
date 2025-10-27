@extends('layouts.landing')

@section('section')
    {{ __('Reportes') }}
@endsection

@section('content')
    @livewire('empresas.reportes-prestadora')
@endsection