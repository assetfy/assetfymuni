@extends('layouts.landing')

@section('section')
    {{ __('estado') }}
@endsection

@section('content')
    @livewire('empresas.estado')
@endsection