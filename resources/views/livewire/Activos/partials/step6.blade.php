@if ($user->panel_actual == 'Empresa' || $user->panel_actual == 'Prestadora')
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-boxes-packing text-sky-600"></i> Asignación del Bien
        <span class="text-sm text-gray-500 font-normal">(Opcional)</span>
    </h2>

    <div class="space-y-6">
        <!-- Responsable -->
        @if (!empty($responsableInicial) && $responsableInicial)
        <div>
            <x-label value="Responsable" class="text-sm font-semibold text-gray-700" />
            <div class="relative">
                <i class="fa-solid fa-user-tie absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md pl-10 pr-4 py-2 cursor-not-allowed text-gray-600"
                    value="{{ $responsable }}" disabled />
            </div>
        </div>
        @else
        <div>
            <x-searchable-dropdown label="Responsable" icon="fa-solid fa-user-tie" :options="$empleadosLista"
                model="responsable_id" search-model="searchResponsable" select-method="setResponsable"
                value-key="id" label-key="name" :selected="$responsable" />
            <x-input-error for="responsable_id" class="mt-1" />
        </div>
        @endif

        <!-- Fecha Asignación -->
        <div>
            <x-label value="Fecha de Asignación" for="fecha_asignacion"
                class="text-sm font-semibold text-gray-700" />
            <div class="relative">
                <i class="fa-solid fa-calendar-day absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="datetime-local" id="fecha_asignacion" wire:model.live="fecha_asignacion"
                    max="{{ now()->format('Y-m-d\TH:i') }}"
                    {{ empty($responsable_id) && empty($asignado_a_id) ? 'disabled' : '' }}
                    class="mt-1 block w-full border border-gray-300 rounded-md 
                        pl-10 pr-4 py-2 focus:ring-indigo-500 
                        focus:border-indigo-500 transition" />
            </div>
            @error('fecha_asignacion')
            <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Persona Asignada -->
        @if ($empresaPrestadora != 2)
        @if (!empty($asignadoInicial) && $asignadoInicial)
        <div>
            <x-label value="Persona asignada" class="text-sm font-semibold text-gray-700" />
            <div class="relative">
                <i
                    class="fa-solid fa-user-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md pl-10 pr-4 py-2 cursor-not-allowed text-gray-600"
                    value="{{ $asignado_a }}" disabled />
            </div>
        </div>
        @else
        <div>
            <x-searchable-dropdown label="Persona asignada" icon="fa-solid fa-user-check" :options="$empleadosLista"
                model="asignado_a_id" search-model="searchAsignado" select-method="setAsignadoA"
                value-key="id" label-key="name" :selected="$asignado_a" />
            <x-input-error for="asignado_a_id" class="mt-1" />
        </div>
        @endif
        @endif
    </div>

    <!-- JS Tree Selector -->
    @if (!$inmueble)
    <div class="mt-6">
        <div wire:ignore>
            <input type="hidden" id="selectedLevelInput" wire:model="selectedLevel">
            <input id="jstree_search" type="text" placeholder="Buscar nivel…" class="form-control mb-2">
            <div id="jstree_container"
                style="max-height: 300px; overflow: auto; border: 1px solid #ddd; border-radius: 6px; padding: 6px;">
            </div>
        </div>
    </div>
    @endif
</div>
@endif

@if ($errors->has('step6'))
<div class="text-red-500 font-semibold">{{ $errors->first('step6') }}</div>
@endif