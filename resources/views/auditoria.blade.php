@extends('layouts.landing')

@section('section')
    {{ __('Auditoria') }}
@endsection

@section('content')
    @livewire('empresas.auditoria')
@endsection
