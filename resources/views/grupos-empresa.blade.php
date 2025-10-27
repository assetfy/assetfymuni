@extends('layouts.landing')

@section('section')
    {{ __('Grupos de Empresa') }}
@endsection

@section('content')
    @livewire('grupos.grupos-empresa')
@endsection
