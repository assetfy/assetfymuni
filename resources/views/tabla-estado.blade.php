@extends('layouts.landing')

@section('section')
    {{ __('Tablas') }}
@endsection

@section('content')
     @livewire('estado.tabla-estado')
@endsection