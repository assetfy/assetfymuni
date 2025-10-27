@extends('layouts.landing')

@section('section')
    {{ __('Nuevos Atributos Activos') }}
@endsection

@section('content')
    @livewire('atributos.create-nuevo-atributos-activos')
@endsection
