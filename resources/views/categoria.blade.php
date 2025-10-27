@extends('layouts.landing')

@section('section')
    {{ __('Categoria') }}
@endsection

@section('content')
    @livewire('categoria.categoria')
@endsection
