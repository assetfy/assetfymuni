@extends('layouts.landing')

@section('section')
    {{ __('Controles Subcategorias') }}
@endsection

@section('content')
    @livewire('controles.controlessubcategorias.controles-subcategoria')
@endsection