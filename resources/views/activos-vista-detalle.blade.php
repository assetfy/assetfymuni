@extends('layouts.landing')

@section('section')
    {{ __('Activos Vista Detalle') }}
@endsection

@section('content')
    @livewire('activos.activos-vista-detalle')
@endsection