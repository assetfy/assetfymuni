<div id="editar-bien">
    <x-dialog-modal wire:model.live="open" maxWidth="2xl">
        <x-slot name="title">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold">Editar Bien</h2>
                <button wire:click="close" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Nombre del Activo -->
            <div class="mb-4 flex items-center">
                <x-label class="mr-4">
                    <span class="flex items-center">
                        <i class="fas fa-tag text-green-500 mr-2"></i> Nombre del Activo
                    </span>
                </x-label>
                @if ($editMode)
                <x-input type="text" wire:model.defer="upnombre"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" />
                <x-input-error for="upnombre" />
                @else
                <p class="text-gray-700">{{ $nombreActivo }}</p>
                @endif
            </div>

            <!-- Imagen del Activo -->
            <div class="flex justify-center items-center mb-6">
                <div class="text-center">
                    @if ($editMode)
                    <div class="flex justify-center gap-2 flex-wrap mt-4">
                        @foreach ($imagenes as $i => $url)
                        <div class="image-wrapper">
                            <img src="{{ $url }}" alt="Imagen {{ $i + 1 }}">
                            <button type="button" wire:click="eliminarImagenBD('{{ $url }}')"
                                class="delete-btn">✖</button>
                        </div>
                        @endforeach
                        @if ($updatedImagenes && count($updatedImagenes) > 0)
                        @foreach ($updatedImagenes as $index => $imagenTmp)
                        <div class="image-wrapper">
                            <img src="{{ $imagenTmp->temporaryUrl() }}" alt="Vista previa">
                            <button type="button" wire:click="eliminarImagenTemp({{ $index }})"
                                class="delete-btn">✖</button>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <x-input type="file" wire:model="updatedImagenes" multiple class="mt-4" />
                    <x-input-error for="updatedImagenes.*" />
                    @else
                    <div class="flex justify-center gap-10 flex-wrap mt-4">
                        <div wire:ignore.self class="flex flex-col items-center">
                            <x-label value="Imagen del Bien" />
                            @if (count($imagenes))
                            <div x-data='{ current: 0, images: @json($imagenes) }'
                                class="relative mt-2" style="width: 150px;">
                                <div class="overflow-hidden rounded-lg border border-gray-300 mx-auto"
                                    style="width: 150px; height: 150px;">
                                    <template x-for="(img, index) in images" :key="index">
                                        <img x-show="current === index" :src="img"
                                            alt="Imagen del activo"
                                            class="w-full h-full object-cover transition-opacity duration-500" />
                                    </template>
                                </div>
                                @if (count($imagenes) > 1)
                                <div class="flex justify-center mt-2 space-x-2">
                                    <button @click="current = (current - 1 + images.length) % images.length"
                                        class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400 text-xs">&lt;</button>
                                    <button @click="current = (current + 1) % images.length"
                                        class="px-2 py-1 bg-gray-300 rounded hover:bg-gray-400 text-xs">&gt;</button>
                                </div>
                                @endif
                            </div>
                            @else
                            <div class="overflow-hidden rounded-lg border border-gray-300 mx-auto mt-2 flex items-center justify-center text-gray-500 text-sm"
                                style="width: 150px; height: 150px;">
                                No hay imagen disponible
                            </div>
                            @endif
                        </div>

                        <!-- QR -->
                        <div class="flex flex-col items-center">
                            <x-label value="QR del Bien" />
                            <div class="mt-2 border border-gray-300 rounded p-2">
                                <img src="{{ $qrUrl }}" alt="Código QR del activo"
                                    class="w-40 h-40 object-contain">
                            </div>
<button onclick="printMode('individual')" class="print-btn mt-3">IMPRIMIR</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div id="editar-bien-tabs">
                <ul class="tabs-nav">
                    <li>
                        <button wire:click="$set('activeTab', 'detalles')"
                            class="tab-detalles {{ $activeTab === 'detalles' ? 'tab-active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span class="tab-label">Detalles</span>
                        </button>
                    </li>
                    <li>
                        <button wire:click="$set('activeTab', 'atributos')"
                            class="tab-atributos {{ $activeTab === 'atributos' ? 'tab-active' : '' }}">
                            <i class="fas fa-tag"></i>
                            <span class="tab-label">Atributos</span>
                        </button>
                    </li>
                    @if (!$inmueble)
                    <li>
                        <button wire:click="$set('activeTab', 'garantia')"
                            class="tab-garantia {{ $activeTab === 'garantia' ? 'tab-active' : '' }}">
                            <i class="fas fa-shield-alt"></i>
                            <span class="tab-label">Garantía</span>
                        </button>
                    </li>
                    <li>
                        <button wire:click="$set('activeTab', 'Unidad_Responsable')"
                            class="tab-org {{ $activeTab === 'Unidad_Responsable' ? 'tab-active' : '' }}">
                            <i class="fas fa-sitemap"></i>
                            <span class="tab-label">Organización</span>
                        </button>
                    </li>
                    @endif
                </ul>
            </div>

            <!-- Tab Contents -->
            <div class="{{ $activeTab === 'detalles' ? '' : 'hidden' }}">
                @include('livewire.activos.partials.editarDetalles')
            </div>
            <div class="{{ $activeTab === 'atributos' ? '' : 'hidden' }}">
                @include('livewire.activos.partials.EditarAtributos')
            </div>
            <div class="{{ $activeTab === 'garantia' ? '' : 'hidden' }}">
                @include('livewire.activos.partials.EditarGarantia')
            </div>
            <div class="{{ $activeTab === 'Unidad_Responsable' ? '' : 'hidden' }}">
                @include('livewire.activos.partials.EditarUnidadResponsable')
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-4 mt-6">
                @if (!$responsable)
                <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
                @else
                @if (!$editMode)
                <x-secondary-button wire:click="toggleEditMode">Editar</x-secondary-button>
                <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
                @else
                <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
                <x-danger-button wire:click="actualizar" wire:loading.attr="disabled">Guardar
                    Cambios</x-danger-button>
                @endif
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>

    @php
    $logoEmp = !empty($qr?->foto) ? Storage::disk('s3')->temporaryUrl($qr->foto, now()->addMinutes(10)) : null;
    $textoEmp = $qr?->texto;
    $hasExtras = filled($logoEmp) || filled($textoEmp);

    $hEtiqueta = $hasExtras ? '85mm' : '70mm'; // sin extras: más baja
    $space = $hasExtras ? '1.8mm' : '1.4mm';
    @endphp

    <div class="print-only-qr hidden print:block">
        <x-qr.label :qr-src="$qrUrl" :logo-url="$logoEmp" :text="$textoEmp" :asset-name="$nombreActivo" w="70mm"
            h="{{ $hEtiqueta }}" padding="4mm" space="{{ $space }}" qr-size="42mm" :border="true" />
    </div>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/edit-activos.css') }}">
</div>