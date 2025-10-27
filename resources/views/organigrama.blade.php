@extends('layouts.landing')

@section('section')
    {{ __('Organigrama') }}
@endsection

@section('content')
    @livewire('empresas.organigrama')
@endsection
