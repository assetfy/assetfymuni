@extends('layouts.landing')

@section('section')
    {{ __('dashboard empresa') }}
@endsection

@section('content')
    @livewire('empresas.dashboard-empresa')
@endsection


