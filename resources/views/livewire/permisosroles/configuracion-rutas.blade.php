<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl">
        <x-slot name="title">
            Configuración de Ruta
        </x-slot>

        <x-slot name="content">
            <!-- Seleccionar opción: Bienes o Clientes -->
            <div class="mb-6">
                <x-label value="Seleccione la opción de configuración:" class="text-sm font-semibold text-gray-700" />
                <div class="flex space-x-6 mt-2">
                    <!-- Opción para Bienes -->
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <!-- Icono para Bienes -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        <input type="radio" value="bienes" wire:model.live="opcion" class="form-radio" />
                        <span class="text-sm">Bienes</span>
                    </label>
                    <!-- Opción para Clientes -->
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <!-- Icono para Clientes -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 01-8 0M12 14v7m6-7a6 6 0 00-12 0" />
                        </svg>
                        <input type="radio" value="clientes" wire:model.live="opcion" class="form-radio" />
                        <span class="text-sm">Clientes</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <!-- Icono para Clientes -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 01-8 0M12 14v7m6-7a6 6 0 00-12 0" />
                        </svg>
                        <input type="radio" value="tipo_ubicacion" wire:model.live="opcion" class="form-radio" />
                        <span class="text-sm">Tipo de ubicacion</span>
                    </label>
                </div>
            </div>

            <!-- Campos para configuración de Bienes -->
            @if ($opcion === 'bienes')
                <div class="mb-6">
                    <x-label for="tipo" value="Tipo" class="text-sm font-semibold text-gray-700" />
                    <select id="tipo" wire:model.live="tipoSeleccionado"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                        <option value="" hidden>Seleccione un tipo</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <x-label for="categoria" value="Categoría" class="text-sm font-semibold text-gray-700" />
                    <select id="categoria" wire:model.live="categoriaSeleccionada"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                        <option value="" hidden>Seleccione una categoría</option>
                        @foreach ($categoriasFiltradas as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-6">
                    <x-label for="subcategoria" value="Subcategoría" class="text-sm font-semibold text-gray-700" />
                    <select id="subcategoria" wire:model.live="subcategoriaSeleccionada"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                        <option value="" hidden>Seleccione una subcategoría</option>
                        @foreach ($subcategoriasFiltradas as $subcat)
                            <option value="{{ $subcat->id_subcategoria }}">{{ $subcat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($opcion === 'tipo_ubicacion')
                <div class="mb-6">
                    <div class="flex items-center space-x-2">
                        <!-- Ícono para Tipo de Ubicación -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c0 .414-.168.79-.44 1.06l-5.12 5.12a1.5 1.5 0 01-2.12-2.12l5.12-5.12A1.5 1.5 0 0112 11zm0 0a1.5 1.5 0 011.06-.44h4.38m-6.44 1.44a1.5 1.5 0 010 2.12l-4.38 4.38m0 0a1.5 1.5 0 01-2.12-2.12l4.38-4.38" />
                        </svg>
                        <x-label for="tipo" value="Tipo de Ubicación" class="text-sm font-semibold text-gray-700" />
                    </div>
                    <select id="tipo" wire:model.live="tipoUbicacionSeleccionado"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                        <option value="" hidden>Seleccione un tipo</option>
                        @foreach ($tipoUbicacion as $tipo)
                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <!-- Campos para configuración de Clientes -->
            @if ($opcion === 'clientes')
                <!-- Segundo grupo de radio para elegir entre "Cliente" y "Empresa" -->
                <div class="mb-6">
                    <x-label value="Seleccione el tipo de configuración para Clientes:"
                        class="text-sm font-semibold text-gray-700" />
                    <div class="flex space-x-6 mt-2">
                        <!-- Opción Cliente -->
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 01-8 0M12 14v7" />
                            </svg>
                            <input type="radio" value="cliente" wire:model.live="clienteOempresa"
                                class="form-radio" />
                            <span class="text-sm">Cliente</span>
                        </label>
                        <!-- Opción Empresa -->
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M3 12h18" />
                            </svg>
                            <input type="radio" value="empresa" wire:model.live="clienteOempresa"
                                class="form-radio" />
                            <span class="text-sm">Empresa</span>
                        </label>
                    </div>
                </div>
                <!-- Mostrar select según la opción elegida -->
                @if ($clienteOempresa === 'cliente')
                    <div class="mb-6">
                        <x-label for="cliente" value="Cliente" class="text-sm font-semibold text-gray-700" />
                        <select id="cliente" wire:model.live="atributosSeleccionados.cliente"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                            <option value="" hidden>Seleccione un cliente</option>
                            @foreach ($clientesEmpresa as $cliente)
                                <option value="{{ $cliente->cliente_cuit }}">{{ $cliente->cliente_cuit }}</option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($clienteOempresa === 'empresa')
                    <div class="mb-6">
                        <x-label for="empresa" value="Empresa" class="text-sm font-semibold text-gray-700" />
                        <select id="empresa" wire:model.defer="atributosSeleccionados.empresa"
                            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                            <option value="" hidden>Seleccione una empresa</option>
                            @foreach ($clientes as $empresa)
                                <option value="{{ $empresa->cuil }}">{{ $empresa->cuil }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-4">
                <x-secondary-button wire:click="$set('open', false)">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="actualizar">
                    Actualizar configuración
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
