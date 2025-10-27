@extends('layouts.landing')

@section('section')
    {{ __('Activos Controles') }}
@endsection

@section('content')
    @livewire('controles.activoscontroles.activos-controles')
@endsection