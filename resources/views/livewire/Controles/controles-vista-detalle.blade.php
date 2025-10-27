@extends('layouts.landing')

@section('content')
<div class="container mt-5">
    <div class="text-first mt-4" style="padding-bottom: 20px;">
        <a href="{{ $previousUrl }}" class="btn-3 text-white rounded-lg">
            Volver
        </a>
    </div>
    @if (count($controlesFaltantes) > 0)
    <div class="alert alert-info">
        <strong>Controles Obligatorios de Carga Inicial:</strong>
        @foreach ($controlesFaltantes as $controlObligatorio)
        {{ $controlObligatorio->nombre}}.
        @endforeach
    </div>
    @endif
    @if (count($controles) > 0)
    @php
    $controlesChunks = $controles->chunk(3);
    @endphp
    @foreach ($controlesChunks as $controlesRow)
    <div class="row">
        @foreach ($controlesRow as $control)
        <div class="col-md-4 mb-4">
            <div class="card text-end" style="width: 18rem;">
                <div class="card-header">
                    @foreach ($controlesModelo as $controlModel)
                    @if ($controlModel->id_control == $control->id_control)
                    <h5 class="card-title">{{ $controlModel->nombre }}</h5>
                    @endif
                    @endforeach
                </div>
                <div class="card-body">
                    <a href="{{ route('control-detalle', ['id_control' => $control->id_control]) }}">Detalles Controles</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
    @else
    <div class="text-center font-semibold custom-bg text-black px-6 py-4">
        SIN REGISTROS PARA MOSTRAR
    </div>
    @endif
</div>
@endsection