@extends('layouts.landing')

@section('section')
    {{ __('Estado General') }}
@endsection

@section('content')
    @livewire('general.estado-general')
@endsection