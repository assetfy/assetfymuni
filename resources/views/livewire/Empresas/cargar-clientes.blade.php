<div>
    <!-- Modal -->
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            <h2 class="flex items-center text-xl font-bold text-gray-800">
                <i class="fas fa-user-plus mr-2"></i>
                Registrar Cliente
            </h2>
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                <!-- Opciones para elegir el tipo de cliente -->
                <div class="mb-4">
                    <!--<label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccione el tipo de cliente
                    </label>-->
                    <div class="flex space-x-4">
                        @if ($tipo_clientes == 'individual')
                            <button type="button" wire:click="$set('tipo_clientes', 'individual')"
                                class="flex items-center px-4 py-2 rounded-md border transition-colors duration-150 {{ $tipo_clientes == 'individual' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                <i class="fas fa-user mr-2"></i>
                                Cliente Individual
                            </button>
                        @endif
                        <!--<button type="button" wire:click="$set('tipo_clientes', 'empresa')"
                            class="flex items-center px-4 py-2 rounded-md border transition-colors duration-150 {{ $tipo_clientes == 'empresa' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                            <i class="fas fa-building mr-2"></i>
                            Empresa
                        </button>-->
                    </div>
                </div>

                <!-- Sección de búsqueda y resumen, si no se está usando el formulario manual -->
                @if (!$mostrarAgregarFormulario)
                    @if ($tipo_clientes == 'individual')
                        <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-2">
                            <div class="flex-1">
                                <label for="search_individual" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-search mr-1"></i>
                                    Buscar por nombre o email
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input id="search_individual" type="text"
                                        wire:model.debounce.500ms="terminoBusqueda"
                                        class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                        placeholder="Ej: Juan o juan@ejemplo.com" />
                                </div>
                            </div>
                            <!-- Botón Buscar para usuario -->
                            <button wire:click="buscarUsuarios"
                                class="mt-4 sm:mt-0 h-10 px-4 flex items-center bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:outline-none transition-colors duration-150">
                                <i class="fas fa-search mr-2"></i>
                                Buscar
                            </button>
                            <!-- Botón para activar formulario manual -->
                            <button wire:click="activarFormularioManual"
                                class="mt-4 sm:mt-0 h-10 px-4 flex items-center bg-gray-200 text-gray-700 rounded-md shadow hover:bg-gray-300 focus:outline-none relative group"
                                title="Agregar cliente individual manualmente">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar
                                <span
                                    class="absolute bottom-full mb-1 hidden bg-black text-white text-xs rounded-lg px-2 py-1 shadow-lg group-hover:block">
                                    Agregar cliente individual manualmente
                                </span>
                            </button>
                        </div>

                        <!-- Mensaje de resultado de búsqueda -->
                        @if ($mensajeBusqueda)
                            <div
                                class="mt-4 p-3 rounded-md flex items-center {{ $mensajeBusqueda ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ $mensajeBusqueda }}</span>
                            </div>
                        @endif

                        <!-- Resumen de datos del cliente encontrado -->
                        @if ($mensajeBusqueda)
                            <div class="mt-6 p-4 bg-gray-100 rounded-md">
                                <h3 class="flex items-center text-lg font-semibold mb-4 text-gray-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Datos del Cliente Encontrado:
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" wire:model="nombre"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">CUIL</label>
                                        <input type="text" wire:model="cuil"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="text" wire:model="email"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($tipo_clientes == 'empresa')
                        <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-2">
                            <div class="flex-1">
                                <label for="search_empresa" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-search mr-1"></i>
                                    Buscar por razón social o CUIT
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input id="search_empresa" type="text"
                                        wire:model.debounce.500ms="terminoBusqueda" maxlength="11"
                                        class="block w-full pl-10 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                        placeholder="Ej: Empresa o 30123456789" />
                                </div>
                            </div>
                            <!-- Botón Buscar para empresa -->
                            <button wire:click="buscarEmpresa"
                                class="mt-4 sm:mt-0 h-10 px-4 flex items-center bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:outline-none transition-colors duration-150">
                                <i class="fas fa-search mr-2"></i>
                                Buscar
                            </button>
                            <!-- Botón para activar formulario manual -->
                            <button wire:click="activarFormularioManual"
                                class="mt-4 sm:mt-0 h-10 px-4 flex items-center bg-gray-200 text-gray-700 rounded-md shadow hover:bg-gray-300 focus:outline-none relative group"
                                title="Agregar empresa manualmente">
                                <i class="fas fa-plus mr-2"></i>
                                Agregar
                                <span
                                    class="absolute bottom-full mb-1 hidden bg-black text-white text-xs rounded-lg px-2 py-1 shadow-lg group-hover:block">
                                    Agregar empresa manualmente
                                </span>
                            </button>
                        </div>
                        <!-- Mensaje de resultado de búsqueda -->
                        @if ($mensajeBusqueda)
                            <div
                                class="mt-4 p-3 rounded-md flex items-center @if ($empresaEncontrada) bg-green-50 border border-green-200 text-green-700 @else bg-red-50 border border-red-200 text-red-700 @endif">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ $mensajeBusqueda }}</span>
                            </div>
                        @endif
                        <!-- Resumen de datos de la empresa encontrada -->
                        @if ($empresaEncontrada)
                            <div class="mt-6 p-4 bg-gray-100 rounded-md">
                                <h3 class="flex items-center text-lg font-semibold mb-4 text-gray-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Datos de la Empresa Encontrada:
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                                        <input type="text" wire:model="razon_social"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">CUIT</label>
                                        <input type="text" wire:model="cuit"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Provincia</label>
                                        <input type="text" wire:model="provincia"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Localidad</label>
                                        <input type="text" wire:model="localidad"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif

                <!-- Sección de formulario manual para ingresar datos -->
                @if ($mostrarAgregarFormulario)
                    @foreach ($clientesManual as $index => $cliente)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                                <input type="text"
                                    wire:model.debounce.500ms="clientesManual.{{ $index }}.razon_social"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                    placeholder="Ingrese la Razón Social" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">CUIT</label>
                                <input type="text"
                                    wire:model.debounce.500ms="clientesManual.{{ $index }}.cuit"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                    placeholder="Ingrese el CUIT" maxlength="11" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Número de Cliente</label>
                                <input type="text"
                                    wire:model.debounce.500ms="clientesManual.{{ $index }}.contrato"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm p-2.5"
                                    placeholder="Ingrese el número de Cliente" />
                            </div>

                            {{-- Dropdown Provincia --}}
                            <div x-data="{ open: false, search: '' }" @click.away="open = false" class="relative">
                                <label class="block text-sm font-medium text-gray-700">Provincia</label>
                                <button type="button" @click="open = !open"
                                    class="mt-1 w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-left flex justify-between items-center">
                                    <span class="text-gray-700">
                                        {{ $clientesManual[$index]['provincia'] ?: 'Seleccione provincia' }}
                                    </span>
                                    <svg class="w-5 h-5 transform" :class="{ 'rotate-180': open }"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute mt-2 w-full bg-white border rounded-lg shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="search" wire:model.live="searchProvincia"
                                            @keydown.enter.prevent="open = false"
                                            @keydown.escape.prevent="open = false"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                            placeholder="Buscar provincia..." />
                                    </div>
                                    <ul class="max-h-60 overflow-auto">
                                        @forelse($provincias as $prov)
                                            <li class="cursor-pointer px-4 py-2 hover:bg-blue-100"
                                                @click="open = false; search = ''"
                                                wire:click="setProvincia({{ $index }}, '{{ $prov['id'] }}')">
                                                {{ $prov['nombre'] }}
                                            </li>
                                        @empty
                                            <li class="px-4 py-2 text-gray-600">
                                                {{ empty($searchProvincia) ? 'Sin opciones disponibles' : 'Sin resultados' }}
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            {{-- Dropdown Localidad --}}
                            <div class="relative" x-data="{ openLoc: false }" @click.away="openLoc = false">
                                <label class="block text-sm font-medium text-gray-700">Localidad</label>

                                <button type="button"
                                    @click="if (!@js($clientesManual[$index]['provincia'])) return; openLoc = !openLoc"
                                    class="mt-1 w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-left flex justify-between items-center
        @if (empty($clientesManual[$index]['provincia'])) opacity-50 cursor-not-allowed @endif">
                                    <span class="text-gray-700">
                                        {{ $clientesManual[$index]['localidad'] ?: 'Seleccione localidad' }}
                                    </span>
                                    <svg class="w-5 h-5 transform" :class="{ 'rotate-180': openLoc }"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.585l3.71-4.354a.75.75 0 111.14.98l-4 4.7a.75.75 0 01-1.14 0l-4-4.7a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="openLoc" x-transition
                                    class="absolute mt-2 w-full bg-white border rounded-lg shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" wire:model.live="searchLocalidad"
                                            @keydown.enter.prevent="openLoc = false"
                                            @keydown.escape.prevent="openLoc = false"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                            placeholder="Buscar localidad..." />
                                    </div>
                                    <ul class="max-h-60 overflow-auto">
                                        @forelse($localidades as $loc)
                                            <li class="cursor-pointer px-4 py-2 hover:bg-blue-100"
                                                @click="openLoc = false"
                                                wire:click="setLocalidad({{ $index }}, '{{ $loc['id'] }}')">
                                                {{ $loc['nombre'] }}
                                            </li>
                                        @empty
                                            <li class="px-4 py-2 text-gray-600">
                                                {{ empty($searchLocalidad) ? 'Sin localidades' : 'Sin resultados' }}
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            {{-- Contratos Dinámicos --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Números de Contrato</label>

                                <div class="flex items-center space-x-2 mb-4">
                                    <x-button wire:click="agregarContrato" class="h-9">Agregar</x-button>
                                </div>

                                <div class="space-y-2">
                                    @foreach ($contratos as $index => $contrato)
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-2">
                                            <x-input type="text"
                                                wire:model.defer="contratos.{{ $index }}.numero"
                                                class="w-full sm:w-[200px] border-2 border-gray-300 text-black"
                                                placeholder="Ingrese el número de contrato" />
                                            <button wire:click="eliminarContrato({{ $index }})"
                                                class="mt-2 sm:mt-0 flex items-center justify-center w-9 h-9 rounded-full bg-red-500 text-white font-bold"
                                                title="Eliminar contrato">
                                                ✖
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    @endforeach
                    <!-- Botón para agregar otro formulario manual -->
                    <div class="mt-4">
                        <button wire:click="agregarOtroCliente" type="button"
                            class="flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 transition-colors duration-150">
                            <i class="fas fa-plus mr-2"></i>
                            Agregar otro cliente
                        </button>
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="cerrar"
                    class="flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow hover:bg-gray-300 transition-colors duration-150">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                @if ($mostrarAgregarFormulario || $mensajeBusqueda)
                    <button wire:click="guardar"
                        class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 transition-colors duration-150">
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </button>
                @endif
                @if ($mostrarAgregarFormulario)
                    <button wire:click="mostrarBuscador"
                        class="flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md shadow hover:bg-yellow-600 transition-colors duration-150">
                        <i class="fas fa-search-plus mr-2"></i>
                        Buscar otro
                    </button>
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
