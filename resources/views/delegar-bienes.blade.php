@extends('layouts.landing')

@section('section')
    {{ __('Delegar Bienes') }}
@endsection

@section('content')
    @livewire('activos.bienes.delegar-bienes')
@endsection
