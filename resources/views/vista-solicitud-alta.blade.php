@extends('layouts.landing')

@section('section')
    {{ __('Vistas de solicitud') }}
@endsection
@section('content')
    @livewire('estado.vista-solicitud-alta',compact('solicitud'))
@endsection