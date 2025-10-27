@extends('layouts.landing')

@section('section')
    {{ __('Estado ALTAS') }}
@endsection

@section('content')
    @livewire('altas.estados-altas')
@endsection