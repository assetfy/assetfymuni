@extends('layouts.landing')

@section('section')
    {{ __('dashboard Prestadora') }}
@endsection

@section('content')
    @livewire('empresas.dashboard-prestadora')
@endsection
