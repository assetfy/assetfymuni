@extends('layouts.landing')

@section('section')
    {{ __('Card') }}
@endsection

@section('content')
     @livewire('activos.card',compact('id_tipo'))
@endsection

