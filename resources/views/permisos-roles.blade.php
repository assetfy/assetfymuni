@extends('layouts.landing')

@section('section')
    {{ __('Permisos Roles') }}
@endsection

@section('content')
    @livewire('permisosRoles.permisos-roles')
@endsection