<div>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 py-12">
        <body class="antialiased font-sans bg-gray-200">
            <div class="container mx-auto px-4 sm:px-8">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">Controles</h2>
                </div>
                <div class="px-6 py-4 flex items-center">
                    <x-input class="flex-1 mr-4" placeholder="Búsqueda" type="text" wire:model.live="search" />
                    @livewire('controles.create-controles-activos')
                </div>
                @if ($activosControles->count())
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        @foreach ($datosUnicos as $unico)
                            <div class="col-md-3 border-dark mb-2">
                                <div class="card">
                                    <div class="card-header">
                                        @foreach ($activos as $activo)
                                            @if ($activo->id_activo == $unico->id_activo)
                                                <h5 class="card-title"> Activo: {{ $activo->nombre }}</h5>
                                            @endif
                                        @endforeach
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($tipos as $tipo)
                                            @if ($unico->id_tipo_activo == $tipo->id_tipo)
                                                <li class="list-group-item"> Tipo: {{ $tipo->nombre }}</li>
                                            @endif
                                        @endforeach
                                        @foreach ($categorias as $cat)
                                            @if ($unico->id_categoria_activo == $cat->id_categoria)
                                                <li class="list-group-item"> Categoria: {{ $cat->nombre }}</li>
                                            @endif
                                        @endforeach
                                        @foreach ($subcategorias as $sub)
                                            @if ($unico->id_subcategoria_activo == $sub->id_subcategoria)
                                                <li class="list-group-item"> Subcategoria: {{ $sub->nombre }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    <div class="card-header">
                                        <h5 class="card-title">Controles</h5>
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        @foreach ($activosControles as $activosControle)
                                            @if ($activosControle->id_activo == $unico->id_activo)
                                                @foreach ($controles as $control)
                                                    @if ($activosControle->id_control == $control->id_control)
                                                        <li class="list-group-item">
                                                            {{ $control->nombre }}
                                                            <a href="{{ route('controles-vista-detalle', $activosControle->id_activo) }}"
                                                                method="GET">detalle</a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            Fecha de inicio: {{ $activosControle->fecha_inicio }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            Fecha de Termino: {{ $activosControle->fecha_fin }}
                                                        </li>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-6 py-4">
                        SIN REGISTROS
                    </div>
                @endif
                @if ($activosControles->hasPages())
                    <div class="px-6 py-3">
                        {{ $activosControles->links() }}
                    </div>
                @endif
            </div>
        </body>
    </div>
</div>
