@extends('layouts.landing')

@section('section')
    {{ __('Atributos Subcategorias') }}
@endsection

@section('content')
    @livewire('subcategoria.atributossubcategorias.atributos-subcategorias')
@endsection
