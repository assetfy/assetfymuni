@extends('layouts.landing')

@section('section')
    {{ __('Atributos') }}
@endsection

@section('content')
    @livewire('atributos.atributos')
@endsection
