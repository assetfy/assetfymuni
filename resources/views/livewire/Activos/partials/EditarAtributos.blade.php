<br>
<div class="mb-6">
    <x-label>
        <span class="flex items-center text-sm font-bold text-gray-700 mb-1">
            <i class="fas fa-box text-orange-400 mr-2"></i>
            Estado Inventario
        </span>
    </x-label>
    <div class="mb-2">
        <p class="text-sm text-gray-800">{{ $upestado_inventario }}</p>
    </div>
    <br>
    <!-- Estado Alta -->
    <div class="mb-6">
        <x-label>
            <span class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-check-circle text-red-500 mr-2"></i>
                Estado Alta
            </span>
        </x-label>
        @if ($editMode)
        <select wire:model.live="id_estado_sit_alta"
            class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            <option value="">Seleccione estado alta</option>
            @if ($altas)
            @foreach ($altas as $alta)
            <option value="{{ $alta->id_estado_sit_alta }}">{{ $alta->nombre }}</option>
            @endforeach
            @endif
        </select>
        <x-input-error for="id_estado_sit_alta" />
        @else
        @if ($altas)
        <p class="text-sm text-gray-800">

            {{ $alta }}

        </p>
        @endif
        @endif
    </div>
</div>
<!-- Tipo de Propiedad -->
<div class="mb-6">
    <x-label>
        <span class="flex items-center text-sm font-bold text-gray-700 mb-1">
            <i class="fas fa-home text-orange-400 mr-2"></i>
            Tipo de Propiedad
        </span>
    </x-label>
    <p class="text-sm text-gray-800">{{ $propietario }}</p>
    <br>
    <!-- Comentarios Alta -->
    <div class="mb-6">
        <x-label>
            <span class="flex items-center text-sm font-semibold text-gray-700 mb-1">
                <i class="fas fa-comment-alt text-blue-500 mr-2"></i>
                Comentarios Alta
            </span>
        </x-label>
        <p class="text-sm text-gray-800">{{ $upcomentarios_sit_alta ?: 'Sin comentario' }}</p>
    </div>
</div>
<!-- Identificación Técnica -->
@if (!$inmueble)
<div class="mb-6">
    <x-label>
        <span class="flex items-center text-sm font-semibold text-gray-700 mb-2">
            <i class="fas fa-microchip text-purple-500 mr-2"></i>
            Identificación Técnica
            <span class="text-gray-400 ml-2">(opcional)</span>
        </span>
    </x-label>
    <!-- Número de Serie -->
    <div class="mb-3">
        <x-label class="text-sm text-gray-600">Número de serie / Patente</x-label>
        @if ($editMode)
        <x-input type="text" wire:model.defer="upNmroSerie"
            class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500" />
        <x-input-error for="upNmroSerie" />
        @else
        <p class="text-gray-800">
            {{ $upNmroSerie ?: 'Sin número de serie' }}
        </p>
        @endif
    </div>
    <!-- Marca -->
    <div class="mb-3">
        <x-label class="text-sm text-gray-600">
            Marca <span class="text-gray-400 ml-2">(opcional)</span>
        </x-label>

        @if ($editMode)
        <select wire:model.live="id_marca" wire:change="actualizarModelos"
            class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            <option value="">Seleccione una marca</option>
            @foreach ($ListaMarcas as $marca)
            <option value="{{ $marca->id_marca }}">{{ $marca->nombre }}</option>
            @endforeach
        </select>
        <x-input-error for="id_marca" />
        @else
        @php
        $marcaNombre = null;

        if (optional($activo)->id_modelo) {
        $modeloSel = collect($ListaModelos)->firstWhere('id_modelo', $activo->id_modelo);
        if ($modeloSel && $modeloSel->id_marca) {
        $marcaSel = collect($ListaMarcas)->firstWhere('id_marca', $modeloSel->id_marca);
        $marcaNombre = $marcaSel->nombre ?? null;
        }
        }
        @endphp

        <p class="text-gray-800">
            {{ $marcaNombre ?? 'Sin datos' }}
        </p>
        @endif
    </div>
    <!-- Modelo -->
    <div>
        <x-label class="text-sm text-gray-600">
            Modelo <span class="text-gray-400 ml-2">(opcional)</span>
        </x-label>
        @if ($editMode)
        <select wire:model="id_modelo"
            class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500"
            @disabled(!$id_marca)>
            <option value="">Seleccione un modelo</option>
            @foreach ($ListaModelos as $modelo)
            <option value="{{ $modelo->id_modelo }}">{{ $modelo->nombre }}</option>
            @endforeach
        </select>
        <x-input-error for="id_modelo" />
        @else
        <p class="text-gray-800">
            @php $encontrado = false; @endphp
            @foreach ($ListaModelos as $modelo)
            @if (optional($activo)->id_modelo && $modelo['id_modelo'] == $activo->id_modelo)
            {{ $modelo['nombre'] }}
            @php $encontrado = true; @endphp
            @endif
            @endforeach
            @if (!$encontrado)
            Sin datos
            @endif
        </p>
        @endif
    </div>
</div>
@endif
@if ($editMode)
<div class="flex justify-end mt-4">
    <button wire:click="abrirModalAtributos"
        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
        Cargar / Editar atributos
    </button>
</div>
@endif
<!-- TABLA DE ATRIBUTOS - AL FINAL -->
@if ($atributos && $atributos->count())
<div class="overflow-x-auto mt-10">
    <table class="min-w-full border border-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left font-medium">Nombre</th>
                <th class="px-4 py-2 text-left font-medium">Unidad de Medida</th>
                <th class="px-4 py-2 text-left font-medium">Tipo de Campo</th>
                <th class="px-4 py-2 text-left font-medium">Valor Asociado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($atributos as $atributo)
            <tr class="border-t">
                <td class="px-4 py-2 font-semibold text-gray-800">
                    {{ $atributo->atributo->nombre ?? 'n/a' }}
                </td>
                <td class="px-4 py-2">
                    {{ $atributo->atributo->unidadMedida->nombre ?? '-' }}
                </td>
                <td class="px-4 py-2">
                    {{ $atributo->atributo->tiposCampos->nombre ?? '-' }}
                </td>
                <td class="px-4 py-2 font-medium text-indigo-700">
                    @if ($atributo->campo)
                    {{ $atributo->campo }}
                    @elseif($atributo->campo_numerico)
                    {{ $atributo->campo_numerico }}
                    @elseif($atributo->fecha)
                    {{ \Carbon\Carbon::parse($atributo->fecha)->format('d/m/Y') }}
                    @elseif($atributo->campo_enum)
                    {{ $atributo->campo_enum }}
                    @elseif($atributo->campo_enum_list)
                    <ul class="list-disc pl-4 text-gray-800">
                        @foreach (explode(',', $atributo->campo_enum_list) as $valor)
                        <li>{{ trim($valor) }}</li>
                        @endforeach
                    </ul>
                    @else
                    <span class="text-gray-400 italic">n/a</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-gray-500 mt-4">Sin atributos registrados.</p>
@endif


@if($openModalAtributos)
<div x-data x-show="true" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-2xl shadow-lg">
        <h2 class="text-lg font-semibold mb-4">Editar atributos</h2>

        @foreach($atributosDisponibles as $idAtributo => $atributo)
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $atributo['nombre'] }}
            </label>

            {{-- Si el atributo es predefinido --}}
            {{-- Si el atributo es predefinido → SIEMPRE SELECT --}}
            @if($atributo['predefinido'])
            @php($opciones = array_combine($atributo['ids'], $atributo['valores']))

            {{-- Usamos un único <select>. Si el atributo admite múltiples,
       igual mandamos un único valor (ID) para evitar selecciones múltiples. --}}
            <select
                wire:model="atributosDatos.{{ $idAtributo }}"
                class="w-full border rounded px-2 py-1">
                <option value="" disabled hidden>-- Seleccionar --</option>
                @foreach($opciones as $idValor => $valor)
                <option value="{{ $idValor }}">{{ $valor }}</option>
                @endforeach
            </select>

            {{-- (opcional) mostrar error si querés validar del lado Livewire --}}
            @if(!empty($erroresAtributos[$idAtributo]))
            <p class="mt-1 text-sm text-red-600">{{ $erroresAtributos[$idAtributo] }}</p>
            @endif
            @else
            {{-- No predefinido: campo manual según tipo --}}
            @if($atributo['tipo'] === 'Numerico')
            <input type="text"
                wire:model="atributosDatos.{{ $idAtributo }}"
                class="w-full border rounded px-2 py-1"
                inputmode="numeric"
                pattern="[0-9]*"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            @elseif($atributo['tipo'] === 'Fecha')
            <input type="date" wire:model="atributosDatos.{{ $idAtributo }}"
                class="w-full border rounded px-2 py-1">
            @else
            <input type="text" wire:model="atributosDatos.{{ $idAtributo }}"
                class="w-full border rounded px-2 py-1">
            @endif
            @endif
        </div>
        @endforeach

        <div class="flex justify-end space-x-2 mt-6">
            <button wire:click="cerrarModalAtributos"
                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">
                Cancelar
            </button>
            <button wire:click="guardarAtributos"
                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                Guardar
            </button>
        </div>
    </div>
</div>
@endif