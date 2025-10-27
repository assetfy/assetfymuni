<div>
    <div class="container">
        <div>
            <h2 class="text-2xl font-semibold leading-tight mb-4 p-4">TIPOS</h2>
            <!-- Campo de búsqueda -->
            <input type="text" wire:model.live="search" placeholder="Buscar tipos..."
                class="form-control mb-4" />
        </div>
        @if ($tipos->count())
            <div class="row mb-4">
                @foreach ($tipos as $tipo)
                    <div class="custom-col col-md-3 mb-4">
                        <div class="custom-card card rounded shadow">
                            <div class="card-body text-center">
                                <h5 class="card-title mt-2">{{ $tipo->nombre }}</h5>
                                <img src="{{ asset('storage/' . $tipo->imagen) }}" class="card-img-top mx-auto mt-3"
                                    alt="Foto" style="max-width: 150px;">
                                <div>
                                    @php
                                        $contActivos = $activos->where('id_tipo', $tipo->id_tipo)->count();
                                    @endphp
                                    @if ($contActivos > 0)
                                        <a href="{{ route('card', ['id_tipo' => $tipo->id_tipo]) }}"
                                            class="boton-activos">Mostrar</a>
                                    @else
                                        <a href="{{ route('card', ['id_tipo' => $tipo->id_tipo]) }}"
                                            class="boton-activos">Registrar Activo</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Enlaces de paginación -->
            <div class="d-flex justify-content-center">
                {{ $tipos->links() }}
            </div>
        @else
            <div class="text-center font-semibold bg-gray-200 text-black px-6 py-4">
                SIN REGISTROS PARA MOSTRAR
            </div>
        @endif

        <!-- Estilos CSS -->
        <style>
            @media (max-width: 1224px) {
                .custom-card {
                    max-width: none;
                }

                .custom-col {
                    flex: 0 0 33.3333%;
                    max-width: 33.3333%;
                }
            }

            @media (max-width: 780px) {
                .custom-card {
                    max-width: none;
                }

                .custom-col {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
            }

            @media (max-width: 420px) {
                .custom-card {
                    max-width: 90%;
                    margin: auto;
                }

                .custom-col {
                    flex: 0 0 100%;
                    max-width: 100%;
                    display: flex;
                    justify-content: center;
                }

                .row {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                }
            }
        </style>
    </div>
</div>
