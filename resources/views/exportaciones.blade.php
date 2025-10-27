@extends('layouts.landing')

@section('section')
    {{ __('Exportaciones') }}
@endsection

@section('content')
    @livewire('exportaciones')
@endsection
