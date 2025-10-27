@if (!empty($datoAtributo))
    <div class="mb-8 space-y-6">
        <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-amber-600"></i> Atributos Obligatorios
        </h2>

        @php
            $atributoObligatorioSeleccionado = false;
        @endphp

        @foreach ($datoAtributo as $atributo)
            <div
                class="p-4 border border-gray-300 rounded-lg bg-white shadow-sm hover:shadow-md transition-shadow space-y-4">
                <!-- Checkbox de activación -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model.live="selectedAtributos.{{ $atributo->id_atributo }}"
                        id="atributo-{{ $atributo->id_atributo }}"
                        class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                    <label for="atributo-{{ $atributo->id_atributo }}" class="ml-3 text-base font-medium text-gray-700">
                        {{ $atributo->nombre }}
                    </label>
                </div>

                <!-- Opciones disponibles si está activo -->
                @if (!empty($selectedAtributos[$atributo->id_atributo]))
                    @php
                        $atributoObligatorioSeleccionado = true;
                    @endphp

                    <!-- Predefinido -->
                    @if (isset($atributoDefinid[$atributo->id_atributo]) && strtolower($atributoDefinid[$atributo->id_atributo]) === 'si')
                        <div class="ml-2 space-y-2">
                            <h3 class="text-sm font-semibold text-gray-600">
                                Opciones para {{ $atributo->nombre }}
                            </h3>

                            @if (isset($AtributoMultiple[$atributo->id_atributo]) && strtolower($AtributoMultiple[$atributo->id_atributo]) === 'si')
                                <!-- Opciones múltiples -->
                                <div class="flex flex-wrap gap-4">
                                    @foreach ($atributosValores[$atributo->id_atributo] as $valor)
                                        <div wire:key="atributo-{{ $atributo->id_atributo }}-valor-{{ $valor->id_valor }}"
                                            class="flex items-center">
                                            <input type="checkbox"
                                                wire:model="atributosSeleccionadosValoresCheckboxes.{{ $atributo->id_atributo }}.{{ $valor->id_valor }}"
                                                value="{{ $valor->id_valor }}"
                                                id="atributo-{{ $atributo->id_atributo }}-valor-{{ $valor->id_valor }}"
                                                class="h-4 w-4 text-indigo-500 border-gray-300 rounded focus:ring-indigo-500" />
                                            <label
                                                for="atributo-{{ $atributo->id_atributo }}-valor-{{ $valor->id_valor }}"
                                                class="ml-2 text-sm text-gray-700">
                                                {{ $valor->valor }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Select único -->
                                <select wire:model="atributosSeleccionadosValoresSelects.{{ $atributo->id_atributo }}"
                                    class="mt-2 block w-full border border-gray-300 rounded-md px-4 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($atributosValores[$atributo->id_atributo] as $valor)
                                        <option value="{{ $valor->id_valor }}">{{ $valor->valor }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @else
                        <!-- No predefinido: campos manuales según tipo_campo -->
                        <div class="space-y-4">
                            @switch($atributo->tipo_campo)
                                @case(1)
                                    <!-- Texto -->
                                    <div>
                                        <x-label value="Campo {{ $atributo->nombre }}" />
                                        <x-input type="text" wire:model.lazy="campo.{{ $atributo->id_atributo }}"
                                            class="w-full mt-1" />
                                        <x-input-error for="campo.{{ $atributo->id_atributo }}" />
                                    </div>
                                @break

                                @case(2)
                                    <!-- Numérico -->
                                    <div>
                                        <x-label value="Campo Numérico {{ $atributo->nombre }}" />
                                        <x-input type="number" wire:model.lazy="campo_numerico.{{ $atributo->id_atributo }}"
                                            class="w-full mt-1" />
                                        <x-input-error for="campo_numerico.{{ $atributo->id_atributo }}" />
                                    </div>
                                @break

                                @case(3)
                                    <!-- Fecha hasta hoy -->
                                    <div>
                                        <x-label value="{{ $atributo->nombre }}" />
                                        <input type="date" max="{{ now()->format('Y-m-d') }}"
                                            wire:model.lazy="fecha.{{ $atributo->id_atributo }}"
                                            class="mt-1 block w-full border border-gray-300 rounded-md px-4 py-2" />
                                        <x-input-error for="fecha.{{ $atributo->id_atributo }}" />
                                    </div>
                                @break

                                @case(4)
                                    <!-- Fecha desde hoy -->
                                    <div>
                                        <x-label value="{{ $atributo->nombre }}" />
                                        <input type="date" min="{{ now()->format('Y-m-d') }}"
                                            wire:model.lazy="fecha.{{ $atributo->id_atributo }}"
                                            class="mt-1 block w-full border border-gray-300 rounded-md px-4 py-2" />
                                        <x-input-error for="fecha.{{ $atributo->id_atributo }}" />
                                    </div>
                                @break

                                @default
                                    <div class="text-red-500">Tipo de campo desconocido</div>
                            @endswitch
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

        @if (!$atributoObligatorioSeleccionado)
            <div class="text-red-500 text-sm font-semibold">
                Debe cargar los datos de los atributos obligatorios.
            </div>
        @endif
    </div>
@endif

@if ($errors->has('step2'))
    <div class="text-red-500 font-semibold mt-2">{{ $errors->first('step2') }}</div>
@endif
