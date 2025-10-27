@extends('layouts.landing')

@section('section')
    {{ __('Reportes') }}
@endsection

@section('content')
    @livewire('reportes')
@endsection
