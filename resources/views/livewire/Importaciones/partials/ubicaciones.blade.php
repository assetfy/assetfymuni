 @if (!empty($previewDataUbicacion))

 @php
 $colW = [
 'nombre' => 'w-40',
 'pais' => 'w-32',
 'provincia' => 'w-36',
 'ciudad' => 'w-36',
 'codigo_postal' => 'w-28',
 'calle' => 'w-40',
 'altura' => 'w-24',
 'tipo_de_ubicacion' => 'min-w-[14rem] md:min-w-[14rem] xl:min-w-[14rem]',
 'id_externo' => 'w-28',
 'multiples pisos' => 'w-40 md:w-48 lg:w-56',
 'subsuelo' => 'w-36 md:w-40',
 'piso' => 'w-36 md:w-40'];

 $extraColumnsBienes = [
 'cuit_propietario' => 'min-w-[24rem] md:min-w-[24rem] xl:min-w-[18rem]',
 ];
 @endphp

 @php
 $columnsToDisplay = [
 'nombre' => 'Nombre',
 'pais' => 'País',
 'provincia' => 'Provincia',
 'ciudad' => 'Ciudad',
 'codigo_postal' => 'Código Postal',
 'calle' => 'Calle',
 'altura' => 'Altura',
 'tipo_de_ubicacion' => 'Tipo de Ubicación',
 'id_externo' => 'ID Externo',
 'multiples pisos' => 'Múltiples Pisos',
 'subsuelo' => 'Subsuelo',
 'piso' => 'Piso',
 ];

 $extraColumnsBienes = [
 'cuit_propietario' => 'CUIT Propietario',
 ];
 @endphp

 <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
     <h4 class="font-semibold text-lg flex items-center">
         <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
     </h4>

     <!-- Tabla con borde -->
     <table class="min-w-full table-fixed border-collapse mt-2 border border-gray-300">
         <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
             <tr>
                 @foreach ($columnsToDisplay as $key => $label)
                 <th class="px-3 py-2 font-medium border border-slate-200 {{ $colW[$key] ?? '' }}">
                     {{ $label }}
                     @if ($key === 'tipo_de_ubicacion')
                     <!-- Global: Select de Tipo de Ubicación con búsqueda -->
                     <div class="relative"
                         x-data='{
                                         openDropdownTipo: false,
                                         searchTipo: "",
                                         tipos: @json($tipos),
                                         get filteredTipos() {
                                             return this.tipos.filter(item => item.nombre.toLowerCase().includes(this.searchTipo.toLowerCase()))
                                         }
                                     }'
                         @click.away="openDropdownTipo = false">
                         <button type="button"
                             @click="openDropdownTipo = !openDropdownTipo; if(openDropdownTipo){ const rect = $el.getBoundingClientRect(); dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; }"
                             class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                             <span
                                 x-text="!$wire.get('globalTipoUbicacion') ? 'Seleccione' : '{{ $tipos->firstWhere('id_tipo', $globalTipoUbicacion ?? null)->nombre ?? 'Seleccione' }}'"></span>
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
                         <div x-show="openDropdownTipo" x-transition
                             class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                             <div class="p-2">
                                 <input type="text" x-model="searchTipo"
                                     @keydown.enter="openDropdownTipo = false"
                                     @keydown.escape="openDropdownTipo = false"
                                     class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                     placeholder="Buscar Tipo...">
                             </div>
                             <ul
                                 class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                 <template x-for="tipo in filteredTipos" :key="tipo.id_tipo">
                                     <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                         @click="openDropdownTipo = false; $wire.set('globalTipoUbicacion', tipo.id_tipo); searchTipo = ''">
                                         <span class="font-normal block" x-text="tipo.nombre"></span>
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

                         @elseif ($key === 'multiples pisos')
                         <!-- Global: Select para Tipo Usuario -->
                         <div class="relative"
                             x-data='{
        open:false,
        search:"",
        dropdownStyles:"",
        opts:[ {v:"1", l:"Sí"}, {v:"0", l:"No"} ],
        get filtered(){ 
            return this.opts.filter(o => o.l.toLowerCase().includes(this.search.toLowerCase())) 
        },
        label(){
          const v = String($wire.get("global_multiples") ?? "");
          const o = this.opts.find(x => x.v === v);
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

                             <div x-show="open" x-transition :style="dropdownStyles"
                                 class="rounded-md bg-white shadow-lg z-20">
                                 <div class="p-2">
                                     <input type="text" x-model="search"
                                         @keydown.enter="open=false" @keydown.escape="open=false"
                                         class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                         placeholder="Buscar...">
                                 </div>
                                 <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                     <template x-for="o in filtered" :key="o.v">
                                         <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                             @click="open=false; 
                    $wire.set('global_multiples', o.v); 
                    Livewire.dispatch('updateAllLocalMultiples', [o.v]); 
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
                         @endif
                         @endforeach

                         @if ($tipoDatos === 'UbicacionesUsuarios')
                         {{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                         @foreach ($extraColumnsBienes as $key => $label)
                 <th class="px-3 py-2 font-medium border border-slate-200 {{ $colW[$key] ?? '' }}">
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
                 </th>
             </tr>
         </thead>
         <tbody class="text-gray-700">
             @forelse ($paginatedUbicaciones as $index => $row)
             <tr class="hover:bg-gray-50 transition">
                 @foreach ($columnsToDisplay as $key => $label)
                 <td class="px-3 py-2 border border-slate-200 align-middle {{ $colW[$key] ?? '' }}">
                     @if ($key === 'tipo_de_ubicacion')
                     <!-- Local: Select de Tipo de Ubicación con búsqueda -->
                     <div class="relative"
                         x-data='{
                                             openDropdownTipoRow: false,
                                             searchTipoRow: "",
                                             tipos: @json($tipos),
                                             dropdownStyles: "",
                                             get filteredTipos() {
                                                 return this.tipos.filter(item => item.nombre.toLowerCase().includes(this.searchTipoRow.toLowerCase()))
                                             }
                                         }'
                         @click.away="openDropdownTipoRow = false">
                         <button type="button"
                             @click="openDropdownTipoRow = !openDropdownTipoRow; if(openDropdownTipoRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                             class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                             <span
                                 x-text="!$wire.get('localTipoUbicacion.{{ $index }}') ? 'Seleccione' : '{{ $tipos->firstWhere('id_tipo', $localTipoUbicacion[$index] ?? null)->nombre ?? 'Seleccione' }}'"></span>
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
                         <div x-show="openDropdownTipoRow" x-transition
                             class="rounded-md bg-white shadow-lg z-10">
                             <div class="p-2">
                                 <input type="text" x-model="searchTipoRow"
                                     @keydown.enter="openDropdownTipoRow = false"
                                     @keydown.escape="openDropdownTipoRow = false"
                                     class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                     placeholder="Buscar Tipo...">
                             </div>
                             <ul
                                 class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                 <template x-for="tipo in filteredTipos" :key="tipo.id_tipo">
                                     <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                         @click="openDropdownTipoRow = false; $wire.set('localTipoUbicacion.{{ $index }}', tipo.id_tipo); searchTipoRow = ''">
                                         <span class="font-normal block"
                                             x-text="tipo.nombre"></span>
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

                     @elseif ($key === 'multiples pisos')
                     <!-- Local: Select de Múltiples Pisos con el mismo dropdown que Tipo de Ubicación -->
                     <div class="relative"
                         x-data='{
        open:false,
        search:"",
        dropdownStyles:"",
        opts:[{v:"1", l:"Si"}, {v:"0", l:"No"}],
        get filtered(){ return this.opts.filter(o => o.l.toLowerCase().includes(this.search.toLowerCase())) },
        label(){
          const v = $wire.get("local_multiples.{{ $index }}");
          if (v === null || typeof v === "undefined" || v === "") return "Seleccione";
          return String(v) === "1" ? "Si" : "No";
        }
     }'
                         @click.away="open=false">

                         <button type="button"
                             @click="open = !open;
                  if(open){
                    const r = $el.getBoundingClientRect();
                    dropdownStyles = `position:fixed;top:${r.bottom}px;left:${r.left}px;width:${r.width}px;`;
                  }"
                             class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                             <span x-text="label()"></span>
                             <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                 <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                 </svg>
                             </span>
                         </button>

                         <div x-show="open" x-transition
                             class="rounded-md bg-white shadow-lg z-10">
                             <div class="p-2">
                                 <input type="text" x-model="search"
                                     @keydown.enter="open=false" @keydown.escape="open=false"
                                     class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                     placeholder="Buscar...">
                             </div>
                             <ul class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                 <template x-for="o in filtered" :key="o.v">
                                     <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                         @click="$wire.set('local_multiples.{{ $index }}', o.v); open=false; search=''">
                                         <span class="block" x-text="o.l"></span>
                                     </li>
                                 </template>
                                 <template x-if="filtered.length === 0">
                                     <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                 </template>
                             </ul>
                         </div>
                     </div>

                     @elseif ($key === 'subsuelo')
                     <input type="number"
                         wire:model="localSubsuelo.{{ $index }}"
                         min="0"
                         class="w-full min-w-[9rem] md:min-w-[10rem] px-3 py-2.5 border rounded-md appearance-none text-base focus:ring-1 focus:ring-blue-500"
                         :disabled="$wire.local_multiples[{{ $index }}] !== '1'"
                         placeholder="Subsuelo" />

                     @elseif ($key === 'piso')
                     <input type="number"
                         wire:model="localPiso.{{ $index }}"
                         min="0"
                         class="w-full min-w-[9rem] md:min-w-[10rem] px-3 py-2.5 border rounded-md appearance-none text-base focus:ring-1 focus:ring-blue-500"
                         :disabled="$wire.local_multiples[{{ $index }}] !== '1'"
                         placeholder="Piso" />

                     @else
                     {{ $row[$key] ?? '' }}
                     @endif
                 </td>
                 @endforeach

                 @if ($tipoDatos === 'UbicacionesUsuarios')
                 {{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                 @foreach ($extraColumnsBienes as $key => $label)
                 <td class="px-3 py-2 border border-slate-200 align-middle {{ $colW[$key] ?? '' }}">
                     @if ($key === 'cuit_propietario')
                     <!-- Local: Dropdown para selección de Empresa en cada fila -->
                     <div class="relative"
                         x-data='{
                                                openDropdownEmpresaLocal: false,
                                                searchEmpresaLocal: "",
                                                dropdownStyles: "",
                                                empresas: @json($empresasClientes),
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
     @if ($tipoDatos == 'UbicacionesPropias')
     <div class="flex justify-first mt-2">
         <button wire:click="confirmarImportubicacionesPropias"
             class="px-4 py-2 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
             <i class="fas fa-save"></i> Importar Ubicaciones
         </button>
     </div>
     @else
     <div class="flex justify-first mt-2">
         <button wire:click="confirmarImportubicacionesPropias"
             class="px-4 py-2 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
             <i class="fas fa-save"></i> Importar Ubicaciones
         </button>
     </div>
     @endif
     <div class="flex justify-first mt-4 mb-20">
         {{ $paginatedUbicaciones->links('livewire.custom-pagination') }}
     </div>
 </div>
 @endif