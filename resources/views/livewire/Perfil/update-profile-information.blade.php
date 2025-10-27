<div x-data="{ exito: false, photoName: null, photoPreview: null }" x-init="$watch('exito', value => value && setTimeout(() => exito = false, 3000))" @exito.window="exito = true">

    <x-form-section submit="updateProfile">
        <x-slot name="title">
            {{ __('Información del Perfil') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Actualiza la información del perfil de tu cuenta y la dirección de correo electrónico.') }}
        </x-slot>

        <x-slot name="form">

            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <x-label for="photo" value="{{ __('Foto de Perfil') }}" />

                <!-- Imagen actual del usuario o nombre si no hay foto -->
                <div class="mt-2" x-show="!photoPreview">
                    @if ($this->user->profile_photo_path)
                    <img src="{{ Storage::disk('s3')->temporaryUrl($user->profile_photo_path, now()->addMinutes(10)) }}"
                        alt="Foto de perfil" />
                    @else
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                        class="h-20 w-20 rounded-full mr-4">
                    @endif
                </div>

                <!-- Nueva foto de perfil (previsualización) -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'"></span>
                </div>


                <!-- Campo de selección de archivos -->
                <input type="file" id="photo" class="hidden" wire:model="photo" x-ref="photo"
                    x-on:change="
                photoName = $refs.photo.files[0].name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview = e.target.result;
                };
                reader.readAsDataURL($refs.photo.files[0]);
            " />

                <div class="mt-2 flex items-center">
                    <!-- Botón estilizado para seleccionar archivos -->
                    <x-secondary-button class="me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                        {{ __('Seleccionar una nueva foto') }}
                    </x-secondary-button>

                    <!-- Botón para eliminar la foto si existe -->
                    @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" wire:click="deleteProfilePhoto">
                        {{ __('Eliminar Foto') }}
                    </x-secondary-button>
                    @endif
                </div>

                <x-input-error for="photo" class="mt-2" />
            </div>
            @endif

            {{-- NOMBRE --}}
            <div class="col-span-6 sm:col-span-4">
                <x-label for="name" value="{{ __('Apellido y Nombre') }}" />
                <div class="relative">
                    <input id="name" type="text"
                        class="mt-1 block w-full text-md px-3 py-2 rounded-lg bg-white border-2 border-gray-300
                      placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white
                      focus:border-gray-600 focus:outline-none"
                        wire:model="state.name" autocomplete="name" />
                    <x-input-error for="name" class="input-error" />
                </div>
            </div>

            {{-- EMAIL --}}
            <div class="col-span-6 sm:col-span-4">
                <x-label for="email" value="{{ __('Correo electrónico') }}" />
                <div class="relative">
                    <input id="email" type="email"
                        class="mt-1 block w-full text-md px-3 py-2 rounded-lg bg-white border-2 border-gray-300
                      placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white
                      focus:border-gray-600 focus:outline-none"
                        wire:model="state.email" autocomplete="email" />
                    <x-input-error for="email" class="input-error" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <!-- Mensaje de éxito -->
            <div x-show="exito" x-transition class="text-sm text-green-600 mr-2">
                {{ __('Guardado correctamente.') }}
            </div>

            <x-action-message class="me-3" on="saved">
                {{ __('Guardado.') }}
            </x-action-message>

            <x-button wire:loading.attr="disabled">
                {{ __('Guardar') }}
            </x-button>
        </x-slot>
    </x-form-section>
</div>