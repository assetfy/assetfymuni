<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-ban text-red-500 text-xl"></i>
                <span>Formulario de Rechazo de Servicio</span>
            </div>
        </x-slot>

        <x-slot name="content">
            @if ($servicio)
                <div class="p-4 bg-gray-100 rounded-md mb-4 shadow-sm space-y-2">
                    <h3 class="text-lg font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-info-circle text-gray-600"></i>
                        Detalles del Servicio
                    </h3>
                    <p><strong>🔹 Servicio:</strong> {{ $servicio->servicios->nombre ?? 'N/A' }}</p>
                    <p><strong>👤 Solicitante:</strong> {{ $servicio->users->name ?? $servicio->empresasSolicitantes->razon_social }}</p>
                    <p><strong>🏢 Prestador:</strong> {{ $servicio->empresasPrestadora->razon_social ?? 'N/A' }}</p>
                    <p><strong>📦 Bien Asociado:</strong> {{ $servicio->activos->nombre ?? 'N/A' }}</p>
                    <p><strong>📅 Fecha Pactada:</strong> {{ $servicio->fecha_finalizacion ?? 'No definida' }}</p>
                </div>
            @endif

            <div class="space-y-2">
                <label for="motivoRechazo" class="flex items-center gap-2 font-semibold">
                    <i class="fa-solid fa-pen-to-square text-gray-600"></i>
                    Motivo del Rechazo (máx. 140 caracteres)
                </label>
                <textarea id="motivoRechazo" wire:model="motivoRechazo" maxlength="140"
                    class="w-full p-2 border rounded-md resize-none focus:ring focus:ring-red-300 transition"
                    placeholder="Ingrese el motivo del rechazo aquí..." rows="3">
                </textarea>
                @error('motivoRechazo')
                    <span class="text-red-500 text-sm flex items-center gap-1">
                        <i class="fa-solid fa-exclamation-circle"></i>
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-secondary-button wire:click="close" class="flex items-center gap-2">
                    <i class="fa-solid fa-times"></i>
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="rechazarServicio" wire:loading.remove wire:target="rechazarServicio"
                    class="flex items-center gap-2">
                    <i class="fa-solid fa-trash-alt"></i>
                    Rechazar Servicio
                </x-danger-button>
                <span wire:loading wire:target="rechazar"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    <i class="fa-solid fa-spinner animate-spin"></i>
                    Guardando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
