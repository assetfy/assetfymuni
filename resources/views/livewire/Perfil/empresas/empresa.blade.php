<div class="container mx-auto px-4 py-8" x-data="{
    showUploadModal: false,
    showEditModal: false,
    showEditModalImagen: false,
    showPhoto: false,
    showPhotoProfile: false,
    imageToShow: '',
    selectedImage: '',
    originalDescripcion: '',
    descripcion: @entangle('updateDescripcion'),
}"
    @close-modals.window="
       showUploadModal = false;
       showEditModal = false;
       showEditModalImagen = false;
       showPhoto = false;
       showPhotoProfile = false;
     ">
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1 p-0">
            <div class="flex items-center gap-4">
                @if (isset($empresa) && $empresa->logo)
                    <img src="{{ Storage::disk('s3')->temporaryUrl($empresa->logo, now()->addMinutes(10)) }}"
                    alt="{{ optional($empresa)->razon_social }}" class="w-20 h-20 rounded-full object-cover" @else
                        <div
                        class="w-20 h-20 flex items-center justify-center text-xl font-semibold text-gray-800 bg-transparent">
                    @php
                        // Si $empresa o razon_social no existen, usamos cadena vacía
                        $razon = optional($empresa)->razon_social ?? '';
                        // Separar en palabras (si es cadena vacía, obtenemos [''])
                        $words = explode(' ', trim($razon));
                        // Calcular iniciales de forma segura
                        if (count($words) > 1) {
                            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                        } elseif (!empty($words[0])) {
                            $initials = strtoupper(substr($words[0], 0, 2));
                        } else {
                            $initials = '--';
                        }
                    @endphp

                    {{ $initials }}
            </div>
            @endif

            <div>
                <h3 class="text-2xl font-bold text-gray-900">
                    Empresa: {{ $empresa->razon_social }}
                </h3>
                <div class="mt-1">
                    @if ($empresa->logo)
                        <button wire:click="eliminarFoto" class="text-sm text-red-600 hover:underline">
                            Eliminar Logo
                        </button>
                    @else
                        <button @click="showPhotoProfile = true" class="text-sm text-blue-600 hover:underline">
                            Agregar Logo
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center space-x-4">
            <p class="font-semibold text-gray-800">
                Actividad Económica:
                <span class="font-normal">
                    {{ optional($empresa->actividades)->nombre ?? 'Sin actividad económica' }}
                </span>
            </p>
            @if (empty($empresa->COD_ACTIVIDAD))
                <button wire:click="asignarActividad"
                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    Asignar actividad
                </button>
            @endif
        </div>

        <div class="mt-6 mb-4">
            <p class="font-semibold text-gray-800">Descripción de la Empresa:</p>
            <div class="mt-2 text-gray-900 text-justify">
                {{ $empresa->descripcion_actividad ?: 'Sin descripción de actividad' }}
            </div>
            <div class="mt-2">
                <button @click="showEditModal = true; originalDescripcion = descripcion"
                    class="text-sm px-3 py-1 bg-black text-white rounded hover:opacity-80">
                    Editar Descripción
                </button>
            </div>
        </div>

        <!-- Contenedor de la imagen y los botones -->
        <div class="space-y-4 animate__animated animate__fadeIn">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @for ($i = 0; $i < 4; $i++)
                    @if (isset($imagenes[$i]) && $imagenes[$i])
                        <div class="custom-card">
                            <div class="custom-box">
                                {{-- Aquí sacamos asset/Storage::url y usamos Storage::disk('s3')->url() --}}
                                <img src="{{ Storage::disk('s3')->temporaryUrl($imagenes[$i], now()->addMinutes(10)) }}"
                                    alt="Imagen Empresa {{ $i + 1 }}" class="w-full h-full object-cover" />
                                <div class="custom-description">
                                    <button class="action-button-b mt-2"
                                        @click="
                                                showPhoto = true;
                                                imageToShow = '{{ Storage::disk('s3')->temporaryUrl($imagenes[$i], now()->addMinutes(10)) }}'
                                            ">
                                        Agrandar imagen
                                    </button>


                                    <button class="action-button-b mt-2"
                                        @click="showEditModalImagen = true; selectedImage = '{{ Storage::disk('s3')->url($imagenes[$i]) }}'"
                                        wire:click="fotoSeleccionada({{ $i }})">
                                        Editar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="custom-card">
                            <div class="sin-imagen">
                                <button @click="showUploadModal = true" class="action-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>
</div>

<!-- MODALES CON OVERLAY SEMITRANSPARENTE -->
<!-- Editar Descripción -->
<div @close-edit-modal.window="showEditModal = false">
    <template x-if="showEditModal">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg relative sm:w-2/3 lg:w-1/2 max-w-md">
                <button @click="showEditModal = false" class="absolute top-2 right-2 text-gray-600">&times;</button>
                <h2 class="font-bold mb-4">Editar Descripción</h2>
                <!-- Tamaño de textarea predeterminado -->
                <textarea x-model="descripcion" wire:model.defer="updateDescripcion" class="border p-2 rounded textarea-custom w-full"
                    rows="5" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px';" autofocus></textarea>

                @error('updateDescripcion')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
                <div class="mt-4 flex space-x-4">
                    <button wire:click="guardarDescripcion" class="action-button-b hover-effect"
                        :disabled="descripcion === originalDescripcion" x-show="descripcion !== originalDescripcion">
                        Guardar
                    </button>
                    <button @click="showEditModal = false" class="action-button-b hover-effect">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>


<!-- Modal para subir imagen -->
<div @close-edit-modal.window="showUploadModal = false; fileLoaded = false" x-data="{
    fileLoaded: false,
    isSmallScreen: window.innerWidth <= 768,
    checkScreenSize() { this.isSmallScreen = window.innerWidth <= 768; }
}"
    x-init="checkScreenSize();
    window.addEventListener('resize', checkScreenSize)">
    <template x-if="showUploadModal">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg relative w-full max-w-md">
                <h2 class="font-bold mb-4">Agregar Imagen</h2>

                <div>
                    <input type="file" x-ref="photoInput" wire:model="photoAgregar" class="border p-2 rounded w-full"
                        accept="image/png, image/jpeg" @change="fileLoaded = $event.target.files.length > 0">
                </div>

                <!-- Mensaje de error si lo hay -->
                <x-input-error for="photoAgregar" class="mt-2" />

                <!-- Indicador de carga mientras se sube la imagen -->
                <div wire:loading wire:target="photoAgregar" class="text-blue-500 mt-2">
                    Subiendo imagen, por favor espere...
                </div>

                <!-- Botones -->
                <div class="mt-4 flex space-x-4">
                    <!-- Botón Guardar -->
                    <button wire:click="savePhoto" class="action-button-b" x-show="fileLoaded"
                        wire:loading.attr="disabled" wire:target="savePhoto, photoAgregar">
                        <span wire:loading.remove wire:target="savePhoto">
                            Guardar
                        </span>
                        <span wire:loading wire:target="savePhoto">
                            Guardando
                        </span>
                    </button>
                    <!-- Botón Cancelar -->
                    <button @click="showUploadModal = false" class="action-button-b">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>


<!-- Modal para editar imagen -->
<div @close-edit-modal.window="showEditModalImagen = false; fileLoaded = false" x-data="{
    fileLoaded: false,
    isSmallScreen: window.innerWidth <= 768,
    checkScreenSize() { this.isSmallScreen = window.innerWidth <= 768; }
}"
    x-init="checkScreenSize();
    window.addEventListener('resize', checkScreenSize)">
    <template x-if="showEditModalImagen">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg relative w-full max-w-md">
                <h2 class="font-bold mb-4">Editar Imagen</h2>

                <div>
                    <input type="file" x-ref="photoInput" wire:model="photoEditar"
                        class="border p-2 rounded w-full" accept="image/png, image/jpeg"
                        @change="fileLoaded = $event.target.files.length > 0">
                </div>

                <!-- Mensaje de error si lo hay -->
                <x-input-error for="photoEditar" class="mt-2" />

                <!-- Indicador de carga mientras se sube la imagen -->
                <div wire:loading wire:target="photoEditar" class="text-blue-500 mt-2">
                    Subiendo imagen, por favor espere...
                </div>

                <!-- Botones -->
                <div class="mt-4 flex space-x-4">
                    <!-- Botón Guardar -->
                    <button wire:click="savePhoto" class="action-button-b" x-show="fileLoaded"
                        wire:loading.attr="disabled" wire:target="savePhoto, photoEditar">
                        <span wire:loading.remove wire:target="savePhoto">
                            Guardar
                        </span>
                        <span wire:loading wire:target="savePhoto">
                            Guardando
                        </span>
                    </button>
                    <!-- Botón Cancelar -->
                    <button @click="showEditModalImagen = false" class="action-button-b">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<div class="w-full lg:w-2/3 px-4 mb-8 lg:mb-0">
    <div @close-edit-modal.window="showPhoto = false">
        <!-- Modal para agrandar imagen -->
        <template x-if="showPhoto">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
                <div
                    class="bg-white rounded-lg max-w-full w-full h-full flex flex-col items-center justify-center p-4">
                    <img :src="imageToShow" alt="Imagen ampliada" class="w-full h-auto"
                        style="max-height: 80vh; object-fit: contain;">
                    <div class="mt-4">
                        <button @click="showPhoto = false" class="action-button-b">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- Modal para editar logo de empresa -->
<div @close-edit-modal.window="showPhotoProfile = false; fileLoaded = false" x-data="{
    fileLoaded: false,
    isSmallScreen: window.innerWidth <= 768,
    checkScreenSize() { this.isSmallScreen = window.innerWidth <= 768; }
}"
    x-init="checkScreenSize();
    window.addEventListener('resize', checkScreenSize)">
    <template x-if="showPhotoProfile">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg relative w-full max-w-md">
                <h2 class="font-bold mb-4">Agregar Imagen</h2>

                <div>
                    <input type="file" x-ref="photoInput" wire:model="logo" class="border p-2 rounded w-full"
                        accept="image/png, image/jpeg" @change="fileLoaded = $event.target.files.length > 0">
                </div>

                <!-- Mensaje de error si lo hay -->
                <x-input-error for="logo" class="mt-2" />

                <!-- Indicador de carga mientras se sube la imagen -->
                <div wire:loading wire:target="logo" class="text-blue-500 mt-2">
                    Subiendo imagen, por favor espere...
                </div>

                <!-- Botones -->
                <div class="mt-4 flex space-x-4">
                    <!-- Botón Guardar -->
                    <button wire:click="savePhoto" class="action-button-b" x-show="fileLoaded"
                        wire:loading.attr="disabled" wire:target="savePhoto, logo">
                        <span wire:loading.remove wire:target="savePhoto">
                            Guardar
                        </span>
                        <span wire:loading wire:target="savePhoto">
                            Guardando
                        </span>
                    </button>
                    <!-- Botón Cancelar -->
                    <button @click="showPhotoProfile = false" class="action-button-b">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    .custom-card {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
    }

    .custom-box {
        position: relative;
        width: 100%;
        height: 200px;
        /* Ajusta la altura según tus necesidades */
    }

    .custom-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .custom-description {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: opacity 0.3s ease;
    }

    .custom-card:hover .custom-description {
        opacity: 1;
    }

    .action-button-b {
        background-color: #ffffff;
        color: #000000;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: bold;
        transition: background-color 0.2s ease;
    }

    .action-button-b:hover {
        background-color: #f0f0f0;
    }

    .sin-imagen {
        width: 100%;
        height: 100%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
</style>
</div>
