@if (!empty($vistaPreviaUsuario))

@php
$colW = [
'nombre' => 'w-40',
'apellido' => 'w-32',
'email' => 'w-36',
'cuil' => 'w-36',
'legajo' => 'w-28',
'cuit empresa' => 'min-w-[14rem] md:min-w-[14rem] xl:min-w-[14rem]',
'password' => 'w-24',
'tipo usuario' => 'w-28',
'numcontrato' => 'w-28',
'enviar_invitación' => 'w-28',
'supervisor' => 'w-28',
'supervisor_usuario' => 'w-44'];
@endphp

@php
$columnsToDisplay = [
'nombre' => 'Nombre',
'apellido' => 'Apellido',
'email' => 'Email',
'cuil' => 'CUIL',
'legajo' => 'Legajo',
'cuit empresa' => 'CUIT Empresa', // Aquí se usará para la selección de Empresa
'password' => 'Contraseña',
'tipo usuario' => 'Tipo Usuario',
'numcontrato' => 'Numero Contrato',
'enviar_invitación' => 'Enviar Invitación',
'supervisor' => 'Supervisor',
'supervisor_usuario' => 'Supervisor Usuario',
'depende_de' => 'Depende de',
];
@endphp

<div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
    <h4 class="font-semibold text-lg flex items-center">
        <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
    </h4>
    <table class="min-w-full table-fixed border-collapse mt-2 border border-gray-300">
        <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
            <tr>
                @foreach ($columnsToDisplay as $key => $label)
                <th class="px-3 py-2 font-medium border border-slate-200 {{ $colW[$key] ?? '' }}">
                    {{ $label }}
                    @if ($key === 'cuit empresa')
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
                                        @click="openDropdownEmpresaGlobal = false; $wire.call('aplicarEmpresaGlobal', empresa.cuit, true); searchEmpresaGlobal = ''">
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
                        @elseif ($key === 'tipo usuario')
                        <!-- Global: Dropdown para Tipo Usuario (estilo buscable como "múltiples pisos") -->
                        <div class="relative"
                            x-data='{
            open:false,
            search:"",
            dropdownStyles:"",
            opts:[ {v:"2", l:"Usuario Administrador"}, {v:"1", l:"Usuario Común"} ],
            get filtered(){
                return this.opts.filter(o => o.l.toLowerCase().includes(this.search.toLowerCase()))
            },
            label(){
                const v = String($wire.get("Global_tipo_usario") ?? "");
                const o = this.opts.find(x => String(x.v) === v);
                return o ? o.l : "Seleccione";
            }
         }'
                            @click.away="open=false">

                            <button type="button"
                                @click="open = !open;
                        if(open){
                            const r = $el.getBoundingClientRect();
                            dropdownStyles = `position:fixed;top:${r.bottom}px;left:${r.left}px;width:${r.width}px;`;
                        }"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <span x-text="label()"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>

                            <div x-show="open" x-transition :style="dropdownStyles" class="rounded-md bg-white shadow-lg z-20">
                                <div class="p-2">
                                    <input type="text"
                                        x-model="search"
                                        @keydown.enter="open=false"
                                        @keydown.escape="open=false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Buscar...">
                                </div>

                                <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                    <template x-for="o in filtered" :key="o.v">
                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                            @click="open=false;
                                $wire.set('Global_tipo_usario', o.v);
                                Livewire.dispatch('updateAllLocalTipoUsuario', [o.v]);
                                search=''">
                                            <span class="block" x-text="o.l"></span>
                                        </li>
                                    </template>

                                    <template x-if="filtered.length === 0">
                                        <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                            Sin resultados
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @elseif ($key === 'supervisor_usuario')
                        <div class="relative"
                            x-data='{
        open:false, search:"", dropdownStyles:"",
        opts: @json($supervisores), // [{id,name,cuil}]
        get filtered(){
            const s = this.search.toLowerCase();
            return s ? this.opts.filter(o => (o.name + " " + (o.cuil ?? "")).toLowerCase().includes(s))
                     : this.opts;
        },
        label(){
            const gid = $wire.get("globalSupervisorUsuario");
            if(gid === null || gid === undefined) return "Seleccione ";
            const f = this.opts.find(o => Number(o.id) === Number(gid));
            return f ? f.name : "Seleccione ";
        }
     }'
                            @click.away="open=false">
                            <button type="button"
                                @click="open = !open; if(open){ const r = $el.getBoundingClientRect(); dropdownStyles = `position:fixed;top:${r.bottom}px;left:${r.left}px;width:${r.width}px;`; }"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <span x-text="label()"></span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            <div x-show="open" x-transition :style="dropdownStyles" class="rounded-md bg-white shadow-lg z-20">
                                <div class="p-2">
                                    <input type="text" x-model="search" @keydown.enter="open=false" @keydown.escape="open=false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Buscar supervisor...">
                                </div>
                                <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="open=false; $wire.aplicarSupervisorGlobal(null, true); search=''">
                                        <span class="block">Ninguno</span>
                                    </li>
                                    <template x-for="o in filtered" :key="o.id">
                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                            @click="open=false; $wire.aplicarSupervisorGlobal(o.id, true); search=''">
                                            <span class="block" x-text="o.name + (o.cuil ? ' ('+o.cuil+')' : '')"></span>
                                        </li>
                                    </template>
                                    <template x-if="filtered.length === 0">
                                        <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        @endif
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-gray-700">
            @forelse ($paginatedUsuarios as $index => $row)
            <tr class="hover:bg-gray-50 transition">
                @foreach ($columnsToDisplay as $key => $label)
                <td class="px-3 py-2 border border-slate-200 align-middle {{ $colW[$key] ?? '' }}">
                    @if ($key === 'cuit empresa')
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
                            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span
                                x-text="!$wire.get(`localEmpresa.{{ $index }}`) ? 'Seleccione' : (function(){
                                                    let e = empresas.find(e => e.cuit == $wire.get(`localEmpresa.{{ $index }}`));
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
                        <div x-show="openDropdownEmpresaLocal" x-transition
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
                    @elseif ($key === 'tipo usuario')
                    <!-- Local: Select para Tipo Usuario (por fila) -->
                    <div class="relative"
                        x-data='{
                                open:false,
                                search:"",
                                dropdownStyles:"",
                                // opciones para tipo de usuario
                                opts:[{v:"2", t:"Usuario Administrador"}, {v:"1", t:"Usuario Común"}],
                                label(){
                                const v = $wire.get("local_tipo_usario.{{ $index }}");
                                if (v === null || typeof v === "undefined" || v === "") return "Seleccione";
                                const f = this.opts.find(o => String(o.v) === String(v));
                                return f ? f.t : "Seleccione";
                                },
                                filtered(){
                                return this.search.trim() === ""
                                    ? this.opts
                                    : this.opts.filter(o => o.t.toLowerCase().includes(this.search.toLowerCase()));
                                }
                            }'
                        @click.away="open = false">

                        <button type="button"
                            @click="open = !open; if(open){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span x-text="label()"></span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition
                            class="rounded-md bg-white shadow-lg z-50">
                            <div class="p-2">
                                <input type="text" x-model="search"
                                    @keydown.enter="open=false" @keydown.escape="open=false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar...">
                            </div>
                            <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <template x-for="o in filtered()" :key="o.v">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="$wire.set('local_tipo_usario.{{ $index }}', o.v); open=false; search=''">
                                        <span class="font-normal block" x-text="o.t"></span>
                                    </li>
                                </template>
                                <template x-if="filtered().length === 0">
                                    <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'supervisor_usuario')
                    @php
                    $absIndex = ($paginatedUsuarios->firstItem() - 1) + $index;
                    $labelSup = $vistaPreviaUsuario[$absIndex]['supervisor_usuario'] ?? '';
                    @endphp
                    <div class="relative"
                        x-data='{
        open:false, search:"", dropdownStyles:"",
        opts: @json($supervisores),
        get filtered(){
            const s = this.search.toLowerCase();
            return s ? this.opts.filter(o => (o.name + " " + (o.cuil ?? "")).toLowerCase().includes(s))
                     : this.opts;
        }
     }'
                        @click.away="open=false">
                        <button type="button"
                            @click="open = !open; if(open){ const r = $el.getBoundingClientRect(); dropdownStyles = `position:fixed;top:${r.bottom}px;left:${r.left}px;width:${r.width}px;`; }"
                            class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="{{ $labelSup ? 'text-gray-800' : 'text-gray-500 italic' }}">
                                {{ $labelSup ?: 'Seleccione ' }}
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div x-show="open" x-transition class="rounded-md bg-white shadow-lg z-50">
                            <div class="p-2">
                                <input type="text" x-model="search" @keydown.enter="open=false" @keydown.escape="open=false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar supervisor...">
                            </div>
                            <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                    @click="open=false; $wire.setSupervisorLocal({{ $absIndex }}, null); search=''">
                                    <span class="block">Ninguno</span>
                                </li>
                                <template x-for="o in filtered" :key="o.id">
                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                        @click="open=false; $wire.setSupervisorLocal({{ $absIndex }}, o.id); search=''">
                                        <span class="block" x-text="o.name + (o.cuil ? ' ('+o.cuil+')' : '')"></span>
                                    </li>
                                </template>
                                <template x-if="filtered.length === 0">
                                    <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @elseif ($key === 'depende_de')
                    @php
                    // índice ABSOLUTO dentro de vistaPreviaUsuario
                    $absIndex = ($paginatedUsuarios->firstItem() - 1) + $index;

                    // lee SIEMPRE desde la colección original por el índice absoluto
                    $depId = $vistaPreviaUsuario[$absIndex]['depende_de'] ?? null;

                    // prioriza el nombre seteado por el modal; si no hay, mapea por id
                    $depNombre = $vistaPreviaUsuario[$absIndex]['depende_de_nombre']
                    ?? ($depId ? ($nivelesEmpresa[(int)$depId] ?? $nivelesEmpresa[(string)(int)$depId] ?? null) : null);

                    $payload = [
                    'origen' => 'usuarios_masiva',
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
    <div class="flex justify-first mt-2">
        <button wire:click="confirmarUsuarios"
            class="px-4 py-2 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Importar Usuarios
        </button>
    </div>
    <div class="flex justify-first mt-4 mb-20">
        {{ $paginatedUsuarios->links('livewire.custom-pagination') }}
    </div>
</div>
@endif