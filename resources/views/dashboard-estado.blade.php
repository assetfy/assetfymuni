@extends('layouts.landing')

@section('section')
    {{ __('dashboard estado') }}
@endsection

@section('content')
    @livewire('estado.dashboard-estado')
@endsection


