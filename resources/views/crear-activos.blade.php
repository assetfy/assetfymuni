@extends('layouts.landing')

@section('section')
    {{ __('crear-activos') }}
@endsection

@section('content')
    @livewire('activos.crear-activos')
@endsection