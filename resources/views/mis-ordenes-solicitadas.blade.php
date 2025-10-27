@extends('layouts.landing')

@section('section')
    {{ __('Mis ordenes Solicitadas') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.mis-ordenes-solicitadas')
@endsection
