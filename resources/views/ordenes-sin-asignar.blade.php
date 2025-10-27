@extends('layouts.landing')

@section('section')
    {{ __('Ordenes de trabajo') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.ordenes-sin-asignar')
@endsection
