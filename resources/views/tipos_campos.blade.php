@extends('layouts.landing')

@section('section')
    {{ __('Tipos Campos') }}
@endsection

@section('content')
    @livewire('tiposcampo.tipos_campos')
@endsection