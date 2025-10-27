@extends('layouts.landing')

@section('section')
    {{ __('Bienes Empresa') }}
@endsection

@section('content')
    @livewire('activos.bienes.bienes-empresa')
@endsection
