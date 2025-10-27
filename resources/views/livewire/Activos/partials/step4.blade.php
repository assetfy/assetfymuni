<!-- ESTADO DEL BIEN -->
@if(!$inmueble)
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-green-600"></i> Estado del Bien
    </h2>

    <div class="mb-4">
        <x-label value="Estado de Alta" />
        <div class="relative">
            <i
                class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            <select wire:model="id_estado_sit_alta"
                class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="" hidden>Seleccione el Estado de Alta</option>
                @foreach ($altas as $estado)
                    <option value="{{ $estado->id_estado_sit_alta }}">{{ $estado->nombre }}</option>
                @endforeach
            </select>
        </div>
        <x-input-error for="id_estado_sit_alta" />
    </div>

    <div class="mb-4">
        <x-label value="Descripción / Situación de alta (Opcional)" />
        <div class="relative">
            <i
                class="fa-regular fa-pen-to-square absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            <x-input type="text"
                class="mt-1 block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                wire:model.lazy="comentarios_sit_alta" />
        </div>
        <x-input-error for="comentarios_sit_alta" />
    </div>
    @if ($errors->has('step3'))
        <div class="text-red-500 font-semibold">{{ $errors->first('step3') }}</div>
    @endif
</div>
@endif

<!-- TIPO DE PROPIEDAD -->
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-house-user text-yellow-600"></i> Tipo de Propiedad
    </h2>

    <div class="mb-4">
        <x-label value="Tipo de Propiedad" />
        @if ($origen != 'bienes_aceptados')
            @if (auth()->user()->panel_actual == 'Usuario')
                <div class="relative">
                    <i class="fa-solid fa-user-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <x-input type="text"
                        class="mt-1 block w-full bg-gray-100 border border-gray-300 text-gray-700 py-2 px-4 pl-10 rounded-md"
                        value="Propio" disabled />
                </div>
                <x-input type="hidden" wire:model="propietario" value="Propio" />
            @else
                <div class="space-y-4">
                    {{-- Tipo de Propiedad --}}
                    <div class="relative">
                        <label class="sr-only" for="propietario">Tipo de Propiedad</label>
                        <span
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <select id="propietario"
                            class="mt-1 block w-full appearance-none pl-10 pr-10 bg-white border border-gray-300 text-gray-700 py-2.5 rounded-lg leading-tight shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                            wire:model.lazy="propietario">
                            <option value="" hidden>Seleccione un Tipo de Propiedad</option>
                            <option value="Propio">Propio</option>
                            <option value="tercero">De terceros</option>
                        </select>
                        <span
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </div>

                    {{-- Tipo de tercero (solo si corresponde) --}}
                    @if ($propietario === 'tercero')
                        <div x-data="{ t: @entangle('tipo_tercero') }" {{-- enlaza con Livewire para icono dinámico --}} class="relative">
                            <label class="sr-only" for="tipo_tercero">Tipo de tercero</label>

                            {{-- Icono dinámico: usuario para Individuo, edificio para Empresa --}}
                            <span
                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fa-solid" :class="t === 'Empresa' ? 'fa-building' : 'fa-user'"></i>
                            </span>

                            <select id="tipo_tercero"
                                class="mt-1 block w-full appearance-none pl-10 pr-10 bg-white border border-gray-300 text-gray-700 py-2.5 rounded-lg leading-tight shadow-sm focus:outline-none focus:ring-4 focus:ring-indigo-500/30 focus:border-indigo-500 transition"
                                wire:model.live="tipo_tercero">
                                <option value="" hidden>Seleccione tipo de tercero</option>
                                <option value="Individuo">Individuo</option>
                                <option value="Empresa">Empresa</option>
                            </select>
                            <span
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                        <div x-data="{ t: @entangle('tipo_tercero') }" class="relative">
                            <x-label :value="($tipo_tercero === 'Empresa' ? 'Nombre de la Empresa' : ($tipo_tercero === 'Individuo' ? 'Nombre del Individuo' : 'Nombre del tercero'))" />
                            <span class="pointer-events-none absolute left-3 top-9 -translate-y-1/2 text-gray-400">
                                <i class="fa-solid" :class="t === 'Empresa' ? 'fa-building' : 'fa-user'"></i>
                            </span>
                            <x-input type="text"
                                class="mt-1 block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                wire:model.lazy="tercero_nombre" :placeholder="($tipo_tercero === 'Empresa' ? 'Ej.: ACME S.A.' : 'Ej.: Juan Pérez')" />
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="relative">
                <i class="fa-solid fa-people-arrows absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <x-input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 text-gray-700 py-2 px-4 pl-10 rounded-md"
                    value="Cliente" disabled />
            </div>
            <x-input type="hidden" wire:model="propietario" value="Cliente" />
        @endif
        <x-input-error for="propietario" />
    </div>
</div>

<!-- ESTADO GENERAL DEL BIEN -->
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-heart-pulse text-teal-600"></i> Estado General del Bien
    </h2>

    <div class="mb-4">
        <x-label value="Estado General" />
        <div class="relative">
            <i
                class="fa-solid fa-gauge-high absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            <select wire:model.live="id_estado_sit_general"
                class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="" hidden>Seleccione el estado general del bien</option>
                @foreach ($general as $generales)
                    <option value="{{ $generales->id_estado_sit_general }}">{{ $generales->nombre }}</option>
                @endforeach
            </select>
        </div>
        <x-input-error for="id_estado_sit_general" />
    </div>
    @if ($id_estado_sit_general == 5)
        <!-- Si el estado es "Prestado" -->
        <div class="mb-4">
            <x-label value="Prestado a" />
            <div class="relative">
                <i class="fa-solid fa-box absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <x-input type="text"
                    class="mt-1 block w-full px-4 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    wire:model.lazy="prestado" />
            </div>
            <x-input-error for="prestado" />
        </div>
    @endif
</div>
<!-- CONDICIÓN DEL BIEN -->
@if(!$inmueble)
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-wrench text-blue-600"></i> Condición del Bien
    </h2>

    <div class="mb-4">
        <x-label value="Condición" />
        <div class="relative">
            <i
                class="fa-solid fa-screwdriver-wrench absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            <select wire:model="id_condicion"
                class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="" hidden>Seleccione la condición del bien</option>
                @foreach ($condiciones as $condicion)
                    <option value="{{ $condicion->id_condicion }}">{{ $condicion->nombre }}</option>
                @endforeach
            </select>
        </div>
        <x-input-error for="id_condicion" />
    </div>
</div>
@endif

<!-- FOTOS DEL BIEN -->
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-camera-retro text-pink-500"></i> Adjuntar Fotos <span
            class="text-sm text-gray-500 font-normal">(Opcional)</span>
    </h2>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-2">Fotos:</label>
        <div class="flex flex-wrap gap-4">
            @foreach ($fotos as $index => $foto)
                <div wire:key="uploaded-photo-{{ $index }}"
                    class="w-32 h-32 bg-white rounded-lg shadow-md overflow-hidden relative">
                    <img src="{{ $foto->temporaryUrl() }}" class="object-cover w-full h-full">
                    <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1"
                        wire:click="removeFoto({{ $index }})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endforeach
            @foreach ($capturedPhotos as $index => $capturedPhoto)
                <div wire:key="captured-photo-{{ $index }}"
                    class="w-32 h-32 bg-white rounded-lg shadow-md overflow-hidden relative">
                    <img src="{{ $capturedPhoto }}" class="object-cover w-full h-full">
                    <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1"
                        wire:click="removeCapturedPhoto({{ $index }})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endforeach
            @if (count($fotos) + count($capturedPhotos) < 10)
                <div class="flex items-center justify-center w-32 h-32 bg-gray-100 rounded-lg cursor-pointer border-2 border-dashed border-gray-400 hover:border-indigo-500 hover:text-indigo-500 transition"
                    onclick="document.getElementById('foto').click()">
                    <i class="fa-solid fa-plus text-2xl"></i>
                </div>
            @endif
        </div>
        <input type="file" id="foto" multiple class="hidden" wire:model="nuevasFotos">
        @error('fotos.*')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
        <div wire:loading wire:target="nuevasFotos" class="text-blue-500 mt-2">Cargando fotos...</div>

        @if ($errors->has('step4'))
            <div class="text-red-500 mt-2">{{ $errors->first('step4') }}</div>
        @endif
    </div>
</div>
