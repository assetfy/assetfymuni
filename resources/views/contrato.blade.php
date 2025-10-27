@extends('layouts.landing')

@section('section')
    {{ __('Contrato') }}
@endsection

@section('content')
    @livewire('contratos.contrato')
@endsection
