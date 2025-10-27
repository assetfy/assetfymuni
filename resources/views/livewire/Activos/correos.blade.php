{{-- resources/views/livewire/activos/correos.blade.php --}}
<div>
    <style>
        /* Scroll cómodo en móviles dentro del modal */
        .modal-scroll {
            max-height: 85vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }
    </style>

    <x-dialog-modal wire:model.live="open" maxWidth="2xl">
        {{-- Título --}}
        <x-slot name="title">
            <div class="text-center">
                <h2 class="text-2xl font-semibold text-gray-900">
                    {{ $esInvitado ? 'Enviar mensaje al gestor' : 'Contactar ' . ($etiqueta ?: 'destinatario') }}
                </h2>
                @unless($esInvitado)
                <p class="mt-1 text-xs text-gray-500">
                    El destinatario verá tu nombre y, si responde, te llegará a tu correo.
                </p>
                @endunless
            </div>
        </x-slot>

        {{-- Contenido (form para que el submit funcione en iOS) --}}
        <x-slot name="content">
            <form id="form-correo" wire:submit.prevent="enviar">
                <div class="modal-scroll px-1 pt-1 {{ $esInvitado ? 'space-y-3' : 'space-y-6 sm:space-y-5' }}"
                    x-data="{ tipo: @entangle('asuntoTipo'), msg: @entangle('mensaje') }">

                    @if($esInvitado)
                    <div class="d-flex justify-content-center my-2">
                        <div class="btn-group" role="group" aria-label="Asunto">
                            <button type="button"
                                class="btn {{ $asuntoTipo === 'mensaje' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                wire:click="$set('asuntoTipo','mensaje')">
                                Mensaje
                            </button>

                            <button type="button"
                                class="btn {{ $asuntoTipo === 'falla' ? 'btn-danger' : 'btn-outline-danger' }}"
                                wire:click="$set('asuntoTipo','falla')">
                                Reportar falla
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Para / Bien --}}
                    @unless($esInvitado)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Para:</span>
                            <span class="font-medium text-gray-800 break-all">{{ $email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Bien:</span>
                            <span class="font-medium text-gray-800">{{ $activoNombre }}</span>
                            @isset($urlBien)
                            <a href="{{ $urlBien }}" target="_blank" class="ml-2 text-blue-600 hover:underline">ver</a>
                            @endisset
                        </div>
                    </div>
                    @endunless

                    {{-- Datos del remitente (solo invitados)
                         Móvil: tarjeta con borde y título
                         Desktop: sin borde/encabezado, layout más compacto --}}
                    @if($esInvitado)
                    <div class="border border-gray-200 rounded-xl p-0 sm:border-0 sm:p-0">
                        {{-- Encabezado sólo en móvil --}}
                        <div class="space-y-3">
                            <input type="text" readonly
                                value="Bien: {{ $activoNombre }}"
                                class="w-full px-3 py-2 sm:py-3 bg-gray-50 ring-1 ring-gray-300 rounded-lg
                                          text-gray-800 font-medium">

                            {{-- Nombre --}}
                            <input id="remitenteNombreApellido" type="text" autocomplete="name"
                                wire:model.defer="remitenteNombreApellido"
                                class="w-full px-3 py-2 sm:py-3 bg-gray-50 ring-1 ring-gray-300 rounded-lg
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 mt-1"
                                placeholder="Nombre y Apellido">
                            <x-input-error for="remitenteNombreApellido" class="mt-1 text-sm text-red-500" />

                            {{-- Correo --}}
                            <input id="correo" type="email" autocomplete="email" inputmode="email"
                                wire:model.defer="remitenteEmail"
                                class="w-full px-3 py-2 sm:py-3 bg-gray-50 ring-1 ring-gray-300 rounded-lg
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 mt-1"
                                placeholder="Correo electrónico">
                            <x-input-error for="remitenteEmail" class="mt-1 text-sm text-red-500" />

                            {{-- Mensaje (mismo ancho que inputs) --}}
                            <textarea id="mensaje" maxlength="1000" rows="4" x-model="msg" wire:model.defer="mensaje"
                                :placeholder="tipo === 'falla'
                                ? 'Reporte de falla. Ej.: Se detectó la siguiente falla: pasos para reproducir, cuándo ocurrió y el impacto…'
                                : 'Mensaje al gestor. Ej.: Buenos días, necesito consultar por el estado del bien…'"
                                class="block w-full max-w-full min-w-0 resize-y px-3 py-2 sm:py-3 bg-gray-50 ring-1 ring-gray-300 rounded-lg
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 mt-1"></textarea>
                            <x-input-error for="mensaje" class="mt-1 text-sm text-red-500" />
                        </div>
                    </div>
                    @endif

                    @if(!$esInvitado)
                    {{-- Asunto (en desktop queda compacto; en móvil sigue cómodo) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-2">Asunto</label>
                        <div class="inline-flex rounded-lg bg-gray-100 p-1 ring-1 ring-gray-200">
                            <label class="px-3 py-1.5 text-sm rounded-md cursor-pointer transition"
                                :class="tipo === 'mensaje' ? 'bg-white ring-1 ring-gray-300 text-gray-900' : 'text-gray-600 hover:text-gray-800'">
                                <input type="radio" class="sr-only" wire:model="asuntoTipo" value="mensaje">
                                Mensaje
                            </label>

                            <label class="px-3 py-1.5 text-sm rounded-md cursor-pointer transition"
                                :class="tipo === 'falla' ? 'bg-white ring-1 ring-gray-300 text-gray-900' : 'text-gray-600 hover:text-gray-800'">
                                <input type="radio" class="sr-only" wire:model="asuntoTipo" value="falla">
                                Reportar falla
                            </label>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            • <strong>Mensaje</strong>: comunicación general.<br>
                            • <strong>Reportar falla</strong>: describe el problema y el impacto.
                        </p>

                        <x-input-error for="asuntoTipo" class="mt-1 text-sm text-red-500" />
                    </div>

                    {{-- Mensaje + contador derecha --}}
                    <div>
                        <div class="flex items-baseline justify-between">
                            <label class="block text-sm font-medium text-gray-800">Mensaje</label>
                            <span class="text-xs text-gray-500" x-text="`${(msg ?? '').length}/1000`"></span>
                        </div>

                        <textarea wire:model.defer="mensaje" x-model="msg" maxlength="1000" rows="6"
                            placeholder="Ej.: Buenos días, necesito consultar por el estado del bien / Se detectó la siguiente falla: pasos para reproducir, cuándo ocurrió y el impacto…"
                            class="mt-2 w-full px-4 py-3 bg-gray-50 ring-1 ring-gray-200 rounded-xl
                                         focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>

                        <p class="mt-1 text-xs text-gray-500">
                            Evitá compartir contraseñas o datos sensibles.
                        </p>

                        <x-input-error for="mensaje" class="mt-1 text-sm text-red-500" />
                    </div>

                    {{-- Adjuntos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-2">
                            Adjuntos <span class="text-gray-400 font-normal">(opcional)</span>
                        </label>

                        <input type="file" wire:model="adjuntos" multiple
                            class="block w-full text-sm text-gray-700
                                      file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                      file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 transition" />

                        <div class="mt-2 text-xs text-gray-500" wire:loading wire:target="adjuntos">
                            Subiendo archivos… esperá a que termine para poder enviar.
                        </div>

                        @if (!empty($adjuntos))
                        <ul class="mt-2 space-y-1">
                            @foreach ($adjuntos as $file)
                            <li class="text-sm text-gray-700 flex items-center gap-2">
                                <i class="fa-regular fa-file text-gray-400"></i>
                                {{ method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'archivo' }}
                            </li>
                            @endforeach
                        </ul>
                        @endif

                        <x-input-error for="adjuntos.*" class="text-sm text-red-500 mt-1" />
                    </div>
                    @endif
                </div>
            </form>
        </x-slot>

        {{-- Footer: barra gris a lo ancho, en todos los tamaños --}}
        <x-slot name="footer">
            @if (!$esInvitado)
            <div class="w-full bg-gray-100 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    <x-secondary-button type="button" wire:click="cerrar" class="px-4 py-2 text-sm">
                        Cancelar
                    </x-secondary-button>

                    <x-button
                        type="submit"
                        form="form-correo"
                        wire:loading.attr="disabled"
                        wire:target="enviar"
                        class="px-4 py-2 text-sm inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="enviar">Enviar</span>
                        <span wire:loading wire:target="enviar" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4A4 4 0 004 12z"></path>
                            </svg>
                            Enviando…
                        </span>
                    </x-button>

                    <div class="text-xs text-gray-500" wire:loading wire:target="adjuntos">
                        Esperando que termine la subida de adjuntos…
                    </div>
                </div>
            </div>
            @else
            <div>
                <div class="justify-end">
                    <x-secondary-button type="button" wire:click="cerrar" class="text-sm text-black">
                        Cancelar
                    </x-secondary-button>

                    <x-button
                        type="submit"
                        form="form-correo"
                        wire:loading.attr="disabled"
                        wire:target="enviar"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm inline-flex items-center">
                        <span wire:loading.remove wire:target="enviar">Enviar</span>
                        <span wire:loading wire:target="enviar" class="inline-flex items-center">Enviando…</span>
                    </x-button>
                </div>
            </div>
            @endif
        </x-slot>
    </x-dialog-modal>

    <style>
        /* Solo para los dos botones del grupo "Asunto" */
        .btn-group[aria-label="Asunto"] .btn {
            /* Asegura que Bootstrap gane frente a utilidades Tailwind */
            color: var(--bs-btn-color) !important;
            background-color: var(--bs-btn-bg) !important;
            border-color: var(--bs-btn-border-color) !important;
        }

        /* Hover correcto para outlines (texto blanco, fondo coloreado) */
        .btn-group[aria-label="Asunto"] .btn.btn-outline-secondary:hover,
        .btn-group[aria-label="Asunto"] .btn.btn-outline-secondary:focus {
            color: #fff !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }

        .btn-group[aria-label="Asunto"] .btn.btn-outline-danger:hover,
        .btn-group[aria-label="Asunto"] .btn.btn-outline-danger:focus {
            color: #fff !important;
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
    </style>
</div>