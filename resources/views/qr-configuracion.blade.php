@extends('layouts.landing')

@section('section')
    {{ __('Configuracion') }}
@endsection

@section('content')
    @livewire('qr-configuracion')
@endsection
