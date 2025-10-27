<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            Crear Usuario
        </x-slot>
        <form wire:submit.prevent="{{ $createNewUser ? 'createUser' : 'save' }}">
            <x-slot name="content">
                <div class="mb-4">
                    <!-- Botón para alternar modo: usuario existente / nuevo usuario -->
                    <div class="mb-4">
                        <button type="button" wire:click="$toggle('createNewUser')" class="btn btn-secondary">
                            {{ $createNewUser ? 'Usar usuario existente' : 'Crear nuevo usuario' }}
                        </button>
                    </div>

                    @if ($createNewUser)
                        <!-- Formulario para crear un nuevo usuario -->
                        <div class="mb-3">
                            <label class="block text-md font-semibold text-black">Apellido</label>
                            <x-input type="text" wire:model.lazy="apellido"
                                class="w-full h-9 border-2 border-gray-400 text-black" />
                            <x-input-error for="apellido" />
                        </div>

                        <div class="mb-3">
                            <label class="block text-md font-semibold text-black">Nombre</label>
                            <x-input type="text" wire:model.lazy="name"
                                class="w-full h-9 border-2 border-gray-400 text-black" />
                            <x-input-error for="name" />
                        </div>
                        <div class="mb-3">
                            <label class="block text-md font-semibold text-black">Email</label>
                            <x-input type="email" wire:model.lazy="email"
                                class="w-full h-9 border-2 border-gray-400 text-black" />
                            <x-input-error for="email" />
                        </div>
                        <div class="mb-3">
                            <label class="block text-md font-semibold text-black">Cuil</label>
                            <x-input type="text" wire:model.lazy="cuil" maxlength="11"
                                class="w-full h-9 border-2 border-gray-400 text-black" />
                            <x-input-error for="cuil" />
                        </div>
                        <div class="mb-3">
                            <label class="block text-md font-semibold text-black">Contraseña</label>
                            <x-input type="password" wire:model.lazy="password"
                                class="w-full h-9 border-2 border-gray-400 text-black" />
                            <x-input-error for="password" />
                        </div>
                    @else
                        <!-- Formulario para buscar usuario existente -->
                        <div class="flex items-center space-x-4 mb-2">
                            <label class="block text-md font-semibold text-black w-48">CUIL</label>
                            <div class="flex space-x-1 w-full">
                                <x-input type="text" class="w-[250px] h-9 border-2 border-gray-400 text-black"
                                    wire:model.lazy="cuil" maxlength="11"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                    wire:keydown.enter="buscarUsuario" />
                                <x-button wire:click="buscarUsuario" class="ml-2 h-9">Buscar</x-button>
                            </div>
                        </div>

                        <x-input-error for="cuil" />

                        @if (!$buscando && $cuil)
                            <!-- Indicador de carga -->
                            <div wire:loading wire:target="buscarUsuario" class="text-black text-md mb-2">
                                Buscando usuario...
                            </div>
                            <div>
                                @if ($usuario)
                                    @foreach ($usuarios as $user)
                                        @if ($user->id == $usuario)
                                            <div class="flex justify-center items-center mt-2">
                                                <p>
                                                    <strong class="text-black">Datos del usuario: </strong>
                                                    <span
                                                        class="fw-bold text-primary text-items-center mb-2">{{ $user->name }}</span>
                                                </p>
                                            </div>
                                        @endif
                                    @endforeach
                                @elseif ($mensajeError)
                                    <div class="alert alert-danger mt-2">
                                        {{ $mensajeError }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    <!-- Campo común: Tipo de Usuario (se muestra en ambos modos) -->
                    <div class="flex items-center mb-2 mt-2">
                        <label for="tipo_inter_exter"
                            class="block text-md font-semibold text-black w-[10.25rem] mt-2">Tipo Usuario</label>
                        <select id="tipo_inter_exter" wire:model.live="tipo_inter_exter"
                            class="form-select border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-1/2 h-9 mt-2">
                            <option value="" hidden>Seleccione</option>
                            <option value="Interno">Interno</option>
                            <option value="Externo">Externo</option>
                        </select>
                    </div>
                    <x-input-error for="tipo_inter_exter" />

                    <!-- Sección de Prestadora y Contratos para usuarios Externos -->
                    @if ($tipo_inter_exter == 'Externo')
                        <div class="flex items-center space-x-4 mb-2 mt-3">
                            <label for="busqueda_prestadora"
                                class="block text-md font-semibold text-black w-48">Prestadora</label>
                            <div class="flex space-x-1 w-full">
                                <x-input type="text" class="w-full h-9 border-2 border-gray-400 text-black"
                                    placeholder="Ingrese CUIT o Razón Social" wire:keydown.enter="buscarEmpresa"
                                    wire:model.lazy="busqueda_prestadora"
                                    oninput="this.value = this.value.replace(/[^0-9a-zA-Z\s]/g, '')" />
                                <x-button wire:click="buscarEmpresa" class="ml-2 h-9">Buscar</x-button>
                            </div>
                        </div>

                        @if ($datos_empresa_prestadora)
                            @foreach ($empresas as $empresa)
                                @if ($empresa->cuit == $datos_empresa_prestadora)
                                    <div class="mb-4">
                                        <label class="fw-bold text-black mb-2">Datos de la Prestadora</label>
                                        <div class="ml-2 text-gray-700 mb-2">
                                            <strong>Razón Social:</strong> {{ $empresa->razon_social }}
                                        </div>
                                        <div class="ml-2 text-gray-700 mb-2">
                                            <strong>Ubicación:</strong>
                                            {{ "{$empresa->provincia}, {$empresa->localidad}, {$empresa->domicilio}" }}
                                        </div>
                                        <div class="ml-2 text-gray-700 mb-2">
                                            <strong>Actividad que realiza:</strong>
                                            {{ $empresa->descripcion_actividad }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @elseif($empresa_buscada && $error_message)
                            <div wire:key="error-message" class="alert alert-danger mt-2">
                                @if ($error_message)
                                    Datos incorrectos, verifíquelos
                                @endif
                            </div>
                        @endif

                        @if ($datos_empresa_prestadora)
                            <div>
                                <div class="mb-4">
                                    <div class="flex items-center mb-2">
                                        <label class="block text-md font-semibold text-black w-48">Número de
                                            Contrato</label>
                                        <x-button wire:click="agregarContrato" class="ml-2 h-9">Agregar</x-button>
                                        @if (count($contratos) > 0)
                                            <x-input type="text" wire:model.lazy="contratos.0.numero"
                                                class="ml-2 w-[200px] h-9 border-2 border-gray-400 text-black"
                                                placeholder="Ingrese el número de contrato" />
                                            <button wire:click="eliminarContrato(0)"
                                                class="ml-2 flex items-center justify-center w-9 h-9 rounded-full bg-red-500 text-white font-bold">
                                                ✖
                                            </button>
                                        @endif
                                    </div>
                                    <div>
                                        @foreach ($contratos as $index => $contrato)
                                            @if ($index > 0)
                                                <div class="flex items-center space-x-1 mb-2"
                                                    wire:key="contrato-{{ $index }}">
                                                    <x-input type="text"
                                                        wire:model.lazy="contratos.{{ $index }}.numero"
                                                        class="w-[200px] h-9 border-2 border-gray-400 text-black ml-[20.5rem]"
                                                        placeholder="Ingrese el número de contrato" />
                                                    <button wire:click="eliminarContrato({{ $index }})"
                                                        class="flex items-center justify-center w-9 h-9 rounded-full bg-red-500 text-white font-bold">
                                                        ✖
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    <div>
                        <div class="flex items-center mb-2 mt-2">
                            <label for="supervisor"
                                class="block text-md font-semibold text-black w-[10.25rem] mt-2">¿Es
                                supervisor?</label>
                            <select id="supervisor" wire:model.live="supervisor"
                                class="form-select border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-1/2 h-9 mt-2">
                                <option value="" hidden>Seleccione</option>
                                <option value="1">Si</option>
                                <option value="0">No</option>
                            </select>
                            <x-input-error for="supervisor" />
                        </div>

                        {{-- ¿Tiene supervisor? --}}
                        <div class="flex items-center mb-2 mt-2">
                            <label for="tiene_supervisor"
                                class="block text-md font-semibold text-black w-[10.25rem] mt-2">¿Tiene
                                supervisor?</label>
                            <select id="tiene_supervisor" wire:model.live="tiene_supervisor"
                                class="form-select border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-1/2 h-9 mt-2">
                                <option value="" hidden>Seleccione</option>
                                <option value="1">Si</option>
                                <option value="0">No</option>
                            </select>
                        </div>

                        @if ((string) $tiene_supervisor === '1')
                            <div class="flex items-center mb-2 mt-2" x-data="{ openDropdownEmpleados: false }"
                                @click.outside="openDropdownEmpleados = false" x-cloak>
                                <label class="block text-md font-semibold text-black w-[10.25rem] mt-2">Elija un
                                    Supervisor</label>

                                <div class="relative w-1/2">
                                    <button type="button" @click="openDropdownEmpleados = !openDropdownEmpleados"
                                        class="w-full h-9 bg-white border border-gray-300 rounded-md px-3 flex items-center justify-between">
                                        <span
                                            class="truncate">{{ $selectedEmpleados ?: 'Seleccione supervisor' }}</span>
                                        <svg class="h-4 w-4 text-gray-700" viewBox="0 0 20 20">
                                            <path d="M5.516 7.548l4.484 4.484 4.484-4.484L16 8.032l-6 6-6-6z" />
                                        </svg>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div x-show="openDropdownEmpleados" x-transition
                                        class="absolute mt-1 w-full rounded-lg bg-white shadow-lg z-10 border border-gray-200">
                                        <div class="p-2">
                                            <input type="text" wire:model.live.debounce.300ms="searchEmpleados"
                                                class="w-full border border-gray-300 rounded-lg h-9 px-3"
                                                placeholder="Buscar por nombre o CUIL…" />
                                        </div>

                                        <ul class="max-h-60 overflow-auto">
                                            @forelse(($empleadosLista ?? collect()) as $empleado)
                                                <li wire:click="setEmpleados({{ $empleado->id }})"
                                                    @click="openDropdownEmpleados = false"
                                                    class="cursor-pointer py-2 px-3 hover:bg-blue-50">
                                                    <div class="font-medium">{{ $empleado->name }}</div>
                                                </li>
                                            @empty
                                                <li class="py-3 px-3 text-gray-600">No hay usuarios asociados a los
                                                    datos ingresados.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                                <x-input-error for="supervisor_usuario" />
                            </div>
                        @endif

                        {{-- Árbol de Dependencia (lo tuyo tal cual) --}}
                        <div class="flex items-start mb-2 mt-2">
                            <label for="selectedLevel"
                                class="block text-md font-semibold text-black w-[10.25rem] mt-2">
                                Depende de:
                            </label>
                            <div class="w-1/2">
                                <div wire:ignore>
                                    <input type="hidden" id="selectedLevelInput" wire:model="selectedLevel">
                                    <input id="jstree_search" type="text" placeholder="Buscar nivel…"
                                        class="w-full h-9 border border-gray-300 rounded-md px-3 focus:ring-indigo-500 focus:border-indigo-500 mb-2 mt-2" />
                                    <div id="jstree_container"
                                        class="max-h-72 overflow-auto border border-gray-300 rounded-md p-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
        </form>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                @if ($createNewUser)
                    <x-danger-button wire:click="createUser" wire:loading.remove wire:target="createUser">
                        Crear Usuario
                    </x-danger-button>
                @else
                    @if (!is_null($usuario))
                        <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                            Registrar Usuario
                        </x-danger-button>
                    @endif
                @endif
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="{{ $createNewUser ? 'createUser' : 'save' }}">
                    Cargando...
                </span>
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
