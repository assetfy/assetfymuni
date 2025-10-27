@extends('layouts.landing')

@section('section')
    {{ __('notificaciones') }}
@endsection

@section('content')
    @livewire('usuarios.notificaciones')
@endsection