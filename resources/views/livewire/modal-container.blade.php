<div>
    {{-- Overlay suave a pantalla completa --}}
    <div wire:loading.delay.shortest class="fixed inset-0 z-[9998] bg-white/70 backdrop-blur-sm" aria-hidden="true"></div>

    {{-- Tarjeta centrada en el viewport (no depende del contenedor) --}}
    <div wire:loading.delay.shortest class="fixed z-[9999] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
        aria-live="polite" aria-busy="true">
        <div class="flex items-center gap-3 rounded-2xl bg-white/95 px-5 py-4 shadow-xl ring-1 ring-black/5">
            {{-- Loader “tres puntos” sutil y moderno --}}
            <div class="flex items-center gap-1.5">
                <span
                    class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-bounce [animation-duration:900ms] [animation-delay:-0.20s]"></span>
                <span
                    class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-bounce [animation-duration:900ms] [animation-delay:-0.10s]"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-blue-600 animate-bounce [animation-duration:900ms]"></span>
            </div>
            <span class="text-sm font-medium text-slate-700">Cargando…</span>
        </div>
    </div>

    {{-- Contenido / modales (visible cuando NO está cargando) --}}
    <div wire:loading.remove>
        @foreach ($components as $component)
            @livewire($component, key($component))
        @endforeach
    </div>
</div>
