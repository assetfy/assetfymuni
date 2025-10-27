<div class="mx-10 my-6">
    <!-- Título con Icono -->
    <h3 class="text-2xl font-bold text-center text-gray-900 mb-8 flex items-center justify-center gap-3">
        <i class="fas fa-file-upload text-blue-600"></i> Carga Masiva
    </h3>

    <!-- Contenedor de Importación -->
    <div class="mb-6 p-6 bg-white w-full rounded-lg shadow-lg border border-gray-200">
        <label class="block font-medium text-gray-800 mb-3 text-lg flex items-center gap-3">
            Importar Datos Usuarios
        </label>

        <div class="flex flex-wrap items-center gap-4">
            <!-- Select: Tipo de Datos -->
            <div class="relative w-70">
                <select wire:model.live="tipoDatos"
                    class="border rounded-lg px-6 py-3 text-lg focus:ring-2 focus:ring-blue-500 bg-white appearance-none w-full shadow-md">
                    <option value="1" selected disabled class="text-gray-500">Seleccione una opción</option>
                    <option value="Usuarios">Usuarios</option>
                </select>
            </div>

            <!-- Select: Tipo de Operación -->
            <div class="relative w-70">
                <select wire:model.live="tipoOperacion"
                    class="border rounded-lg px-6 py-3 text-lg focus:ring-2 focus:ring-blue-500 bg-white appearance-none w-full shadow-md">
                    <option value="1" selected disabled class="text-gray-500">Seleccione una opción</option>
                    <option value="Actualizar">🔄 Actualizar</option>
                    <option value="Insertar">➕ Insertar</option>
                </select>
            </div>
            <!-- Botón de carga de archivo y vista previa -->
            @if ($tipoDatos != '1' && $tipoOperacion != '1')
                <button wire:click="descargarEjemploUsuarios"
                    class="px-6 py-3 bg-gray-700 text-white rounded-lg flex items-center gap-2 hover:bg-gray-800 transition shadow-md">
                    <i class="fas fa-download"></i> Descargar Ejemplo
                </button>
                <label
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg cursor-pointer flex items-center gap-2 hover:bg-blue-700 transition shadow-md">
                    <i class="fas fa-upload"></i> Subir Archivo
                    <input type="file" class="hidden" wire:model="archivo">
                </label>
            @endif

            @if ($archivo)
                <button wire:click="importarArchivoUsuario"
                    class="bg-green-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 hover:bg-green-700 transition shadow-md">
                    <i class="fas fa-eye"></i> Vista Previa Usuarios
                </button>
            @endif
        </div>

        <p class="text-gray-600 text-sm mt-3">
            Seleccione el tipo de datos, el tipo de operación y luego cargue un archivo para su importación.
        </p>
    </div>

    @if (!empty($vistaPreviaUsuario))
        <!-- Buscador para Usuarios -->
        <div>
            <input type="text" wire:model.lazy="searchUsuarios" placeholder="Buscar por nombre de Usuario..."
                class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
        </div>

        @php
            $columnsToDisplay = [
                'name' => 'Name',
                'cuit' => 'CUIT', // Aquí se usará para la selección de Empresa
                'email' => 'Email',
                'password' => 'Contraseña',
                'tipo_usuario' => 'Tipo Usuario',
                'rep_tecnico' => 'Representante Técnico',
            ];
        @endphp

        <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
            <h4 class="font-semibold text-lg flex items-center">
                <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
            </h4>
            <table class="min-w-full border-collapse mt-2 border border-gray-300">
                <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                    <tr>
                        @foreach ($columnsToDisplay as $key => $label)
                            <th class="px-4 py-2 font-medium border border-gray-300">
                                {{ $label }}
                                @if ($key === 'cuit')
                                    <!-- Global: Dropdown para selección de Empresa (CUIT) -->
                                    <div class="relative"
                                        x-data='{
                                             openDropdownEmpresaGlobal: false,
                                             searchEmpresaGlobal: "",
                                             dropdownStyles: "",
                                             empresas: @json($empresas),
                                             get filteredEmpresas() {
                                                 return this.empresas.filter(item =>
                                                     (item.cuit + " " + item.razon_social)
.toLowerCase().includes(this.searchEmpresaGlobal.toLowerCase())
                                                 )
                                             }
                                         }'
                                        @click.away="openDropdownEmpresaGlobal = false">
                                        <button type="button"
                                            @click="openDropdownEmpresaGlobal = !openDropdownEmpresaGlobal; if(openDropdownEmpresaGlobal){ const rect = $el.getBoundingClientRect(); dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; }"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalEmpresa') ? '-- Seleccione --' : (function(){
                                               let e = empresas.find(e => e.cuit == $wire.get('globalEmpresa'));
                                               return e ? `${e.cuit} - ${e.razon_social}` : '-- Seleccione --';
                                           })()"></span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownEmpresaGlobal" x-transition :style="dropdownStyles"
                                            class="rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchEmpresaGlobal"
                                                    @keydown.enter="openDropdownEmpresaGlobal = false"
                                                    @keydown.escape="openDropdownEmpresaGlobal = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Empresa...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="empresa in filteredEmpresas" :key="empresa.cuit">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownEmpresaGlobal = false; $wire.set('globalEmpresa', empresa.cuit); searchEmpresaGlobal = ''">
                                                        <span class="font-normal block"
                                                            x-text="`${empresa.cuit} - ${empresa.razon_social}`"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredEmpresas.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    @elseif ($key === 'tipo_usuario')
                                        <!-- Global: Select para Tipo Usuario -->
                                        <select wire:model="Global_tipo_usario"
                                            x-on:change="Livewire.dispatch('updateAllLocalTipoUsuario', [$event.target.value])"
                                            class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                            <option value="" selected disabled class="text-gray-500">Seleccione
                                                una opción</option>
                                            <option value="2">Usuario Administrador</option>
                                            <option value="1">Usuario Común</option>
                                        </select>
                                    @elseif ($key === 'rep_tecnico')
                                        <!-- Global: Select para Representante Técnico -->
                                        <select wire:model="representante_tecnico_global"
                                            x-on:change="Livewire.dispatch('updateAllLocalRepTecnico', [$event.target.value])"
                                            class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                            <option value="" selected disabled class="text-gray-500">Seleccione
                                                una opción</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @forelse ($paginatedUsuarios as $index => $row)
                        <tr class="hover:bg-gray-50 transition">
                            @foreach ($columnsToDisplay as $key => $label)
                                <td class="px-4 py-2 border border-gray-300">
                                    @if ($key === 'cuit')
                                        <!-- Local: Dropdown para selección de Empresa en cada fila -->
                                        <div class="relative"
                                            x-data='{
                                                openDropdownEmpresaLocal: false,
                                                searchEmpresaLocal: "",
                                                dropdownStyles: "",
                                                empresas: @json($empresas->toArray()),
                                                get filteredEmpresas() {
                                                    return this.empresas.filter(item =>
                                                        (item.cuit + " " + item.razon_social)
.toLowerCase().includes(this.searchEmpresaLocal.toLowerCase())
                                                    )
                                                }
                                            }'
                                            @click.away="openDropdownEmpresaLocal = false">
                                            <button type="button"
                                                @click="openDropdownEmpresaLocal = !openDropdownEmpresaLocal; 
                                                    if(openDropdownEmpresaLocal){ 
                                                        const rect = $el.getBoundingClientRect(); 
                                                        dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; 
                                                    }"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get(`localEmpresa.{{ $index }}`) ? '-- Seleccione --' : (function(){
                                                    let e = empresas.find(e => e.cuit == $wire.get(`localEmpresa.{{ $index }}`));
                                                    return e ? `${e.cuit} - ${e.razon_social}` : '-- Seleccione --';
                                                })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownEmpresaLocal" x-transition :style="dropdownStyles"
                                                class="rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchEmpresaLocal"
                                                        @keydown.enter="openDropdownEmpresaLocal = false"
                                                        @keydown.escape="openDropdownEmpresaLocal = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Empresa...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="empresa in filteredEmpresas"
                                                        :key="empresa.cuit">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownEmpresaLocal = false; $wire.set(`localEmpresa.{{ $index }}`, empresa.cuit); searchEmpresaLocal = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${empresa.cuit} - ${empresa.razon_social}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredEmpresas.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    @elseif ($key === 'tipo_usuario')
                                        <!-- Local: Select para Tipo Usuario (por fila) -->
                                        <select wire:model="local_tipo_usario.{{ $index }}"
                                            class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                            <option value="" selected disabled class="text-gray-500">Seleccione
                                                una opción</option>
                                            <option value="2">Usuario Administrador</option>
                                            <option value="1">Usuario Común</option>
                                        </select>
                                    @elseif ($key === 'rep_tecnico')
                                        <!-- Local: Select para Representante Técnico (por fila) -->
                                        <select wire:model="representante_tecnico_local.{{ $index }}"
                                            class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                            <option value="" selected disabled class="text-gray-500">Seleccione
                                                una opción</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    @else
                                        {{ $row[$key] ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <button wire:click="confirmarUsuarios"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                    <i class="fas fa-save"></i> Importar Usuarios
                </button>
            </div>
            <div class="flex justify-end mt-4">
                {{ $paginatedUsuarios->links() }}
            </div>
        </div>
    @endif
</div>
