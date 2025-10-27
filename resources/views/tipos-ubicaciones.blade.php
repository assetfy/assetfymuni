@extends('layouts.landing')

@section('section')
    {{ __('Tipos Ubicaciones') }}
@endsection

@section('content')
     @livewire('ubicaciones.tipos-ubicaciones')
@endsection