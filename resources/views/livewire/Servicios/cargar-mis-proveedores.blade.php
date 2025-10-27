<div>
    <!-- Modal -->
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            <!-- Título dinámico según el estado -->
            <div class="text-center text-2xl font-bold text-gray-800">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $mostrarAgregarFormulario ? 'Registrar Proveedor Favorito' : 'Registrar Proveedor' }}
                </h2>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Descripción dinámica -->
            @if ($mostrarAgregarFormulario)
                <p class="text-gray-600 mb-4">
                    Para registrar proveedores favoritos, completa los datos mínimos solicitados. Esto nos permitirá
                    guardarlos en tu lista personal y facilitar el acceso en futuras búsquedas.
                </p>
            @endif

            <!-- Buscador y Botones -->
            @if (!$mostrarAgregarFormulario)
                <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-2">
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nombre o
                            CUIT</label>
                        <input id="search" type="text" wire:model.debounce.500ms="terminoBusqueda"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                            placeholder="Ej: Empresa o 30123456789" />
                    </div>
                    <!-- Botón Buscar -->
                    <button wire:click="buscar"
                        class="mt-4 sm:mt-0 h-10 px-4 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700 focus:outline-none">
                        Buscar
                    </button>
                    <!-- Botón Agregar -->
                    <button wire:click="mostrarFormularioAgregar"
                        class="mt-4 sm:mt-0 h-10 px-4 bg-gray-200 text-gray-700 rounded-md shadow hover:bg-gray-300 focus:outline-none relative group"
                        title="Agregar proveedor no registrado">
                        <i class="fas fa-plus"></i>
                        <!-- Tooltip -->
                        <span
                            class="absolute bottom-full mb-1 hidden bg-black text-white text-xs rounded-lg px-2 py-1 shadow-lg group-hover:block">
                            Agregar proveedor no registrado
                        </span>
                    </button>
                </div>
            @endif

            <!-- Mensaje de Resultado -->
            @if ($mensajeBusqueda && !$mostrarAgregarFormulario)
                <div
                    class="{{ $encontrado ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700' }} mt-4 p-3 rounded-md">
                    {{ $mensajeBusqueda }}
                </div>
            @endif

            <!-- Resumen de Datos Encontrados -->
            @if ($encontrado && !$mostrarAgregarFormulario)
                <div class="mt-6 p-4 bg-gray-100 rounded-md">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Datos del Proveedor Encontrado:</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Razón Social -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                            <input type="text" wire:model="proveedores.0.razonSocial"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                readonly />
                        </div>
                        <!-- CUIT -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CUIT</label>
                            <input type="text" wire:model="proveedores.0.cuit"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                readonly />
                        </div>
                        <!-- Localidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Localidad</label>
                            <input type="text" wire:model="proveedores.0.localidad"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                readonly />
                        </div>
                        <!-- Provincia -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Provincia</label>
                            <input type="text" wire:model="proveedores.0.provincia"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5"
                                readonly />
                        </div>
                        <div class="form-group mb-4">
                            <label class="block text-sm font-medium text-gray-700">¿Contrato Vigente?</label>
                            <select id="contrato"
                                class="mt-1 block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none"
                                wire:model.live="contrato">
                                <option value="" hidden>Seleccione</option>
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <!-- NroContrato (Solo si Contrato Vigente es Si) -->
                        @if ($contrato === 'Si')
                            <div class="form-group mb-4">
                                <label class="block text-sm font-medium text-gray-700">Nro Contrato</label>
                                <input type="text" id="numeroContrato" wire:model.defer="numeroContrato"
                                    class="form-control mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ingrese el Nro de Contrato" maxlength="10"
                                    @if ($contrato === 'Si') required @endif />
                                @error('numeroContrato')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label for="fechaContrato" class="block text-sm font-medium text-gray-700">Fecha del
                                    Contrato</label>
                                <input type="date" id="fechaContrato" wire:model.defer="fechaContrato"
                                    class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    required />
                                @error('fechaContrato')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Formulario para Agregar Proveedores -->
            @if ($mostrarAgregarFormulario)
                <div class="space-y-6 mt-6">
                    @foreach ($proveedores as $index => $proveedor)
                        <div class="border p-4 rounded-md relative">
                            <h3 class="text-lg font-semibold mb-4">Proveedor {{ $index + 1 }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Razón Social -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                                    <input type="text"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.razonSocial"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.razonSocial') border-red-500 @enderror"
                                        placeholder="Ingrese la Razón Social" />
                                    @error('proveedores.' . $index . '.razonSocial')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- CUIT -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">CUIT</label>
                                    <input type="text"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.cuit"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.cuit') border-red-500 @enderror"
                                        maxlength="11" pattern="\d*" inputmode="numeric"
                                        placeholder="Solo números, 11 dígitos"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
                                    @error('proveedores.' . $index . '.cuit')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Localidad -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Localidad</label>
                                    <input type="text"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.localidad"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.localidad') border-red-500 @enderror"
                                        placeholder="Ingrese la Localidad" />
                                    @error('proveedores.' . $index . '.localidad')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Provincia -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Provincia</label>
                                    <input type="text"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.provincia"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.provincia') border-red-500 @enderror"
                                        placeholder="Ingrese la Provincia" />
                                    @error('proveedores.' . $index . '.provincia')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Latitud (opcional) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Latitud</label>
                                    <input type="number" step="any"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.lat"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.lat') border-red-500 @enderror"
                                        placeholder="Opcional" />
                                    @error('proveedores.' . $index . '.lat')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Longitud (opcional) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Longitud</label>
                                    <input type="number" step="any"
                                        wire:model.debounce.500ms="proveedores.{{ $index }}.long"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.long') border-red-500 @enderror"
                                        placeholder="Opcional" />
                                    @error('proveedores.' . $index . '.long')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email"
                                    wire:model.debounce.500ms="proveedores.{{ $index }}.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.email') border-red-500 @enderror"
                                    placeholder="Ejemplo: correo@dominio.com " />
                                @error('proveedores.' . $index . '.email')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <br>
                            <div>
                                <label for="url" class="block text-base font-medium text-gray-700">
                                    Sitio Web
                                </label>
                                <input type="url" wire:model.debounce.500ms="proveedores.{{ $index }}.url"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm p-2.5 @error('proveedores.' . $index . '.email') border-red-500 @enderror"
                                    placeholder="Ejemplo: https://www.ejemplo.com (Opcional)" />
                                @error("proveedores.$index.url")
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <label class="block text-sm font-medium text-gray-700">¿Contrato Vigente?</label>
                                <select id="contrato"
                                    class="mt-1 block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none"
                                    wire:model.live="contrato">
                                    <option value="" hidden>Seleccione</option>
                                    <option value="Si">Sí</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <!-- NroContrato (Solo si Contrato Vigente es Si) -->
                            @if ($contrato === 'Si')
                                <div class="form-group mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Nro Contrato</label>
                                    <input type="text" id="numeroContrato" wire:model.defer="numeroContrato"
                                        class="form-control mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Ingrese el Nro de Contrato" maxlength="10"
                                        @if ($contrato === 'Si') required @endif />
                                    @error('numeroContrato')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-4">
                                    <label for="fechaContrato" class="block text-sm font-medium text-gray-700">Fecha
                                        del
                                        Contrato</label>
                                    <input type="date" id="fechaContrato" wire:model.defer="fechaContrato"
                                        class="form-control mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        required />
                                    @error('fechaContrato')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                            <!-- Botón para eliminar este proveedor -->
                            @if ($index > 0)
                                <button type="button" wire:click="eliminarProveedor({{ $index }})"
                                    class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                                    title="Eliminar Proveedor">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                    <!-- Botón para agregar otro proveedor -->
                    <!-- <button type="button" wire:click="agregarProveedor"
                        class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700 focus:outline-none flex items-center">
                        <i class="fas fa-plus mr-2"></i> Agregar Otro Proveedor
                    </button> -->
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="cerrar"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md shadow hover:bg-gray-300">
                    Cancelar
                </button>
                @if ($mostrarAgregarFormulario || $encontrado)
                    <button wire:click="guardar"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">
                        Guardar
                    </button>
                @endif
                @if ($mostrarAgregarFormulario)
                    <button wire:click="mostrarBuscador"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-md shadow hover:bg-yellow-600">
                        Buscar otra empresa
                    </button>
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
