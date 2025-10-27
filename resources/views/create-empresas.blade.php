@extends('layouts.landing')

@section('section')
    {{ __('Crear empresa') }}
@endsection

@section('content')
    @livewire('empresas.create-empresas')
@endsection
