@extends('layouts.landing')

@section('section')
    {{ __('Mis Proveedores Favoritos') }}
@endsection

@section('content')
    @livewire('servicios.mis-provedores-favoritos')
@endsection