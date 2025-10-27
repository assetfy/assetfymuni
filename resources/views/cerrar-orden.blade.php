@extends('layouts.landing')

@section('section')
    {{ __('Órdenes · Cerrar') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.cerrar-orden', ['id_orden' => $id])
@endsection
