<div>
    <a class="border border-neutral-200 shadow-sm rounded-md outline-none focus:border-blue-300 px-4 py-2 bg-blue-500 text-white transition ease-in-out 
        hover:bg-blue-600 dark:bg-blue-700 dark:border-blue-600 dark:focus:border-blue-600 dark:text-white dark:hover:bg-blue-800 dark:hover:border-blue-600"
        wire:click="$set('open', true)" style="text-decoration: none;"> Editar <i class="fa-solid fa-pen-to-square"></i> </a>

    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Autorizacion
        </x-slot>
        <x-slot name="content">
            <div class="flex items-center">
                <!-- Información a la izquierda -->
                <div class="mr-8">
                    <div class="mb-4">
                        <x-label value="Cuit" />
                        <div class="fw-bold fs-6">{{ $empresa->cuit }}</div>
                    </div>
                    <div class="mb-4">
                        <x-label value="Razon Social" />
                        <div class="fw-bold fs-6">{{$empresa->razon_social }}</div>
                    </div>
                    <div class="mb-4">
                        <x-label value="Estado de la Solicitud" />
                        <select class="form-control" wire:model.defer="updateAutorizacion">
                            <option value="">Autoriza</option> <!-- Opción por defecto -->
                            <option value="1">Aceptado</option>
                            <option value="2">Rechazado</option>
                        </select>
                        <x-input-error for="updateAutorizacion" /> <!-- Corregí el nombre del campo para el input error -->
                    </div>
                    
                </div>

                <!-- Imagen o documento a la derecha -->
                <div>
                    <!-- Coloca aquí tu imagen o documento -->
                    <!-- Por ejemplo, una imagen -->
                    <img src="{{ $empresa->constancia_afip}}" alt="Imagen" class="w-32 h-auto">
                    <!-- O un enlace a un documento -->

                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>

            <x-danger-button wire:click="actualizarAutorizacion" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>
