@extends('layouts.landing')

@section('section')
    {{ __('Roles') }}
@endsection

@section('content')
    @livewire('roles.roles')
@endsection
