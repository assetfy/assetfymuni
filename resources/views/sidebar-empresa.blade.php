@extends('layouts.landing')

@section('section')
    {{ __('Sidebar') }}
@endsection

@section('content')
    @livewire('empresa.sidebar-empresa')
@endsection