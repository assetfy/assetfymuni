@extends('layouts.landing')

@section('section')
    {{ __('Ubicaciones Sin Bienes') }}
@endsection

@section('content')
    @livewire('ubicaciones.ubicaciones-sin-bienes')
@endsection