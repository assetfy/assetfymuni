@extends('layouts.landing')

@section('section')
    {{ __('Revision') }}
@endsection

@section('content')
    @livewire('empresas.empresas-vista')
@endsection