@if (!empty($previewDataActivo))

@php
// Definir las columnas en el orden deseado
$columnsToDisplay = [
'nombre' => 'Nombre',
'identificador' => 'Identificador',
'tipo' => 'Tipo',
'categoria' => 'Categoría',
'subcategoria' => 'Subcategoría',
'marca' => 'Marca',
'modelo' => 'Modelo',
'estado_alta' => 'Estado Situación Alta',
'estado_general' => 'Estado Situación General',
'condicion' => 'Condición',
'atributos' => 'Atributos',
'ubicación' => 'Ubicación',
'depende_de' => 'Depende de',
'responsable_inventario' => 'Gestor',
'usuario_titular' => 'Responsable',
];

$extraColumnsBienes = [
'cuit_propietario' => 'CUIT Propietario',
];
@endphp

<div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
    <h4 class="font-semibold text-lg flex items-center">
        <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de Bienes:
    </h4>

    <!-- Tabla con borde -->
    <table class="min-w-full border-collapse mt-2 border border-gray-300">
        <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
            <tr>
                @foreach ($columnsToDisplay as $key => $label)
                <th class="px-4 py-2 font-medium border border-gray-300">
                    {{ $label }}

                    {{-- Dropdown global según la columna --}}
                    @if ($key === 'tipo')
                    <!-- GLOBAL: Select de Tipo -->
                    <div class="relative"
                        x-data='{
                                                openDropdownGlobalTipo: false,
                                                searchGlobalTipo: "",
                                                tipos: @json($tiposActivos),
                                                get filteredTipos() {
                                                    return this.tipos.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalTipo.toLowerCase())
                                                    )
                                                }
                                            }'
                        @click.away="openDropdownGlobalTipo = false">
                        <button type="button"
                            @click="openDropdownGlobalTipo = !openDropdownGlobalTipo"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalTipo')
                                                    ? 'Seleccione un tipo'
                                                    : '{{ $tiposActivos->firstWhere('id_tipo', $globalTipo ?? null)->nombre ?? 'Seleccione un tipo' }}'">
                            </span>
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
                        <div x-show="openDropdownGlobalTipo" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGlobalTipo"
                                    @keydown.enter="openDropdownGlobalTipo = false"
                                    @keydown.escape="openDropdownGlobalTipo = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Tipo...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredTipos" :key="item.id_tipo">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGlobalTipo = false; $wire.set('globalTipo', item.id_tipo); searchGlobalTipo = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredTipos.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'categoria')
                    <!-- GLOBAL: Select de Categoría filtrado por Tipo y deshabilitado hasta seleccionar Tipo -->
                    <div class="relative" @click.away="open = false"
                        x-data='{
                                open: false,
                                search: "",
                                items: @json($categorias)
                                }'>
                        <button type="button" @click="if ($wire.get('globalTipo') > 0) open = !open"
                            :disabled="$wire.get('globalTipo') <= 0"
                            :class="{
                                                'opacity-50 cursor-not-allowed': $wire.get('globalTipo') <= 0,
                                                'opacity-100 cursor-pointer': $wire.get('globalTipo') > 0
                                            }"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left
                                    focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                            <span
                                x-text="
                                $wire.get('globalTipo') > 0
                                ? (
                                    $wire.get('globalCategoria') > 0
                                        ? (items.find(i => i.id_categoria == $wire.get('globalCategoria')) || {}).nombre
                                        : ' Seleccione Categoría '
                                    )
                                : 'Seleccione una categoría'
                                ">
                            </span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0
                                        01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute mt-1 w-full bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="search" @keydown.enter="open = false"
                                    @keydown.escape="open = false"
                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Categoría...">
                            </div>
                            <ul
                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                <template
                                    x-for="item in items
                                        .filter(i => i.id_tipo == $wire.get('globalTipo'))
                                        .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                    :key="item.id_categoria">
                                    <li @click="open = false;
                                                $wire.set('globalCategoria', item.id_categoria);
                                                search = ''"
                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span x-text="item.nombre"></span>
                                    </li>
                                </template>

                                <template
                                    x-if="
                                            items
                                            .filter(i => i.id_tipo == $wire.get('globalTipo'))
                                            .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
                                            .length === 0
                                        ">
                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'subcategoria')
                    <!-- GLOBAL: Select de Subcategoría filtrado por Categoría y deshabilitado hasta seleccionar Categoría -->
                    <div class="relative" @click.away="open = false"
                        x-data='{
                            open: false,
                            search: "",
                            items: @json($subcategorias)
                            }'>
                        <button type="button"
                            @click="if ($wire.get('globalCategoria') > 0) open = !open"
                            :disabled="$wire.get('globalCategoria') <= 0"
                            :class="{
                                                'opacity-50 cursor-not-allowed': $wire.get('globalCategoria') <= 0,
                                                'opacity-100 cursor-pointer': $wire.get('globalCategoria') > 0
                                            }"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm
                                pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1
                                focus:ring-blue-500 transition">
                            <span
                                x-text="
                            $wire.get('globalCategoria') > 0
                                ? (
                                    $wire.get('globalSubcategoria') > 0
                                    ? (items.find(i => i.id_subcategoria == $wire.get('globalSubcategoria')) || {}).nombre
                                    : ' Seleccione Subcategoría '
                                )
                                : 'Seleccione una subcategoría'
                            "></span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71
                                        a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06
                                        0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute mt-1 w-full bg-white shadow-lg z-10 rounded-md">
                            <div class="p-2">
                                <input type="text" x-model="search" @keydown.enter="open = false"
                                    @keydown.escape="open = false"
                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Subcategoría...">
                            </div>
                            <ul
                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                <template
                                    x-for="item in items
                                        .filter(i => i.id_categoria == $wire.get('globalCategoria'))
                                        .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                    :key="item.id_subcategoria">
                                    <li @click="open = false;
                                                $wire.set('globalSubcategoria', item.id_subcategoria);
                                                search = ''"
                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span x-text="item.nombre"></span>
                                    </li>
                                </template>

                                <template
                                    x-if="
                                            items
                                            .filter(i => i.id_categoria == $wire.get('globalCategoria'))
                                            .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
                                            .length === 0
                                        ">
                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'marca')
                    <!-- GLOBAL: Select de Marca -->
                    <div class="relative" @click.away="open = false"
                        x-data="{
                                open: false,
                                search: '',
                                items: @entangle('marca')
                            }">
                        <button type="button"
                            @click="if ($wire.get('globalSubcategoria') > 0) open = !open"
                            :disabled="$wire.get('globalSubcategoria') <= 0"
                            :class="{
                                    'opacity-50 cursor-not-allowed': $wire.get('globalSubcategoria') <= 0,
                                    'opacity-100 cursor-pointer': $wire.get('globalSubcategoria') > 0
                                            }"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm
                                    pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1
                                    focus:ring-blue-500 transition">
                            <span
                                x-text="
                                    $wire.get('globalSubcategoria') > 0
                                    ? (
                                        $wire.get('globalMarca') > 0
                                        ? (items.find(i => i.id_marca == $wire.get('globalMarca')) || {}).nombre
                                        : ' Seleccione una Marca '
                                    )
                                    : 'Seleccione una subcategoría'
                                "></span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71
                                        a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06
                                        0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute mt-1 w-full bg-white shadow-lg z-10 rounded-md">
                            <div class="p-2">
                                <input type="text" x-model="search" @keydown.enter="open = false"
                                    @keydown.escape="open = false"
                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Marca...">
                            </div>
                            <ul
                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                <template
                                    x-for="item in items.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                    :key="item.id_marca">
                                    <li @click="open = false;
                                        $wire.set('globalMarca', item.id_marca);
                                        search = ''"
                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span x-text="item.nombre"></span>
                                    </li>
                                </template>

                                <template
                                    x-if="items.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase())).length === 0">
                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'modelo')
                    <!-- GLOBAL: Select de modelo -->
                    <div class="relative" @click.away="open = false"
                        x-data='{
                            open: false,
                            search: "",
                            items: @json($modelos)
                            }'>
                        <button type="button" @click="if ($wire.get('globalMarca') > 0) open = !open"
                            :disabled="$wire.get('globalMarca') <= 0"
                            :class="{'opacity-50 cursor-not-allowed': $wire.get('globalMarca') <= 0,
                                    'opacity-100 cursor-pointer': $wire.get('globalMarca') > 0}"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left
                                    focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                            <span
                                x-text="
                                    $wire.get('globalMarca') > 0
                                    ? (
                                        $wire.get('globalModelo') > 0
                                            ? (items.find(i => i.id_modelo == $wire.get('globalModelo')) || {}).nombre
                                            : ' Seleccione un modelo '
                                        )
                                    : 'Seleccione una marca'
                                    ">
                            </span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0
                                        01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition
                            class="absolute mt-1 w-full bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="search" @keydown.enter="open = false"
                                    @keydown.escape="open = false"
                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Modelo...">
                            </div>
                            <ul
                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                <template
                                    x-for="item in items
                                        .filter(i => i.id_marca == $wire.get('globalMarca'))
                                        .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                    :key="item.id_modelo">
                                    <li @click="open = false;
                                        $wire.set('globalModelo', item.id_modelo);
                                        search = ''"
                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span x-text="item.nombre"></span>
                                    </li>
                                </template>

                                <template
                                    x-if="
                                            items
                                            .filter(i => i.id_marca == $wire.get('globalMarca'))
                                            .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
                                            .length === 0
                                        ">
                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'estado_alta')
                    <!-- GLOBAL: Select de Estado Sit Alta -->
                    <div class="relative"
                        x-data='{
                                                openDropdownGlobalSitAlta: false,
                                                searchGlobalSitAlta: "",
                                                estadosSitAlta: @json($estadosSitAlta),
                                                get filteredSitAlta() {
                                                    return this.estadosSitAlta.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalSitAlta.toLowerCase())
                                                    )
                                                }
                                            }'
                        @click.away="openDropdownGlobalSitAlta = false">
                        <button type="button"
                            @click="openDropdownGlobalSitAlta = !openDropdownGlobalSitAlta"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalSitAlta')
                                                    ? 'Seleccione '
                                                    : '{{ $estadosSitAlta->firstWhere('id_estado_sit_alta', $globalSitAlta ?? null)->nombre ?? 'Seleccione' }}'">
                            </span>
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
                        <div x-show="openDropdownGlobalSitAlta" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGlobalSitAlta"
                                    @keydown.enter="openDropdownGlobalSitAlta = false"
                                    @keydown.escape="openDropdownGlobalSitAlta = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Estado Sit Alta...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredSitAlta"
                                    :key="item.id_estado_sit_alta">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGlobalSitAlta = false; $wire.set('globalSitAlta', item.id_estado_sit_alta); searchGlobalSitAlta = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredSitAlta.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'estado_general')
                    <!-- GLOBAL: Select de Estado Sit General -->
                    <div class="relative"
                        x-data='{
                                                openDropdownGlobalSitGeneral: false,
                                                searchGlobalSitGeneral: "",
                                                estadosSitGeneral: @json($estadosSitGeneral),
                                                get filteredSitGeneral() {
                                                    return this.estadosSitGeneral.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalSitGeneral.toLowerCase())
                                                    )
                                                }
                                            }'
                        @click.away="openDropdownGlobalSitGeneral = false">
                        <button type="button"
                            @click="openDropdownGlobalSitGeneral = !openDropdownGlobalSitGeneral"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalSitGeneral')
                                                    ? 'Seleccione '
                                                    : '{{ $estadosSitGeneral->firstWhere('id_estado_sit_general', $globalSitGeneral ?? null)->nombre ?? 'Seleccione' }}'">
                            </span>
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
                        <div x-show="openDropdownGlobalSitGeneral" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGlobalSitGeneral"
                                    @keydown.enter="openDropdownGlobalSitGeneral = false"
                                    @keydown.escape="openDropdownGlobalSitGeneral = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Estado Situacion General...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredSitGeneral"
                                    :key="item.id_estado_sit_general">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGlobalSitGeneral = false; $wire.set('globalSitGeneral', item.id_estado_sit_general); searchGlobalSitGeneral = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredSitGeneral.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'condicion')
                    <!-- GLOBAL: Select de Condicion -->
                    <div class="relative"
                        x-data='{
                                                openDropdownGlobalCondicion: false,
                                                searchGlobalCondicion: "",
                                                condiciones: @json($condiciones),
                                                get filteredCondicion() {
                                                    return this.condiciones.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalCondicion.toLowerCase())
                                                    )
                                                }
                                            }'
                        @click.away="openDropdownGlobalCondicion = false">
                        <button type="button"
                            @click="openDropdownGlobalCondicion = !openDropdownGlobalCondicion"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span
                                x-text="!$wire.get('globalCondicion')
                                                    ? 'Seleccione'
                                                    : '{{ $condiciones->firstWhere('id_condicion', $globalCondicion ?? null)->nombre ?? 'Seleccione' }}'">
                            </span>
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
                        <div x-show="openDropdownGlobalCondicion" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGlobalCondicion"
                                    @keydown.enter="openDropdownGlobalCondicion = false"
                                    @keydown.escape="openDropdownGlobalCondicion = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Condicion...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredCondicion"
                                    :key="item.id_condicion">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGlobalCondicion = false; $wire.set('globalCondicion', item.id_condicion); searchGlobalCondicion = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredCondicion.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'ubicación')
                    <!-- GLOBAL: Select de Ubicación -->
                    <div class="relative"
                        x-data='{
                                                openDropdownGlobalUbicacion: false,
                                                searchGlobalUbicacion: "",
                                                ubicaciones: @json($ubicaciones),
                                                get filteredUbicaciones() {
                                                    return this.ubicaciones.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalUbicacion.toLowerCase())
                                                    )
                                                }
                                            }'
                        @click.away="openDropdownGlobalUbicacion = false">
                        <button type="button"
                            @click="openDropdownGlobalUbicacion = !openDropdownGlobalUbicacion"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span
                                x-text="!$wire.get('globalUbicacion')
                                                    ? 'Seleccione '
                                                    : ubicaciones.find(u => u.id_ubicacion == $wire.get('globalUbicacion'))?.nombre ?? 'Seleccione'">
                            </span>
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

                        <div x-show="openDropdownGlobalUbicacion" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGlobalUbicacion"
                                    @keydown.enter="openDropdownGlobalUbicacion = false"
                                    @keydown.escape="openDropdownGlobalUbicacion = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Ubicación...">
                            </div>

                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredUbicaciones"
                                    :key="item.id_ubicacion">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGlobalUbicacion = false; $wire.set('globalUbicacion', item.id_ubicacion); searchGlobalUbicacion = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>

                                <template x-if="filteredUbicaciones.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'responsable_inventario')
                    <!-- Global: Select de Gestor con búsqueda -->
                    <div class="relative"
                        x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                        @click.away="openDropdownGestor = false">
                        <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalGestores') ? ' Seleccione ' : (function(){
                                    let g = gestores.find(g => g.id == $wire.get('globalGestores'));
                                    return g ? `${g.cuil} - ${g.name}` : ' Seleccione ';
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
                        <div x-show="openDropdownGestor" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGestor"
                                    @keydown.enter="openDropdownGestor = false"
                                    @keydown.escape="openDropdownGestor = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Gestor...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="gestor in filteredGestores"
                                    :key="gestor.id">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGestor = false; $wire.set('globalGestores', gestor.id); searchGestor = ''">
                                        <span class="font-normal block"
                                            x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                    </li>
                                </template>
                                <template x-if="filteredGestores.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'usuario_titular')
                    <!-- Global: Select de Gestor con búsqueda -->
                    <div class="relative"
                        x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                        @click.away="openDropdownGestor = false">
                        <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalResponsable') ? ' Seleccione ' : (function(){
                                    let g = gestores.find(g => g.id == $wire.get('globalResponsable'));
                                    return g ? `${g.cuil} - ${g.name}` : ' Seleccione';
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
                        <div x-show="openDropdownGestor" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGestor"
                                    @keydown.enter="openDropdownGestor = false"
                                    @keydown.escape="openDropdownGestor = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Gestor...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="gestor in filteredGestores"
                                    :key="gestor.id">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGestor = false; $wire.set('globalResponsable', gestor.id); searchGestor = ''">
                                        <span class="font-normal block"
                                            x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                    </li>
                                </template>
                                <template x-if="filteredGestores.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'asignado')
                    <!-- Global: Select de Gestor con búsqueda -->
                    <div class="relative"
                        x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                        @click.away="openDropdownGestor = false">
                        <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('globalAsignado') ? ' Seleccione ' : (function(){
                                let g = gestores.find(g => g.id == $wire.get('globalAsignado'));
                                return g ? `${g.cuil} - ${g.name}` : ' Seleccione';
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
                        <div x-show="openDropdownGestor" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchGestor"
                                    @keydown.enter="openDropdownGestor = false"
                                    @keydown.escape="openDropdownGestor = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Gestor...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="gestor in filteredGestores"
                                    :key="gestor.id">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownGestor = false; $wire.set('globalAsignado', gestor.id); searchGestor = ''">
                                        <span class="font-normal block"
                                            x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                    </li>
                                </template>
                                <template x-if="filteredGestores.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @endif
                </th>
                @endforeach

                @if ($tipoDatos === 'Bienes')
                {{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                @foreach ($extraColumnsBienes as $key => $label)
                <th class="px-4 py-2 font-medium border border-gray-300">
                    {{ $label }}
                    @if ($key === 'cuit_propietario')
                    <!-- Global: Dropdown para selección de Empresa (CUIT) -->
                    <div class="relative"
                        x-data='{
                                openDropdownEmpresaGlobal: false,
                                searchEmpresaGlobal: "",
                                dropdownStyles: "",
                                empresas: @json($empresasClientes),
                                get filteredEmpresas() {
                                    return this.empresas.filter(item =>
                                        (item.cuit + " " + item.razon_social)
                                            .toLowerCase()
                                            .includes(this.searchEmpresaGlobal.toLowerCase())
                                    );
                                }
                            }'
                        @click.away="openDropdownEmpresaGlobal = false">
                        <button type="button"
                            @click="openDropdownEmpresaGlobal = !openDropdownEmpresaGlobal; if(openDropdownEmpresaGlobal){ const rect = $el.getBoundingClientRect(); dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; }"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span
                                x-text="!$wire.get('globalEmpresa') ? 'Seleccione' : (function(){
                                               let e = empresas.find(e => e.cuit == $wire.get('globalEmpresa'));
                                               return e ? `${e.cuit} - ${e.razon_social}` : 'Seleccione';
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
                                        @click="openDropdownEmpresaGlobal = false; $wire.call('aplicarEmpresaGlobales', empresa.cuit, true); searchEmpresaGlobal = ''">
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
                </th>
                @endif
                @endforeach
                @endif
            </tr>
        </thead>
        <tbody class="text-gray-600">
            @forelse ($paginatedActivos as $i => $row)
            <tr class="hover:bg-gray-50 transition" wire:key="act-row-{{ $i }}">
                @foreach ($columnsToDisplay as $key => $label)
                <td class="px-4 py-2 border border-gray-300">
                    @if ($key === 'tipo')
                    <!-- LOCAL: Select de Tipo con búsqueda -->
                    <div class="relative"
                        x-data='{
                                openDropdownLocalTipo: false,
                                searchLocalTipo: "",
                                tipos: @json($tiposs),
                                dropdownStyles: "",
                                get filteredTipos() {
                                    return this.tipos.filter(item => item.nombre.toLowerCase().includes(this.searchLocalTipo.toLowerCase()))
                                }
                            }'
                        @click.away="openDropdownLocalTipo = false">

                        <button type="button"
                            @click="openDropdownLocalTipo = !openDropdownLocalTipo"
                            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">

                            <span class="block truncate"
                                x-data="{ selectedId: $wire.entangle('localTipo.' + {{ $i }}) }"
                                x-init="$watch('selectedId', value => selectedId = value)"
                                x-text="(() => {
                                    const tipo = tipos.find(t => Number(t.id_tipo) === Number(selectedId));
                                    return tipo ? tipo.nombre : 'Seleccione un tipo';
                                })()">
                            </span>

                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="openDropdownLocalTipo" x-transition :style="dropdownStyles"
                            class="rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchLocalTipo"
                                    @keydown.enter="openDropdownLocalTipo = false"
                                    @keydown.escape="openDropdownLocalTipo = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Tipo...">
                            </div>

                            <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredTipos" :key="item.id_tipo">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownLocalTipo = false; $wire.call('onLocalTipoSeleccionado', {{ $i }}, item.id_tipo); searchLocalTipo = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredTipos.length === 0">
                                    <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'categoria')
                    <!-- LOCAL: Select de Categoría filtrado por Tipo y deshabilitado hasta seleccionar Tipo -->
                    <div class="relative"
                        x-data='{
                                                openDropdownLocalCategoria: false,
                                                searchLocalCategoria: "",
                                                dropdownStyles: "",
                                                categorias: @json($categorias),
                                                get filteredCategorias() {
                                                    const tipoSeleccionado = $wire.get("localTipo.{{ $i }}") || $wire.get("globalTipo");

                                                    if (!tipoSeleccionado) return []; // nada seleccionado => no mostrar nada

                                                    return this.categorias
                                                        .filter(item => item.id_tipo == tipoSeleccionado)
                                                        .filter(item =>
                                                            item.nombre.toLowerCase().includes(this.searchLocalCategoria.toLowerCase())
                                                        );
                                                }
                                            }'
                        @click.away="openDropdownLocalCategoria = false">
                        <button type="button" @click="($wire.get('globalTipo') > 0 || $wire.get('localTipo.{{ $i }}') > 0) ? (openDropdownLocalCategoria = !openDropdownLocalCategoria) : null"
                            x-bind:disabled="$wire.get('globalTipo') <= 0 && $wire.get('localTipo.{{ $i }}') <= 0"
                            :class="{
                                    'opacity-50 cursor-not-allowed': $wire.get('globalTipo') <= 0 && $wire.get('localTipo.{{ $i }}') <= 0,
                                    'opacity-100 cursor-pointer': $wire.get('globalTipo') > 0 && $wire.get('localTipo.{{ $i }}') > 0
                                }"
                            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="block truncate"
                                x-text="!$wire.get('localCategoria.{{ $i }}')
                                                    ? ' Seleccione una categoría'
                                                    : '{{ $categorias->firstWhere('id_categoria', $localCategoria[$i] ?? null)->nombre ?? 'Seleccione una categoría' }}'">
                            </span>
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="openDropdownLocalCategoria" x-transition :style="dropdownStyles"
                            class="rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" x-model="searchLocalCategoria"
                                    @keydown.enter="openDropdownLocalCategoria = false"
                                    @keydown.escape="openDropdownLocalCategoria = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Categoria...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="item in filteredCategorias" :key="item.id_categoria">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownLocalCategoria = false; $wire.call('onLocalCategoriaSeleccionada', {{ $i }}, item.id_categoria); searchLocalCategoria = ''">
                                        <span class="font-normal block" x-text="item.nombre"></span>
                                    </li>
                                </template>
                                <template x-if="filteredCategorias.length === 0">
                                    <li
                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                        @elseif ($key === 'subcategoria')
                        <!-- LOCAL: Select de Subcategoría con búsqueda -->
                        <div class="relative"
                            x-data='{
                                                openDropdownLocalSubcategoria: false,
                                                searchLocalSubcategoria: "",
                                                dropdownStyles: "",
                                                subcategorias: @json($subcategorias),
                                                get subcategoriasFiltradas() {
                                                    const categoriaSeleccionada = $wire.get("localCategoria.{{ $i }}") || $wire.get("globalCategoria");

                                                    if (!categoriaSeleccionada) return []; // nada seleccionado => no mostrar nada

                                                    return this.subcategorias
                                                        .filter(item => item.id_categoria == categoriaSeleccionada)
                                                        .filter(item =>
                                                            item.nombre.toLowerCase().includes(this.searchLocalSubcategoria.toLowerCase())
                                                        );
                                                }
                                            }'
                            @click.away="openDropdownLocalSubcategoria = false">
                            <button type="button" @click="($wire.get('globalCategoria') > 0 || $wire.get('localCategoria.{{ $i }}') > 0) ? (openDropdownLocalSubcategoria = !openDropdownLocalSubcategoria) : null"
                                x-bind:disabled="$wire.get('globalCategoria') <= 0 && $wire.get('localCategoria.{{ $i }}') <= 0"
                                :class="{
                                    'opacity-50 cursor-not-allowed': $wire.get('globalCategoria') <= 0 && $wire.get('localCategoria.{{ $i }}') <= 0,
                                    'opacity-100 cursor-pointer': $wire.get('globalCategoria') > 0 && $wire.get('localCategoria.{{ $i }}') > 0
                                }"
                                class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <span class="block truncate"
                                    x-text="!$wire.get('localSubcategoria.{{ $i }}')
                                                    ? ' Seleccione una subcategoría'
                                                    : '{{ $subcategorias->firstWhere('id_subcategoria', $localSubcategoria[$i] ?? null)->nombre ?? 'Seleccione una subcategoría' }}'">
                                </span>
                                <span
                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            <div x-show="openDropdownLocalSubcategoria" x-transition :style="dropdownStyles"
                                class="rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" x-model="searchLocalSubcategoria"
                                        @keydown.enter="openDropdownLocalSubcategoria = false"
                                        @keydown.escape="openDropdownLocalSubcategoria = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Buscar Subcategoria...">
                                </div>
                                <ul
                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                    <template x-for="item in subcategoriasFiltradas" :key="item.id_subcategoria">
                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                            @click="openDropdownLocalSubcategoria = false; $wire.call('onLocalSubcategoriaSeleccionada', {{ $i }}, item.id_subcategoria); searchLocalSubcategoria = ''">
                                            <span class="font-normal block" x-text="item.nombre"></span>
                                        </li>
                                    </template>
                                    <template x-if="subcategoriasFiltradas.length === 0">
                                        <li
                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                            Sin resultados</li>
                                    </template>
                                </ul>
                            </div>
                            @elseif ($key === 'marca')
                            <!-- LOCAL: Select de Marca con búsqueda -->
                            <div class="relative"
                                x-data="{
                                        openDropdownLocalMarca: false,
                                        searchLocalMarca: '',
                                        dropdownStyles: '',
                                        items: [],
                                        marcaSeleccionada() {
                                            const idMarca = $wire.get('localMarca.{{ $i }}');
                                            return this.items.find(m => m.id_marca == idMarca)?.nombre || 'Seleccione una marca';
                                        },
                                        marcasFiltradas() {
                                            return this.items.filter(item =>
                                                item.nombre.toLowerCase().includes(this.searchLocalMarca.toLowerCase())
                                            );
                                        }
                                    }"
                                x-effect="items = $wire.localMarcas[{{ $i }}] || []"
                                @click.away="openDropdownLocalMarca = false">

                                <button type="button"
                                    @click="($wire.get('globalSubcategoria') > 0 || $wire.get('localSubcategoria.{{ $i }}') > 0) ? (openDropdownLocalMarca = !openDropdownLocalMarca) : null"
                                    x-bind:disabled="$wire.get('globalSubcategoria') <= 0 && $wire.get('localSubcategoria.{{ $i }}') <= 0"
                                    :class="{
                                            'opacity-50 cursor-not-allowed': $wire.get('globalSubcategoria') <= 0 && $wire.get('localSubcategoria.{{ $i }}') <= 0,
                                            'opacity-100 cursor-pointer': $wire.get('globalSubcategoria') > 0 && $wire.get('localSubcategoria.{{ $i }}') > 0
                                        }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">

                                    <span class="block truncate" x-text="marcaSeleccionada()"></span>

                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="openDropdownLocalMarca" x-transition :style="dropdownStyles" class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalMarca"
                                            @keydown.enter="openDropdownLocalMarca = false"
                                            @keydown.escape="openDropdownLocalMarca = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Marca...">
                                    </div>

                                    <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in marcasFiltradas()" :key="item.id_marca">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalMarca = false; $wire.set('localMarca.{{ $i }}', item.id_marca); searchLocalMarca = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="marcasFiltradas().length === 0">
                                            <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'modelo')
                            <!-- LOCAL: Select de modelo con búsqueda -->
                            <div class="relative"
                                wire:key="dropdown-modelo-{{ $i }}"
                                x-data='{
                                        openDropdownLocalModelo: false,
                                            searchLocalModelo: "",
                                            get modelosFiltrados() {
                                                const modelos = $wire.get("localModelos.{{ $i }}") || [];
                                                return modelos.filter(item =>
                                                    item.nombre.toLowerCase().includes(this.searchLocalModelo.toLowerCase())
                                                );
                                            }
                                        }'
                                @click.away="openDropdownLocalModelo = false">

                                <button type="button"
                                    @click="($wire.get('globalMarca') > 0 || $wire.get('localMarca.{{ $i }}') > 0) ? (openDropdownLocalModelo = !openDropdownLocalModelo) : null"
                                    x-bind:disabled="$wire.get('globalMarca') <= 0 && $wire.get('localMarca.{{ $i }}') <= 0"
                                    :class="{
                                            'opacity-50 cursor-not-allowed': $wire.get('globalMarca') <= 0 && $wire.get('localMarca.{{ $i }}') <= 0,
                                            'opacity-100 cursor-pointer': $wire.get('globalMarca') > 0 || $wire.get('localMarca.{{ $i }}') > 0
                                        }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">

                                    <span class="block truncate"
                                        x-text="!$wire.get('localModelo.{{ $i }}')
                                                    ? ' Seleccione un modelo'
                                                    : '{{ $modelos->firstWhere('id_modelo', $localModelo[$i] ?? null)->nombre ?? 'Seleccione un modelo' }}'"></span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0
                                                    01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="openDropdownLocalModelo" x-transition class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalModelo" @keydown.enter="openDropdownLocalModelo = false"
                                            @keydown.escape="openDropdownLocalModelo = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Modelo...">
                                    </div>
                                    <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in modelosFiltrados" :key="item.id_modelo">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalModelo = false; $wire.set('localModelo.{{ $i }}', item.id_modelo); searchLocalModelo = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="modelosFiltrados.length === 0">
                                            <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'estado_alta')
                            <!-- LOCAL: Select de Estado Sit Alta con búsqueda -->
                            <div class="relative"
                                x-data='{
                                                openDropdownLocalSitAlta: false,
                                                searchLocalSitAlta: "",
                                                dropdownStyles: "",
                                                estadosSitAlta: @json($estadosSitAlta),
                                                get filteredSitAlta() {
                                                    return this.estadosSitAlta.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalSitAlta.toLowerCase())
                                                    )
                                                }
                                            }'
                                @click.away="openDropdownLocalSitAlta = false">
                                <button type="button"
                                    @click="openDropdownLocalSitAlta = !openDropdownLocalSitAlta; if(openDropdownLocalSitAlta){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="!$wire.get('localSitAlta.{{ $i }}')
                                                    ? 'Seleccione'
                                                    : '{{ $estadosSitAlta->firstWhere('id_estado_sit_alta', $localSitAlta[$i] ?? null)->nombre ?? 'Seleccione' }}'">
                                    </span>
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
                                <div x-show="openDropdownLocalSitAlta" x-transition class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalSitAlta"
                                            @keydown.enter="openDropdownLocalSitAlta = false"
                                            @keydown.escape="openDropdownLocalSitAlta = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Estado Sit Alta...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in filteredSitAlta" :key="item.id_estado_sit_alta">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalSitAlta = false; $wire.set('localSitAlta.{{ $i }}', item.id_estado_sit_alta); searchLocalSitAlta = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredSitAlta.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'estado_general')
                            <!-- LOCAL: Select de Estado Sit General con búsqueda -->
                            <div class="relative"
                                x-data='{
                                                openDropdownLocalSitGeneral: false,
                                                searchLocalSitGeneral: "",
                                                dropdownStyles: "",
                                                estadosSitGeneral: @json($estadosSitGeneral),
                                                get filteredSitGeneral() {
                                                    return this.estadosSitGeneral.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalSitGeneral.toLowerCase())
                                                    )
                                                }
                                            }'
                                @click.away="openDropdownLocalSitGeneral = false">
                                <button type="button"
                                    @click="openDropdownLocalSitGeneral = !openDropdownLocalSitGeneral; if(openDropdownLocalSitGeneral){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="!$wire.get('localSitGeneral.{{ $i }}')
                                                    ? 'Seleccione'
                                                    : '{{ $estadosSitGeneral->firstWhere('id_estado_sit_general', $localSitGeneral[$i] ?? null)->nombre ?? 'Seleccione' }}'">
                                    </span>
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
                                <div x-show="openDropdownLocalSitGeneral" x-transition class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalSitGeneral"
                                            @keydown.enter="openDropdownLocalSitGeneral = false"
                                            @keydown.escape="openDropdownLocalSitGeneral = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Estado Situacion General...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in filteredSitGeneral" :key="item.id_estado_sit_general">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalSitGeneral = false; $wire.set('localSitGeneral.{{ $i }}', item.id_estado_sit_general); searchLocalSitGeneral = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredSitGeneral.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'condicion')
                            <!-- LOCAL: Select de condicion con búsqueda -->
                            <div class="relative"
                                x-data='{
                                        openDropdownLocalCondicion: false,
                                        searchLocalCondicion: "",
                                        dropdownStylesCondicion: "",
                                        condiciones: @json($condiciones),
                                        get filteredCondicion() {
                                            return this.condiciones.filter(item =>
                                            item.nombre.toLowerCase().includes(this.searchLocalCondicion.toLowerCase())
                                            )
                                        }
                                    }'
                                @click.away="openDropdownLocalCondicion = false">
                                <button type="button"
                                    @click="openDropdownLocalCondicion = !openDropdownLocalCondicion;
                                                if (openDropdownLocalCondicion) {
                                                const rect = $el.getBoundingClientRect();
                                                dropdownStylesCondicion = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`
                                                }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="! $wire.get('localCondicion.{{ $i }}')
                                                ? 'Seleccione'
                                                : (condiciones.find(e => e.id_condicion == $wire.get('localCondicion.{{ $i }}')) || {}).nombre">
                                    </span>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0
                                                    111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0
                                                    01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="openDropdownLocalCondicion" x-transition
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalCondicion"
                                            @keydown.enter="openDropdownLocalCondicion = false"
                                            @keydown.escape="openDropdownLocalCondicion = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Condicion...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in filteredCondicion"
                                            :key="item.id_condicion">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalCondicion = false;
                                                        $wire.set('localCondicion.{{ $i }}', item.id_condicion);
                                                        searchLocalCondicion = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredCondicion.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'atributos')
                            <button wire:click="abrirModalAtributos({{ $i }})"
                                class="px-2 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                Seleccionar atributos
                            </button>
                            @elseif ($key === 'ubicación')
                            <!-- LOCAL: Select de Ubicación con búsqueda -->
                            <div class="relative"
                                x-data='{
                                                openDropdownLocalUbicacion: false,
                                                searchLocalUbicacion: "",
                                                dropdownStyles: "",
                                                ubicaciones: @json($ubicaciones),
                                                get filteredUbicaciones() {
                                                    return this.ubicaciones.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalUbicacion.toLowerCase())
                                                    )
                                                }
                                            }'
                                @click.away="openDropdownLocalUbicacion = false">
                                <button type="button"
                                    @click="openDropdownLocalUbicacion = !openDropdownLocalUbicacion"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="!$wire.get('localUbicacion.{{ $i }}')
                                                    ? 'Seleccione'
                                                    : '{{ $ubicaciones->firstWhere('id_ubicacion', $localUbicacion[$i] ?? null)->nombre ?? 'Seleccione' }}'">
                                    </span>
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
                                <div x-show="openDropdownLocalUbicacion" x-transition
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalUbicacion"
                                            @keydown.enter="openDropdownLocalUbicacion = false"
                                            @keydown.escape="openDropdownLocalUbicacion = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Ubicación...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in filteredUbicaciones" :key="item.id_ubicacion">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalUbicacion = false; $wire.set('localUbicacion.{{ $i }}', item.id_ubicacion); searchLocalUbicacion = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredUbicaciones.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'depende_de')
                            @php
                            // índice ABSOLUTO dentro de previewDataActivo
                            $absIndex = ($paginatedActivos->firstItem() - 1) + $i;

                            // lee SIEMPRE desde la colección original por el índice absoluto
                            $depId = $previewDataActivo[$absIndex]['depende_de'] ?? null;

                            // prioriza el nombre seteado por el modal; si no hay, mapea por id
                            $depNombre = $previewDataActivo[$absIndex]['depende_de_nombre']
                            ?? ($depId ? ($nivelesEmpresa[(int)$depId] ?? $nivelesEmpresa[(string)(int)$depId] ?? null) : null);

                            $payload = [
                            'origen' => 'bienes_masiva',
                            'id' => null, // en masiva aún no existe
                            'rowIndex' => $absIndex,
                            'depIdExcel' => $depId,
                            'depNombreExcel' => $depNombre,
                            ];
                            @endphp

                            <div class="flex flex-col items-start space-y-1">
                                <span class="text-sm {{ $depNombre ? 'text-gray-800' : 'text-gray-500 italic' }}">
                                    {{ $depNombre ?? 'Sin Dependencia' }}
                                </span>

                                <button x-data="{ p: @js($payload) }"
                                    @click="$wire.abrirModalDependencia(p)"
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-md
                   bg-blue-600 hover:bg-blue-700 text-white shadow">
                                    <i class="fa fa-sitemap"></i> Editar
                                </button>
                            </div>
                            @elseif ($key === 'responsable_inventario')
                            <!-- Local: Select de Gestor con búsqueda -->
                            <!-- Dropdown Local para Gestor en cada fila -->
                            <div class="relative"
                                x-data='{
                        openDropdownGestorRow: false,
                        searchGestorRow: "",
                        gestores: @json($gestores),
                        dropdownStyles: "",
                        get filteredGestores() {
                            return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
                        }
                    }'
                                @click.away="openDropdownGestorRow = false">
                                <button type="button"
                                    @click="openDropdownGestorRow = !openDropdownGestorRow"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="block truncate"
                                        x-text="!$wire.get('localGestores.{{ $i }}') ? 'Seleccione' : (function(){
                                                    let g = gestores.find(g => g.id == $wire.get('localGestores.{{ $i }}'));
                                                    return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
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
                                <div x-show="openDropdownGestorRow" x-transition
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchGestorRow"
                                            @keydown.enter="openDropdownGestorRow = false"
                                            @keydown.escape="openDropdownGestorRow = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Gestor...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="gestor in filteredGestores" :key="gestor.id">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownGestorRow = false; $wire.set('localGestores.{{ $i }}', gestor.id); searchGestorRow = ''">
                                                <span class="font-normal block"
                                                    x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredGestores.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'usuario_titular')
                            <!-- Local: Select de Gestor con búsqueda -->
                            <div class="relative"
                                x-data='{
                                        openDropdownGestorRow: false,
                                        searchGestorRow: "",
                                        gestores: @json($gestores),
                                        dropdownStyles: "",
                                        get filteredGestores() {
                                            return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
                                        }
                                    }'
                                @click.away="openDropdownGestorRow = false">
                                <button type="button"
                                    @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                    class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="block truncate"
                                        x-text="!$wire.get('localResponsable.{{ $i }}') ? 'Seleccione' : (function(){
                                            let g = gestores.find(g => g.id == $wire.get('localResponsable.{{ $i }}'));
                                            return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
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
                                <div x-show="openDropdownGestorRow" x-transition
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchGestorRow"
                                            @keydown.enter="openDropdownGestorRow = false"
                                            @keydown.escape="openDropdownGestorRow = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Gestor...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="gestor in filteredGestores" :key="gestor.id">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownGestorRow = false; $wire.set('localResponsable.{{ $i }}', gestor.id); searchGestorRow = ''">
                                                <span class="font-normal block"
                                                    x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredGestores.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @elseif ($key === 'asignado')
                            <!-- Local: Select de Gestor con búsqueda -->
                            <div class="relative"
                                x-data='{
                                    openDropdownGestorRow: false,
                                    searchGestorRow: "",
                                    gestores: @json($gestores),
                                    dropdownStyles: "",
                                    get filteredGestores() {
                                        return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
                                    }
                                }'
                                @click.away="openDropdownGestorRow = false">
                                <button type="button"
                                    @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="!$wire.get('localAsignado.{{ $i }}') ? 'Seleccione' : (function(){
                                            let g = gestores.find(g => g.id == $wire.get('localAsignado.{{ $i }}'));
                                            return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
                                        })()"></span>
                                    <span class="block truncate"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>
                                <div x-show="openDropdownGestorRow" x-transition
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchGestorRow"
                                            @keydown.enter="openDropdownGestorRow = false"
                                            @keydown.escape="openDropdownGestorRow = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Gestor...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="gestor in filteredGestores" :key="gestor.id">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownGestorRow = false; $wire.set('localAsignado.{{ $i }}', gestor.id); searchGestorRow = ''">
                                                <span class="font-normal block"
                                                    x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredGestores.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            @else
                            {{ $row[$key] ?? '' }}
                            @endif
                </td>
                @endforeach

                @if ($tipoDatos === 'Bienes')
                {{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                @foreach ($extraColumnsBienes as $key => $label)
                <td class="px-4 py-2 border border-gray-300">
                    @if ($key === 'cuit_propietario')
                    <!-- Local: Dropdown para selección de Empresa en cada fila -->
                    <div class="relative"
                        x-data='{
                                                openDropdownEmpresaLocal: false,
                                                searchEmpresaLocal: "",
                                                dropdownStyles: "",
                                                empresas: @json($empresasClientes->toArray()),
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
                                x-text="!$wire.get(`localEmpresa.{{ $i }}`) ? 'Seleccione' : (function(){
                                                    let e = empresas.find(e => e.cuit == $wire.get(`localEmpresa.{{ $i }}`));
                                                    return e ? `${e.cuit} - ${e.razon_social}` : 'Seleccione';
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
                                <template x-for="empresa in filteredEmpresas" :key="empresa.cuit">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="openDropdownEmpresaLocal = false; $wire.set(`localEmpresa.{{ $i }}`, empresa.cuit); searchEmpresaLocal = ''">
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
                </td>
                @endif
                @endforeach
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if ($tipoDatos == 'Activos')
    <div class="flex justify-first mt-2">
        <button wire:click="confirmarImporActivo"
            class="px-4 py-2 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Importar Activos
        </button>
    </div>
    @else
    <div class="flex justify-first mt-2">
        <button wire:click="confirmarImporActivo"
            class="px-4 py-2 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Importar Activos
        </button>
    </div>
    @endif
    <div class="flex justify-first mt-4 mb-20">
        {{ $paginatedActivos->links('livewire.custom-pagination') }}
    </div>
</div>

{{-- Modal para editar atributos --}}
@if ($openModalAtributo)
<div x-data x-show="true" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg">
        <h2 class="text-lg font-semibold mb-4">Seleccionar atributos</h2>

        @if (!is_null($filaEditandoAtributos) && isset($atributosDisponibles[$filaEditandoAtributos]))
        @foreach ($atributosDisponibles[$filaEditandoAtributos] as $id => $atributo)
        @php
        $nombre = $atributo['nombre'] ?? 'Atributo';
        $valores = $atributo['valores'] ?? [];
        @endphp

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $nombre }}</label>

            @if (!empty($valores))
            {{-- Predefinido: dropdown --}}
            <select
                wire:model.defer="atributos.{{ $filaEditandoAtributos }}.{{ $id }}"
                class="block w-full border rounded px-2 py-1">
                <option value="" hidden>Seleccionar</option>
                @foreach ($valores as $valor)
                <option value="{{ $valor }}">{{ $valor }}</option>
                @endforeach
            </select>
            @else
            {{-- No predefinido: numérico / fecha / texto --}}
            @switch($atributo['tipo'])
            @case('Numerico')
            <input type="number" step="any"
                wire:model.defer="atributos.{{ $filaEditandoAtributos }}.{{ $id }}"
                class="w-full border rounded px-2 py-1">
            @break

            @case('Fecha')
            <input type="date"
                wire:model.defer="atributos.{{ $filaEditandoAtributos }}.{{ $id }}"
                class="w-full border rounded px-2 py-1">
            @break

            @default
            <input type="text"
                wire:model.defer="atributos.{{ $filaEditandoAtributos }}.{{ $id }}"
                class="w-full border rounded px-2 py-1">
            @endswitch
            @endif
        </div>
        @endforeach
        @else
        <p class="text-gray-600">No hay atributos disponibles para esta fila.</p>
        @endif

        <div class="flex justify-end mt-6 space-x-2">
            <button wire:click="cerrarModalAtributos"
                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
            <button wire:click="cerrarModalAtributos"
                class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">Seleccionar</button>
        </div>
    </div>
</div>
@endif

@endif