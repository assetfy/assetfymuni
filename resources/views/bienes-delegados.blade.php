@extends('layouts.landing')

@section('section')
    {{ __('Bienes Delegados') }}
@endsection

@section('content')
    @livewire('activos.bienes.bienes-delegados')
@endsection
