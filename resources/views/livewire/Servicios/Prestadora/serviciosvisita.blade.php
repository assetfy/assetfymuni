<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl"
        class="fixed inset-0 flex items-center justify-center bg-black/50">

        {{-- Encabezado con Pestañas --}}
        <x-slot name="title">
            <div class="flex justify-center border-b bg-gray-100 sticky top-0 z-10">
                <button wire:click="$set('activeTab', 'solicitud')"
                    class="{{ $activeTab === 'solicitud' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Solicitud
                </button>
                <button wire:click="$set('activeTab', 'atributos')"
                    class="{{ $activeTab === 'atributos' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Atributos
                </button>
                <button wire:click="$set('activeTab', 'servicios')"
                    class="{{ $activeTab === 'servicios' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Servicios
                </button>
                <button wire:click="$set('activeTab', 'Garantia')"
                    class="{{ $activeTab === 'Garantia' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Garantia
                </button>
            </div>
        </x-slot>

        {{-- Contenido del Modal --}}
        <x-slot name="content">
            <div class="bg-white p-6 space-y-6 overflow-auto max-h-[calc(100vh-200px)]">
                {{-- Contenido de la Pestaña Solicitud --}}
                <div class="{{ $activeTab === 'solicitud' ? '' : 'hidden' }}">
                    @if ($this->activos)
                        <h2 class="text-center font-semibold text-xl mb-4">📌 Nombre del Bien</h2>
                        <p class="text-center text-gray-700 mb-4 text-lg">{{ $activos->nombre }}</p>

                        <h3 class="text-center font-semibold text-lg mb-4">🛠️ Servicio Solicitado</h3>
                        <p class="text-center text-gray-700 mb-6 text-lg">{{ }}</p>
                    @endif)
                </div>
        </x-slot>

        {{-- Footer con Botones --}}
        <x-slot name="footer">
            <div class="flex justify-end mt-2 space-x-2 sticky bottom-0 bg-white p-1 border-t">
                {{-- Botón Cancelar --}}
                <button wire:click="$set('open', false)"
                    class="px-3 py-1 rounded border border-gray-400 text-gray-700 font-medium bg-white transition-all hover:bg-gray-100 hover:border-gray-500 hover:text-gray-800 shadow-sm text-sm">
                    Cancelar
                </button>

                {{-- Botón Actualizar --}}
                <button wire:click="actualizar"
                    class="px-3 py-1 rounded bg-gradient-to-r from-red-500 to-red-600 text-white font-medium transition-all hover:from-red-600 hover:to-red-700 hover:shadow-lg shadow-md text-sm">
                    Actualizar
                </button>
            </div>
        </x-slot>

    </x-dialog-modal>
</div>
