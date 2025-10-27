@extends('layouts.landing')

@section('section')
    {{ __('Tablas') }}
@endsection

@section('content')
     @livewire('menus.tablas')
@endsection