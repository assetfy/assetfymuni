@extends('layouts.landing')

@section('section')
    {{ __('Sidebar') }}
@endsection

@section('content')
    @livewire('usuarios.sidebar-usuario')
@endsection