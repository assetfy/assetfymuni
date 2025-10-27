@extends('layouts.landing')

@section('section')
    {{ __('Ubicaciones Delegadas') }}
@endsection
@section('content')
    @livewire('ubicaciones.ubicaciones-delegadas')
@endsection
