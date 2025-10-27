@extends('layouts.landing')

@section('section')
    {{ __('Mis ordenes') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.mis-ordenes')
@endsection
