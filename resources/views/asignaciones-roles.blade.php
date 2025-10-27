@extends('layouts.landing')

@section('section')
    {{ __('Asginaciones Roles') }}
@endsection

@section('content')
    @livewire('roles.asignaciones-roles')
@endsection
