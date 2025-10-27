@extends('layouts.landing')

@section('section')
    {{ __('Controles') }}
@endsection

@section('content')
        @livewire('controles.controles')
@endsection