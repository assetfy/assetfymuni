@extends('layouts.landing')

@section('section')
    {{ __('Permisos por empresa') }}
@endsection

@section('content')
    @livewire('permisosRoles.permisos-por-tipo')
@endsection
