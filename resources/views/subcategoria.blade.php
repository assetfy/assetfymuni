@extends('layouts.landing')

@section('section')
    {{ __('Subcategorias') }}
@endsection

@section('content')
    @livewire('subcategoria.subcategoria')
@endsection