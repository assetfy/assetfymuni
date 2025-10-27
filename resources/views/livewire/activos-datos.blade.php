{{-- resources/views/livewire/activos-datos.blade.php --}}
<div class="container-fluid" wire:key="activos-datos-root">
    <div class="row justify-content-center">
        <div class="text-center mb-3">
            <a href="#">
                <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" class="title" style="height: 4rem;">
            </a>
        </div>

        <div class="col-12 col-md-10 col-lg-8 col-xl-6 mx-auto">
            <div class="card card-warning card-custom">
                <div class="card-header text-center text-uppercase">
                    DETALLES DEL BIEN
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Nombre del Bien</div>
                            <div class="col-8">{{ $activo->nombre }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Tipo</div>
                            <div class="col-8">
                                @foreach ($tipos as $tipo)
                                @if ($activo->id_tipo == $tipo->id_tipo)
                                {{ $tipo->nombre }}
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @if($servicios)
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Ultimo mantenimiento</div>
                            <div class="col-8">
                                {{ $ultimaFechaServicio }}
                            </div>
                        </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Ubicación</div>
                            <div class="col-8">
                                @php $tieneUbicacion = false; @endphp
                                @foreach ($ubicaciones as $ubicacion)
                                @if ($activo->id_ubicacion == $ubicacion->id_ubicacion)
                                {{ $ubicacion->nombre }}
                                @php $tieneUbicacion = true; @endphp
                                @endif
                                @endforeach
                                @if (!$tieneUbicacion)
                                <span class="text-muted">Sin ubicación</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Estado General</div>
                            <div class="col-8">
                                @foreach ($altas as $alta)
                                @if ($activo->id_estado_sit_alta == $alta->id_estado_sit_alta)
                                {{ $alta->nombre }}
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 font-weight-bold">Estado Inventario</div>
                            <div class="col-8">{{ $activo->estado_inventario }}</div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Acciones (ul -> li, sin divs adentro) --}}
            <div class="mt-6 text-center">
                <ul class="list-unstyled p-0 m-0" style="font-size:.9rem;">
                    @if (!empty($gestorEmail))
                    <li class="mt-3">
                        <span wire:click="correo('gestor')"
                            class="text-danger fw-bold"
                            style="cursor:pointer;text-transform:uppercase;">
                            REPORTAR FALLA
                        </span>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ $loginNextUrl }}" style="text-decoration: underline;">
                    <span><strong>Ingresa con tu cuenta de Assetfy</strong></span><br>
                    <span><strong>para más datos del bien</strong></span>
                </a>
            </div>
        </div>
    </div>

</div>