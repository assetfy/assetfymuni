@extends('layouts.landing')

@section('section')
    {{ __('Perfiles') }}
@endsection

@section('content')
    @livewire('perfil.perfiles')
@endsection