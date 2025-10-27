@extends('layouts.landing')

@section('section')
    {{ __('Bienes Terceros') }}
@endsection

@section('content')
    @livewire('activos.bienes.bienes-terceros')
@endsection
