@extends('layouts.landing')

@section('section')
    {{ __('Tipos') }}
@endsection

@section('content')
    @livewire('tipos.tipos')
@endsection