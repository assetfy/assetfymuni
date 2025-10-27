@extends('layouts.landing')

@section('section')
    {{ __('Activos') }}
@endsection

@section('content')
    @livewire('activos.activos')
@endsection