@extends('layouts.landing')

@section('section')
    {{ __('Permisos') }}
@endsection

@section('content')
    @livewire('permisosRoles.permisos')
@endsection