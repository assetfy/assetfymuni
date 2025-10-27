<div class="p-2">
    <h1 class="text-3xl font-bold mb-4">Colección de Bienes</h1>
    <div>
        <div class="flex flex-col mb-6">
            <div class="flex items-center mb-2"> <!-- Cambié aquí para que sea una fila flexible -->
                <input type="text" placeholder="Buscar Activo" class="px-4 py-2 border rounded-lg mr-4"
                    wire:model.live="search">
                <div class="flex-shrink-0">
                    @livewire('activos.create-activos')
                </div>
                @if ($activos->isNotEmpty())
                <a href="{{ route('download') }}" class="btn btn-sm btn-primary ml-4" style="background-color: #2479c7">
                    Descargar QRs
                </a>
                @endif
            </div>

            @if ($activos->isEmpty())
            <div class="text-center font-semibold custom-bg text-black px-6 py-4">
                SIN REGISTROS PARA MOSTRAR
            </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($activos as $activo)
                <div class="perspective-1000">
                    <div class="card-container relative h-80 transform-style-preserve-3d transition-transform duration-500"
                        onclick="this.classList.toggle('flipped-{{ $activo->id }}')">
                        <!-- Parte delantera de la tarjeta -->
                        <div class="card-front absolute w-full h-full backface-hidden shadow-lg">
                            <div class="bg-white rounded-lg p-4 relative h-full flex flex-col justify-between">
                                <div class="text-body-secondary">
                                    <div class="mx-auto w-36">
                                        <img class="border border-2 border-black" width="240" height="240"
                                            src="https://quickchart.io/qr?text=http://192.168.3.165:8000/datos-activos/{{ $activo->id_activo }}"
                                            alt="">
                                    </div>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold mb-2">Nombre del activo: {{ $activo->nombre }}
                                    </h2>
                                    <p class="text-gray-500 mb-2">
                                        <strong>Categoría:</strong> {{ $activo->categoria->nombre }}
                                    </p>
                                    <p class="text-gray-500 mb-2">
                                        <strong>Subcategoría:</strong> {{ $activo->subcategoria->nombre }}
                                    </p>
                                    @if ($activo->ubicacion)
                                    <p class="text-gray-500 mb-2">
                                        {{ $activo->ubicacion->pais }},
                                        {{ $activo->ubicacion->provincia }},
                                        {{ $activo->ubicacion->ciudad }},
                                        {{ 'dgsfgsdf' }}
                                    </p>
                                    @endif
                                    @if ($activo->tipo)
                                    <div
                                        class="absolute bottom-2 right-2 flex items-center text-sm text-gray-400">
                                        <img src="{{ asset('storage/' . $activo->tipo->imagen) }}"
                                            alt="Icono de {{ $activo->tipo->nombre }}"
                                            class="w-6 h-6 mr-2">
                                        <p>{{ $activo->tipo->nombre }}</p>
                                        <i class="fa-solid fa-arrow-right text-black ml-2"></i>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Parte trasera de la tarjeta -->
                        <div class="card-back absolute w-full h-full bg-white rounded-lg p-4 transform rotate-y-180 backface-hidden text-black">
                            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 md:overflow-visible">
                                <!-- Acciones -->
                                <div class="flex flex-col justify-center items-center mb-4">
                                    <h3 class="font-bold text-lg mb-1 text-center">Acciones</h3>
                                    <button
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2"
                                        wire:click="openServiciosActivosModal({{ $activo }})"
                                        onclick="event.stopPropagation();">
                                        Solicitar Servicios
                                    </button>
                                    <button
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        wire:click="openCreateControlModal({{ $activo }})"
                                        onclick="event.stopPropagation();">
                                        Cargar control
                                    </button>
                                    <a href="{{ route('controles-vista-detalle', $activo->id_activo) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Detalle Controles</span>
                                    </a>
                                    <a href="{{ route('activos-atributos', $activo->id_activo) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Detalle Atributos</span>
                                    </a>
                                    <a href="{{ route('auditoria-activos', $activo->id_activo) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Detalle de Movimientos</span>
                                    </a>
                                </div>
                                <!-- Detalles -->
                                <div class="flex flex-col justify-center items-center mb-4">
                                    <h3 class="font-bold text-lg mb-1 text-center">Detalles</h3>
                                    <a href="{{ route('activos-vista-detalle', $activo->etiqueta) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 mb-2 w-full text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Detalle Activo</span>
                                    </a>
                                    <a href="{{ route('activos-servicios', $activo->id_activo) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Servicios Realizados</span>
                                    </a>
                                    <a href="{{ route('servicios-activos', $activo->id_activo) }}"
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2 text-center"
                                        onclick="event.stopPropagation();">
                                        <span class="sub_link_name">Servicios Solicitados</span>
                                    </a>
                                    <button
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2"
                                        wire:click="openCreateAtributoModal({{ $activo }})"
                                        onclick="event.stopPropagation();">
                                        Nuevos Atributos
                                    </button>
                                    <button
                                        class="action-button rounded-lg text-black font-bold py-2 w-full mb-2"
                                        wire:click="openModalCambiarUbicacion({{ $activo->id_activo }})"
                                        onclick="event.stopPropagation();">
                                        Cambiar Ubicacion
                                    </button>
                                </div>
                            </div>
                            <div class="flex-grow mt-2"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <style>
            .perspective-1000 {
                perspective: 1000px;
            }

            .transform-style-preserve-3d {
                transform-style: preserve-3d;
            }

            .transition-transform {
                transition: transform 0.5s;
            }

            .backface-hidden {
                backface-visibility: hidden;
            }

            .card-container {
                height: 22rem;
            }

            .card-container[class*='flipped-'] {
                transform: rotateY(180deg);
            }

            .card-back {
                overflow: hidden;
            }

            .card-front,
            .card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 0.5rem;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .rotate-y-180 {
                transform: rotateY(180deg);
            }

            .filter-button {
                background-color: #2479c7;
                color: white;
                transition: background-color 0.3s;
                width: 7em;
            }

            .filter-button:focus {
                outline: none;
            }

            .image-container {
                width: 100%;
                height: 9rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f0f0f0;
            }

            .image {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .card-back .grid {
                margin-top: auto;
            }

            .card-back .grid div {
                padding-bottom: 1.5rem;
            }

            .action-button,
            .detail-button {
                height: 2.5rem;
                width: 12em;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.875rem;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                color: black;
            }

            .action-button:hover,
            .action-button:focus {
                transform: translateY(-0.25em);
                box-shadow: 0 1em 0.7em -0.4em rgba(0, 212, 255, 0.8);
            }

            /* Media query para pantallas pequeñas */
            @media (max-width: 640px) {
                .grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .card-container {
                    height: 20rem;
                    width: 29rem;
                }

                .image-container {
                    height: 10rem;
                }

                .action-button,
                .detail-button {
                    font-size: 0.75rem;
                    height: 2.25rem;
                }

                .filter-button {
                    margin-bottom: 0.5rem;
                }

                .card-back {
                    overflow: auto;
                }
            }

            /* Media query para pantallas medianas */
            @media (max-width: 929px) {
                .grid {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .card-back .grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }

                .action-button,
                .detail-button {
                    font-size: small;
                }
            }

            /* Media query para pantallas grandes */
            @media (min-width: 1023px) {
                .grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }

                .card-back .grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }
            }

            /* Media query para pantallas muy grandes */
            @media (min-width: 1350px) {
                .grid {
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 1rem;
                }

                .card-back .grid {
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }
            }

            .action-button {
                border: 1px solid #4A5568;
                padding: 12px;
                transition: border-color 0.3s ease;
            }

            .action-button:hover {
                border-color: #2D3748;
            }
        </style>
    </div>
</div>