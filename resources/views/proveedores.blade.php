@extends('layouts.landing')

@section('section')
    {{ __('Proveedores') }}
@endsection

@section('content')
    @livewire('servicios.proveedores')
@endsection
