<div x-data="{
    editMode: @entangle('editMode'),
    ratingPrecio: @entangle('updatePrecio'),
    ratingDiagnostico: @entangle('updateDiagnostico'),
    ratingServicio: @entangle('updateCalificacion'),
    tempRating: 0,
    general: @entangle('updateGeneral'),
    opcionSeleccionada: null,
    calificado: null,
    handleStarClick(ratingType, index) {

    if (this.contratar === 0) {
            if (!this.calificado) {
                this.calificado = ratingType; // Registra el primer campo calificado
            }
            if (this.calificado !== ratingType) return; // Bloquea otros campos si ya se calificó uno cuando se elije la opcion no
        }

        if (ratingType === 'precio') {
            this.ratingPrecio = index;
        } else if (ratingType === 'diagnostico') {
            this.ratingDiagnostico = index;
        } else if (ratingType === 'servicio') {
            this.ratingServicio = index;
        }
    },
    getStarClass(rating, index) {
        if (rating >= index) {
            return 'fas fa-star'; // Estrella completa
        } else if (rating >= index - 0.5 && rating < index) {
            return 'fas fa-star-half-alt'; // Media estrella
        } else {
            return 'far fa-star'; // Estrella vacía
        }
    },
    resetRatings() {
        this.ratingPrecio = 0;
        this.ratingDiagnostico = 0;
        this.ratingServicio = 0;
        this.opcionSeleccionada = null;
        this.calificado = null;
    }
}" x-init="ratingPrecio = @entangle('updatePrecio'); ratingDiagnostico = @entangle('updateDiagnostico'); ratingServicio = @entangle('updateCalificacion');">

    <x-dialog-modal wire:model.live="open" maxWidth="2xl" class="fixed inset-0 flex items-center justify-center bg-black/50">
        <x-slot name="title">
            <div class="flex justify-between items-center bg-white p-6 rounded-t-lg shadow">
                <h2 class="text-xl font-bold text-black">Editar Reseña de Servicio</h2>
                <button type="button" wire:click="cerrar" class="text-red-500 hover:text-red-700">
                    <span class="sr-only">Cerrar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="bg-white p-6 space-y-6 rounded-b-lg shadow" x-data="{ 
                contratar: @entangle('updateContratacion'), 
                ratingPrecio: @entangle('updatePrecio'), 
                ratingDiagnostico: @entangle('updateDiagnostico'), 
                ratingServicio: @entangle('updateCalificacion'),
                tempRating: 0 
            }">

                {{-- Proveedor --}}
                <div class="mb-4 flex items-center justify-center space-x-4">
                    <div class="flex space-x-4">
                        @php
                        $empresaSeleccionada = $empresas->firstWhere('cuit', (string)$proveedor);
                        @endphp
                        @if($empresaSeleccionada)
                        <span class="text-lg font-semibold text-black">{{ $empresaSeleccionada->razon_social }}</span>
                        @endif
                    </div>
                </div>

                {{-- Fecha Reseña --}}
                <div class="mb-4 flex items-end justify-end space-x-4">
                    <div class="flex">
                        <span class="text-sm text-gray-600">Fecha de Reseña: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Mostrar mensaje de error global --}}
                @if(session()->has('error'))
                <div class="text-red-500 text-sm text-center">{{ session('error') }}</div>
                @endif

                {{-- Pregunta: ¿Lo volvería a contratar? --}}
                <div class="mb-4 flex items-center space-x-4">
                    <label class="text-lg font-semibold text-black">¿Lo volvería a contratar?</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model="updateContratacion" value="1" :disabled="!editMode" x-on:change="contratar = 1; resetRatings();" class="text-blue-500">
                            <span>Sí</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model="updateContratacion" value="0" :disabled="!editMode" x-on:change="contratar = 0; resetRatings();" class="text-red-500">
                            <span>No</span>
                        </label>
                    </div>
                </div>

                {{-- Calificaciones --}}
                <div class="space-y-4">
                    <div class="flex items-center space-x-4" :class="{ 'opacity-50': contratar === 0 && calificado && calificado !== 'servicio' }">
                        <label class="block text-lg font-semibold text-black w-64">Servicio</label>
                        <div class="flex space-x-1">
                            <template x-for="index in 5" :key="index">
                                <i :class="getStarClass(ratingServicio, index)"
                                    @click="handleStarClick('servicio', index)"
                                    :class="{ 
                                    'cursor-pointer': editMode && (contratar === 1 || !calificado || calificado === 'servicio'),
                                    'cursor-not-allowed': contratar === 0 && calificado && calificado !== 'servicio'
                                    }"
                                    class="text-2xl text-blue-500"></i>
                            </template>
                            <input type="hidden" wire:model="updateCalificacion" x-bind:value="ratingServicio" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-4" :class="{ 'opacity-50': contratar === 0 && calificado && calificado !== 'diagnostico' }">
                        <label class="block text-lg font-semibold text-black w-64">Diagnóstico</label>
                        <div class="flex space-x-1">
                            <template x-for="index in 5" :key="index">
                                <i :class="getStarClass(ratingDiagnostico, index)"
                                    @click="handleStarClick('diagnostico', index)"
                                    :class="{ 
                                    'cursor-pointer': editMode && (contratar === 1 || !calificado || calificado === 'diagnostico'),
                                    'cursor-not-allowed': contratar === 0 && calificado && calificado !== 'diagnostico'
                                }"
                                    class="text-2xl text-blue-500"></i>
                            </template>
                            <input type="hidden" wire:model="updateDiagnostico" x-bind:value="ratingDiagnostico" />
                        </div>
                    </div>

                    <div class="flex items-center space-x-4" :class="{ 'opacity-50': contratar === 0 && calificado && calificado !== 'precio' }">
                        <label class="block text-lg font-semibold text-black w-64">Precio</label>
                        <div class="flex space-x-1">
                            <template x-for="index in 5" :key="index">
                                <i :class="getStarClass(ratingPrecio, index)"
                                    @click="handleStarClick('precio', index)"
                                    :class="{ 
                                    'cursor-pointer': editMode && (contratar === 1 || !calificado || calificado === 'precio'),
                                    'cursor-not-allowed': contratar === 0 && calificado && calificado !== 'precio'
                                }"
                                    class="text-2xl text-blue-500"></i>
                            </template>
                            <input type="hidden" wire:model="updatePrecio" x-bind:value="ratingPrecio" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-lg font-semibold text-black">Calificación General</label>
                        <textarea value="{ $calificacion->general}" wire:model.defer="updateGeneral" class="w-full mt-2 p-2 border rounded-lg" :disabled="!editMode"></textarea>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-4">
                @if (!$editMode)
                <x-secondary-button wire:click="toggleEditMode">Editar</x-secondary-button>
                @else
                <x-secondary-button wire:click="$set('open', false)">Cancelar</x-secondary-button>
                <x-danger-button wire:click="actualizar" wire:loading.attr="disabled">Guardar Cambios</x-danger-button>
                @endif
            </div>
        </x-slot>

    </x-dialog-modal>
</div>