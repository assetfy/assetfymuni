@extends('layouts.landing')

@section('section')
    {{ __('Control Vista Detalle') }}
@endsection

@section('content')
    @livewire('controles.controles-vista-detalle')
@endsection