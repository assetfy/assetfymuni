@extends('layouts.landing')

@section('section')
    {{ __('TableHeaders') }}
@endsection

@section('content')
    @livewire('table-headers')
@endsection