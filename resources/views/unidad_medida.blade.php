@extends('layouts.landing')

@section('section')
    {{ __('Unidad de Medida') }}
@endsection

@section('content')
    @livewire('unidad.unidad_medida')
@endsection
