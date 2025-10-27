<!-- Descripción de la Solicitud -->
<div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
    <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
        <i class="fa-regular fa-note-sticky text-amber-600"></i>
        Descripción de la Solicitud
    </h2>
    <div class="rounded-lg p-4 min-h-[80px] bg-gray-50 text-gray-800 leading-relaxed">
        {{ $orden->comentarios ?? 'N/A' }}
    </div>
</div>
<br>
<!-- Visores de Imágenes -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Visor de Imágenes del Bien -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <h3 class="font-semibold text-lg mb-3 border-b pb-2 flex items-center gap-2">
            <i class="fa-regular fa-image text-green-600"></i>
            Visor de Imágenes del Bien
        </h3>

        @if (!empty($imagenesActivos) && count($imagenesActivos) > 0)
            <div x-data="{ currentIndex: 0, total: {{ count($imagenesActivos) }} }" class="space-y-3">
                <div class="relative w-full h-56 rounded-lg border bg-gray-50 overflow-hidden">
                    @foreach ($imagenesActivos as $index => $url)
                        <img x-show="currentIndex === {{ $index }}" src="{{ $url }}"
                            alt="Imagen activo {{ $index + 1 }}"
                            class="absolute inset-0 w-full h-full object-contain p-2">
                    @endforeach

                    <!-- Controles -->
                    <button type="button"
                        class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                        @click="currentIndex = currentIndex > 0 ? currentIndex - 1 : total - 1">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                        @click="currentIndex = currentIndex < total - 1 ? currentIndex + 1 : 0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="flex justify-center gap-1">
                    @foreach ($imagenesActivos as $index => $url)
                        <button class="h-1.5 w-4 rounded-full transition"
                            :class="currentIndex === {{ $index }} ? 'bg-indigo-600' : 'bg-gray-300'"
                            @click="currentIndex = {{ $index }}"></button>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-center text-gray-500">Sin fotos del bien</p>
        @endif
    </div>

    <!-- Imágenes de Info -->
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <h3 class="font-semibold text-lg mb-3 border-b pb-2 flex items-center gap-2">
            <i class="fa-regular fa-images text-teal-600"></i>
            Imágenes adicionales
        </h3>

        @if (!empty($imagenesInfo) && count($imagenesInfo) > 0)
            <div x-data="{ currentInfoIndex: 0, total: {{ count($imagenesInfo) }} }" class="space-y-3">
                <div class="relative w-full h-56 rounded-lg border bg-gray-50 overflow-hidden">
                    @foreach ($imagenesInfo as $index => $url)
                        <img x-show="currentInfoIndex === {{ $index }}" src="{{ $url }}"
                            alt="Imagen info {{ $index + 1 }}"
                            class="absolute inset-0 w-full h-full object-contain p-2">
                    @endforeach

                    <button type="button"
                        class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                        @click="currentInfoIndex = currentInfoIndex > 0 ? currentInfoIndex - 1 : total - 1">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                        @click="currentInfoIndex = currentInfoIndex < total - 1 ? currentInfoIndex + 1 : 0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="flex justify-center gap-1">
                    @foreach ($imagenesInfo as $index => $url)
                        <button class="h-1.5 w-4 rounded-full transition"
                            :class="currentInfoIndex === {{ $index }} ? 'bg-emerald-600' : 'bg-gray-300'"
                            @click="currentInfoIndex = {{ $index }}"></button>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-center text-gray-500">No hay imágenes de tipo “Info”.</p>
        @endif
    </div>
</div>
