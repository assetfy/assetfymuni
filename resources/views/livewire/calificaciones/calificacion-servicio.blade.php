<div x-data="{
    ratingPrecio: @entangle('precio'),
    ratingDiagnostico: @entangle('diagnostico'),
    ratingServicio: @entangle('calificacion'),
    tempRating: 0,
    general: @entangle('general'),
    opcionSeleccionada: null,
    handleStarClick(ratingType, index) {
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
        this.general = '';
        @this.call('clearError');
    }
}"
    x-init="ratingPrecio = @entangle('precio'); ratingDiagnostico = @entangle('diagnostico'); ratingServicio = @entangle('calificacion');">

    <x-dialog-modal wire:model.live="open" maxWidth="2xl" class="fixed inset-0 flex items-center justify-center bg-black/50">
        <x-slot name="title">
            <div class="flex justify-between items-center bg-white p-6 rounded-t-lg shadow">
                <h2 class="text-xl font-bold text-black">Reseña de Servicio</h2>
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
                contratar: @entangle('contratacion'), 
                ratingPrecio: @entangle('precio'), 
                ratingDiagnostico: @entangle('diagnostico'), 
                ratingServicio: @entangle('calificacion'),
                tempRating: 0 
            }">

                {{-- Pregunta: ¿Lo volvería a contratar? --}}
                <div class="mb-4 flex items-center space-x-4">
                    <label class="text-lg font-semibold text-black">¿Lo volvería a contratar?</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model="contratacion" value="1" x-on:change="contratar = 1; resetRatings();" class="text-blue-500">
                            <span>Sí</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" wire:model="contratacion" value="0" x-on:change="contratar = 0; resetRatings();" class="text-red-500">
                            <span>No</span>
                        </label>
                    </div>
                    <x-input-error for="contratacion" />
                </div>

                {{-- Opciones cuando selecciona "Sí" --}}
                <div x-show="contratar === 1" class="space-y-4">
                    <h2 class="text-lg font-semibold text-center text-black leading-tight">Calificá las características</h2>
                    <p class="text-sm text-center text-gray-500 mt-1">(Opcional)</p>

                    {{-- Mostrar mensaje de error global --}}
                    @if(session()->has('error'))
                        <div class="text-red-500 text-sm text-center">{{ session('error') }}</div>
                    @endif

                    {{-- Calificación del servicio --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Servicio</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @mouseover="tempRating = i" @mouseleave="tempRating = 0" @click="handleStarClick('servicio', i)" class="cursor-pointer text-blue-500">
                                    <i :class="getStarClass(ratingServicio, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" wire:model="calificacion" x-bind:value="ratingServicio" />
                    </div>

                    {{-- Calificación del diagnóstico --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Diagnóstico</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @mouseover="tempRating = i" @mouseleave="tempRating = 0" @click="handleStarClick('diagnostico', i)" class="cursor-pointer text-blue-500">
                                    <i :class="getStarClass(ratingDiagnostico, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" wire:model="diagnostico" x-bind:value="ratingDiagnostico" />
                    </div>

                    {{-- Calificación del precio --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Precio</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @mouseover="tempRating = i" @mouseleave="tempRating = 0" @click="handleStarClick('precio', i)" class="cursor-pointer text-blue-500">
                                    <i :class="getStarClass(ratingPrecio, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                        <input type="hidden" wire:model="precio" x-bind:value="ratingPrecio" />
                    </div>
                </div>

                {{-- Opciones cuando selecciona "No" --}}
                <div x-show="contratar === 0" class="space-y-4">
                    <h2 class="text-lg font-semibold text-center text-black leading-tight">Calificá una característica</h2>
                    <p class="text-sm text-center text-gray-500 mt-1">(Selecciona solo una)</p>

                    {{-- Mostrar mensaje de error global --}}
                    @if(session()->has('error'))
                        <div class="text-red-500 text-sm text-center">{{ session('error') }}</div>
                    @endif

                    {{-- Calificación del servicio --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Servicio</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @click="if (!opcionSeleccionada || opcionSeleccionada === 'servicio') { handleStarClick('servicio', i); opcionSeleccionada = 'servicio'; }"
                                    class="cursor-pointer text-blue-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': opcionSeleccionada && opcionSeleccionada !== 'servicio' }">
                                    <i :class="getStarClass(ratingServicio, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Calificación del diagnóstico --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Diagnóstico</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @click="if (!opcionSeleccionada || opcionSeleccionada === 'diagnostico') { handleStarClick('diagnostico', i); opcionSeleccionada = 'diagnostico'; }"
                                    class="cursor-pointer text-blue-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': opcionSeleccionada && opcionSeleccionada !== 'diagnostico' }">
                                    <i :class="getStarClass(ratingDiagnostico, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Calificación del precio --}}
                    <div class="flex items-center space-x-4">
                        <label class="block text-lg font-semibold text-black w-64">Precio</label>
                        <div class="flex space-x-1">
                            <template x-for="i in [1, 2, 3, 4, 5]" :key="i">
                                <span @click="if (!opcionSeleccionada || opcionSeleccionada === 'precio') { handleStarClick('precio', i); opcionSeleccionada = 'precio'; }"
                                    class="cursor-pointer text-blue-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': opcionSeleccionada && opcionSeleccionada !== 'precio' }">
                                    <i :class="getStarClass(ratingPrecio, i)" class="text-2xl"></i>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Comentarios (Siempre visible) --}}
                <div class="flex items-center space-x-4">
                    <label for="comentarios" class="block text-lg font-semibold text-black">Comentarios</label>
                    <textarea id="general" wire:model="general" class="w-full p-2 text-black border rounded"></textarea>
                </div>
            </div>
        </x-slot>

        {{-- Footer --}}
        <x-slot name="footer">
            <div class="flex justify-end space-x-4">
                <x-secondary-button wire:click="cerrar" class="px-5 py-3 rounded-lg bg-gray-300 text-black hover:bg-gray-400 text-lg">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save" wire:loading.remove wire:target="save" class="px-5 py-3 rounded-lg bg-red-500 text-white hover:bg-red-600 text-lg">
                    Publicar
                </x-danger-button>

                <span class="inline-flex items-center rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>