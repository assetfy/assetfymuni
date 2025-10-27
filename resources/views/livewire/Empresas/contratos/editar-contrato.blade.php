<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        {{-- ===== HEADER ===== --}}
        <x-slot name="title">
            <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-[#1D6FA7] to-[#2B87B9]">
                <div
                    class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-5 text-white">
                    <div class="flex items-center gap-4">
                        <span
                            class="inline-flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid fa-file-signature text-2xl"></i>
                        </span>
                        <div>
                            <h2 class="text-2xl font-bold leading-tight tracking-tight">Editar Contrato</h2>
                            <p class="text-white/80 text-sm mt-1">Actualizá los datos clave de forma rápida y segura.</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 self-start md:self-center">
                        @if ($nro_contrato)
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-inset ring-white/25">
                                <i class="fa-solid fa-hashtag"></i> N.º {{ $nro_contrato }}
                            </span>
                        @endif
                        @if ($prestadora_nombre || $prestadora_cuit)
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold ring-1 ring-inset ring-white/25">
                                <i class="fa-solid fa-building"></i>
                                {{ $prestadora_nombre ?? 'Prestadora' }}
                                @if ($prestadora_cuit)
                                    <span class="opacity-80">({{ $prestadora_cuit }})</span>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </x-slot>

        {{-- ===== CONTENT ===== --}}
        <x-slot name="content">
            {{-- Loading Spinner --}}
            <div wire:loading.flex
                class="absolute inset-0 z-10 items-center justify-center rounded-xl bg-white/80 backdrop-blur-sm dark:bg-gray-900/80">
                <div class="flex items-center gap-3 text-gray-900 dark:text-slate-200">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl text-[#1D6FA7]"></i>
                    <span class="font-semibold text-lg">Cargando...</span>
                </div>
            </div>

            <div class="space-y-6 pt-2">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- ===== Card: Datos principales ===== --}}
                    <section
                        class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                        <header class="flex items-center gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                            <i class="fa-solid fa-clipboard-list text-lg text-[#1D6FA7]"></i>
                            <h3 class="text-base font-bold text-gray-900 ">Datos Principales</h3>
                        </header>

                        <div class="space-y-5">
                            {{-- Nombre --}}
                            <div>
                                <label for="nombre_contrato" class="mb-1.5 block text-sm font-medium text-gray-900">
                                    Nombre del contrato
                                </label>
                                <div
                                    class="flex items-center rounded-lg border border-slate-300 bg-white text-gray-900 focus-within:ring-2 focus-within:ring-[#1D6FA7] focus-within:border-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                    <span
                                        class="shrink-0 border-r border-slate-300 px-3 py-2.5 text-slate-500 dark:border-slate-600 dark:text-slate-400">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </span>
                                    <input type="text" id="nombre_contrato" wire:model.defer="nombre"
                                        class="w-full flex-1 border-none bg-transparent px-3 py-2 outline-none text-gray-900 placeholder:text-gray-500"
                                        placeholder="Ej: Servicio de mantenimiento anual" />
                                </div>
                                <x-input-error for="nombre" class="mt-1.5" />
                            </div>

                            {{-- Tipo --}}
                            <div>
                                <label for="tipo_contrato" class="mb-1.5 block text-sm font-medium text-gray-900 ">
                                    Tipo
                                </label>
                                <div class="relative">
                                    <div
                                        class="flex items-center rounded-lg border border-slate-300 bg-white text-gray-900 focus-within:ring-2 focus-within:ring-[#1D6FA7] focus-within:border-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                        <label for="tipo_contrato"
                                            class="shrink-0 cursor-pointer border-r border-slate-300 px-3 py-2.5 text-slate-500 dark:border-slate-600 dark:text-slate-400">
                                            <i class="fa-solid fa-tags"></i>
                                        </label>
                                        <select id="tipo_contrato" wire:model.defer="id_tipo_contrato"
                                            class="w-full flex-1 appearance-none border-none bg-transparent px-3 py-2 pr-10 outline-none text-gray-900">
                                            <option value="" disabled>Seleccionar...</option>
                                            @foreach ($tipos as $id => $nom)
                                                <option value="{{ $id }}">{{ $nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span
                                        class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <i class="fa-solid fa-angle-down"></i>
                                    </span>
                                </div>
                                <x-input-error for="id_tipo_contrato" class="mt-1.5" />
                            </div>

                            {{-- Estado --}}
                            <div>
                                <label for="estado_contrato" class="mb-1.5 block text-sm font-medium text-gray-900">
                                    Estado
                                </label>
                                <div class="relative">
                                    <div
                                        class="flex items-center rounded-lg border border-slate-300 bg-white text-gray-900 focus-within:ring-2 focus-within:ring-[#1D6FA7] focus-within:border-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                        <label for="estado_contrato"
                                            class="shrink-0 cursor-pointer border-r border-slate-300 px-3 py-2.5 text-slate-500 dark:border-slate-600 dark:text-slate-400">
                                            <i class="fa-solid fa-traffic-light"></i>
                                        </label>
                                        <select id="estado_contrato" wire:model.defer="id_estado_contrato"
                                            class="w-full flex-1 appearance-none border-none bg-transparent px-3 py-2 pr-10 outline-none text-gray-900">
                                            <option value="" disabled>Seleccionar...</option>
                                            @foreach ($estados as $id => $nom)
                                                <option value="{{ $id }}">{{ $nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span
                                        class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <i class="fa-solid fa-angle-down"></i>
                                    </span>
                                </div>
                                <x-input-error for="id_estado_contrato" class="mt-1.5" />
                            </div>
                        </div>
                    </section>

                    {{-- ===== Card: Vigencia y Valor ===== --}}
                    <section
                        class="space-y-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                        <header class="flex items-center gap-3 border-b border-slate-200 pb-3 dark:border-slate-700">
                            <i class="fa-solid fa-scale-balanced text-lg text-[#1D6FA7]"></i>
                            <h3 class="text-base font-bold text-gray-900 ">Vigencia y Valor</h3>
                        </header>

                        <div class="space-y-5">
                            {{-- Fecha inicio --}}
                            <div>
                                <label for="fecha_inicio" class="mb-1.5 block text-sm font-medium text-gray-900">
                                    Fecha de inicio
                                </label>
                                <input type="date" id="fecha_inicio" wire:model.defer="fecha_inicio"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-gray-900 placeholder:text-gray-500 focus:border-[#1D6FA7] focus:outline-none focus:ring-2 focus:ring-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900" />
                                <x-input-error for="fecha_inicio" class="mt-1.5" />
                            </div>

                            {{-- Fecha fin --}}
                            <div>
                                <label for="fecha_fin" class="mb-1.5 block text-sm font-medium text-gray-900 ">
                                    Fecha de fin
                                </label>
                                <input type="date" id="fecha_fin" wire:model.defer="fecha_fin"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-gray-900 placeholder:text-gray-500 focus:border-[#1D6FA7] focus:outline-none focus:ring-2 focus:ring-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900 " />
                                <x-input-error for="fecha_fin" class="mt-1.5" />
                            </div>

                            {{-- Valor del contrato --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-900">
                                    Valor del contrato
                                </label>
                                <div
                                    class="flex items-center rounded-lg border border-slate-300 bg-white text-gray-900 focus-within:ring-2 focus-within:ring-[#1D6FA7] focus-within:border-[#1D6FA7] dark:border-slate-600 dark:bg-slate-900 ">
                                    <div class="relative">
                                        <select wire:model.defer="moneda"
                                            class="h-full appearance-none rounded-l-lg border-r border-slate-300 bg-slate-50 py-2.5 pl-3 pr-8 text-gray-900 outline-none focus:ring-0 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                            @foreach ($monedas as $cod => $label)
                                                <option value="{{ $cod }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <span
                                            class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-slate-400">
                                            <i class="fa-solid fa-angle-down text-xs"></i>
                                        </span>
                                    </div>
                                    <input type="number" step="0.01" min="0" wire:model.defer="monto"
                                        class="w-full flex-1 border-none bg-transparent px-3 py-2 outline-none text-gray-900 placeholder:text-gray-500"
                                        placeholder="0.00" />
                                </div>
                                <x-input-error for="monto" class="mt-1.5" />
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </x-slot>

        {{-- ===== FOOTER ===== --}}
        <x-slot name="footer">
            <div class="flex w-full items-center justify-between gap-3">
                <button type="button" wire:click="$set('open', false)"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm transition-colors duration-200 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Cancelar
                </button>

                <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                    class="inline-flex items-center justify-center rounded-lg bg-[#1D6FA7] px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors duration-200 hover:bg-[#185D8C] focus:outline-none focus:ring-2 focus:ring-[#1D6FA7] focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-70">
                    <span wire:loading.remove wire:target="update" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Actualizar
                    </span>
                    <span wire:loading wire:target="update" class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Guardando...
                    </span>
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
