@extends('layouts.landing')

@section('section')
    {{ __('Control Detalle') }}
@endsection

@section('content')
    @livewire('controles.activoscontroles.control-detalle')
@endsection
