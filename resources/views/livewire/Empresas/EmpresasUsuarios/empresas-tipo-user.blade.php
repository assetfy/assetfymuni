<div class="container mx-auto p-4" x-data="{ showUploadModal: false, currentSlide: 0, flippedCard: null }" @close-upload-modal.window="showUploadModal = false">
    <div class="flex flex-wrap lg:flex-nowrap -mx-4" style="padding-top: 25px;">
        <div class="w-full lg:w-2/3 w-full-lg px-4 mb-8 lg:mb-0">

            <div class="bg-white p-6 rounded-lg flex-1">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Equipo</h2>
                    @livewire('empresas.EmpresasUsuarios.create-usuario')                
                </div>
                <div class="mb-4">
                    <input type="text" placeholder="Buscar colaborador" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($usuariosPaginados as $usuario)
                    <div class="flex flex-col" wire:key="usuario-{{ $usuario->id_usuario }}">
                        <div class="perspective relative" @click="flippedCard = flippedCard === {{ $usuario->id_usuario }} ? null : {{ $usuario->id_usuario }}">
                            <div class="card-container h-64 w-full transform-style-3d transition-transform duration-500" :class="{ 'flipped': flippedCard === {{ $usuario->id_usuario }} }">
                                <div class="card-front absolute inset-0 backface-hidden flex flex-col items-center justify-center bg-white rounded-lg p-4">
                                    <div class="image-container">
                                        <img src="https://via.placeholder.com/150" class="rounded-lg mx-auto mb-4" alt="Imagen">
                                    </div>
                                    @foreach ($users as $user)
                                    @if ($usuario->id_usuario == $user->id)
                                    <p class="font-semibold">{{ $user->name }}</p>
                                    @endif
                                    @endforeach
                                    <p class="text-gray-500">{{ $usuario->cargo }}
                                        @if ($usuario->es_representante_tecnico == 'Si')
                                        <span class="ml-2">
                                            <i class="fas fa-hard-hat text-blue-500"></i>
                                        </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="card-back absolute inset-0 backface-hidden transform rotate-y-180 flex flex-col items-center justify-center bg-white shadow-lg rounded-lg p-4">
                                    <h4 class="text-lg font-bold text-center mb-4">Administrar</h4>
                                    @if ($this->usuarioTieneRoles($usuario->id_usuario))
                                    <button @click.stop wire:click.prevent="updateRepresentanteTecnico({{ $usuario->id_usuario }}, '{{ $usuario->es_representante_tecnico == 'Si' ? 'No' : 'Si' }}')" class="action-button hover-effect">
                                        {{ $usuario->es_representante_tecnico == 'Si' ? 'Remover' : 'Agregar' }} Técnico
                                    </button>
                                    <br>
                                    <button id="action-button-{{ $usuario }}-unique" class="action-button hover-effect" wire:click="updatePermisos({{ $usuario }})" onclick="event.stopPropagation();">
                                        <span class="truncate">Editar Permisos</span>
                                    </button>
                                    <br>
                                        @if ($usuario->es_representante_tecnico == 'Si')
                                        <button id="action-button-{{ $usuario }}-unique" class="action-button hover-effect" wire:click="updateRepresentate({{  $usuario->id_usuario }})" onclick="event.stopPropagation();">
                                            <span class="truncate">Actividad representada</span>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if ($usuariosPaginados->hasPages())
                <div class="mt-4">
                    {{ $usuariosPaginados->links() }}
                </div>
                @endif
            </div>
        </div>
        <!-- Calendario de servicios -->
        <div class="right-column w-full lg:w-1/3 w-full-lg px-4" style="padding-top: 20px;">
            <div class="p-4 h-full" style="border: 1px solid #ccc; border-radius: 10px">
                <h3 class="text-xl font-semibold mb-4 text-black text-center">Calendario de servicios</h3>
                <!-- Calendario -->
                <livewire:calendar />

                <h4 class="text-lg font-semibold mt-4">Próximos servicios</h4>
                <div class="mt-2 space-y-2">
                    @if($servicios->isEmpty())
                    <p class="text-gray-500">Sin servicios próximos</p>
                    @else
                    @foreach($servicios as $servicio)
                    @php
                    $serviceDate = $servicio->fecha_modificada ? \Carbon\Carbon::parse($servicio->fecha_modificada) : \Carbon\Carbon::parse($servicio->fechaHora);
                    @endphp
                    <div class="service-date p-2 rounded bg-blue-100 mb-2">
                        <p class="text-sm font-semibold">{{ $serviceDate->format('jS F') }}</p>
                        <p class="text-sm">{{ $servicio->descripcion }}</p>
                        <p class="text-sm text-gray-500">{{ $serviceDate->format('H:i') }}</p>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .perspective {
            perspective: 1000px;
        }

        .card-container {
            transform-style: preserve-3d;
            transition: transform 0.5s;
        }

        .card-front,
        .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-front {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .card-back {
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .imagen {
            width: 25%;
            background-size: cover;
            border-radius: 0.5rem;
        }

        @media (max-width: 1536px) {
            .imagen, .sin-imagen {
                width: 210px;
                height: 192px;
                margin-bottom: 0;
            }
        }

        .sin-imagen {
            width: 25%;
            height: 12rem;
            border-radius: 0.5rem;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 1090px) {
            .sin-imagen {
                width: 210px !important;
                height: 192px !important;
            }
        }

        .card-container.flipped {
            transform: rotateY(180deg);
        }

        @media (max-width: 1024px) {
            .flex-wrap {
                flex-direction: column;
            }

            .right-column {
                margin-top: 1rem;
            }
        }

        @media (min-width: 1024px) and (max-width: 1536px) {
            .w-full-lg {
                width: 100%;
            }
        }

        /* Estilos específicos para los botones */
        .action-button {
            background-color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            color: black;
            display: inline-block;
            position: relative;
            transition: all 0.3s ease;
        }

    

        .action-button::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 3px;
            background-color: #00c4ff;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

      
        .action-button:hover,
        .action-button:focus {
            transform: translateY(-0.25em);
            box-shadow: 0 1em 0.7em -0.4em rgba(0, 212, 255, 0.8);
        }

    
    </style>
</div>
