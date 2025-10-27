@extends('layouts.landing')

@section('section')
    {{ __('Ordenes') }}
@endsection

@section('content')
    @livewire('servicios.ordenes-de-trabajo.ordenes')
@endsection
