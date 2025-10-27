<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <h2 class="text-2xl font-bold text-gray-800">Cambiar Ubicación del Activo</h2>
        </x-slot>
        <x-slot name="content">
            @if ($ubicacionActual)
                <div class="mb-6">
                    <x-label value="Ubicación actual" class="text-lg font-semibold text-gray-700" />
                    <div class="bg-gray-100 p-3 rounded-lg fw-bold fs-6 text-gray-900">{{ $ubicacionActual->nombre }}</div>
                </div>
                <div class="mb-6">
                    <x-label value="Propiedad" class="text-lg font-semibold text-gray-700" />
                    <div class="bg-gray-100 p-3 rounded-lg fw-bold fs-6 text-gray-900">{{ $ubicacionActual->propiedad }}</div>
                </div>
            @else
                <div class="mb-6">
                    <x-label value="Ubicación actual" class="text-lg font-semibold text-gray-700" />
                    <div class="text-red-500 font-medium">Este activo aún no tiene una ubicación asignada.</div>
                </div>
            @endif

            <div class="mb-6">
                <x-label value="Ubicación disponible" class="text-lg font-semibold text-gray-700" />
                @if ($ubicacionesDisponibles->isEmpty())
                    <div class="text-red-500 font-medium">No existen ubicaciones disponibles para mover el activo.</div>
                @else
                    <select class="form-control bg-gray-100 text-gray-700" wire:model.defer="id_ubicacion">
                        <option value hidden="">Seleccione una ubicación</option>
                        @foreach ($ubicacionesDisponibles as $ubicacion)
                            <option value="{{ $ubicacion->id_ubicacion }}">{{ $ubicacion->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="id_ubicacion" />
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2 bg-gray-300 hover:bg-gray-400 text-gray-800" wire:click="$set('open', false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button class="bg-blue-600 hover:bg-blue-700 text-white" wire:click="actualizar" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <x-button class="bg-blue-600 hover:bg-blue-700 text-white animate-pulse" wire:click="crearubicacion">
                Crear Ubicación
            </x-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>
