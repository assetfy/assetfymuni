@extends('layouts.landing')

@section('section')
    {{ __('dashboard') }}
@endsection

@section('content')
    @livewire('usuarios.dashboard-usuario')
@endsection


