@extends('layouts.landing')

@section('section')
    {{ __('Agenda') }}
@endsection

@section('content')
    @livewire('usuarios.agenda')
@endsection