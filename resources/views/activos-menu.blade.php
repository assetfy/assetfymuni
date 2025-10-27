@extends('layouts.landing')

@section('section')
    {{ __('Activos Menus') }}
@endsection

@section('content')
    @livewire('menus.activos-menu')
@endsection