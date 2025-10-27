{{-- ====== Servicios y Materiales ====== --}}
<h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
    <i class="fa-solid fa-list-check text-indigo-600"></i>
    Servicios y Materiales
</h3>

{{-- Switches superiores --}}
<div class="flex flex-wrap gap-4 bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
    <label class="inline-flex items-center gap-2 text-sm text-gray-800">
        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            wire:model="incluyeMateriales" />
        <i class="fa-solid fa-box-open text-gray-500"></i> Incluye Materiales
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-800">
        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            wire:model="manoDeObra" />
        <i class="fa-solid fa-person-digging text-gray-500"></i> Mano de Obra
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-gray-800">
        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            wire:model.live="inspeccion" />
        <i class="fa-solid fa-clipboard-list text-gray-500"></i> Incluye Inspección
    </label>
</div>

{{-- Garantías: dos columnas como en la captura --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-4 bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <label class="block font-medium text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-shield-heart text-indigo-600"></i>
            ¿El servicio tiene garantía?
        </label>
        <div class="flex gap-6">
            <label class="flex items-center gap-2">
                <input type="radio" wire:model.live="servicio_con_garantia" value="si"
                    class="text-blue-600 focus:ring-blue-500">
                <span>Sí</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" wire:model="servicio_con_garantia" value="no"
                    class="text-blue-600 focus:ring-blue-500">
                <span>No</span>
            </label>
        </div>
        @if ($servicio_con_garantia === 'si')
            <div>
                <label for="fecha_expiracion_garantia_servicio" class="block text-sm font-medium text-gray-700">
                    Expiración Garantía Servicio
                </label>
                <input type="date" id="fecha_expiracion_garantia_servicio"
                    wire:model="fecha_expiracion_garantia_servicio" min="{{ date('Y-m-d') }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                @error('fecha_expiracion_garantia_servicio')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endif
    </div>

    <div class="space-y-4 bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
        <label class="block font-medium text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
            ¿Los repuestos tienen garantía?
        </label>
        <div class="flex gap-6">
            <label class="flex items-center gap-2">
                <input type="radio" wire:model.live="repuestos_con_garantia" value="si"
                    class="text-blue-600 focus:ring-blue-500">
                <span>Sí</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" wire:model="repuestos_con_garantia" value="no"
                    class="text-blue-600 focus:ring-blue-500">
                <span>No</span>
            </label>
        </div>
        @if ($repuestos_con_garantia === 'si')
            <div>
                <label for="fecha_expiracion_garantia_repuestos" class="block text-sm font-medium text-gray-700">
                    Expiración Garantía Repuestos
                </label>
                <input type="date" id="fecha_expiracion_garantia_repuestos"
                    wire:model="fecha_expiracion_garantia_repuestos" min="{{ date('Y-m-d') }}"
                    class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                @error('fecha_expiracion_garantia_repuestos')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endif
    </div>
</div>

{{-- Horas (dos columnas como la captura) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="text-sm font-medium text-gray-700 flex items-center gap-2">
            <i class="fa-regular fa-clock text-gray-600"></i> Hora de Llegada
        </label>
        <input type="time" wire:model.live="horaLlegada"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
    </div>
    <div>
        <label class="text-sm font-medium text-gray-700 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-gray-600"></i> Hora de Retiro
        </label>
        <input type="time" wire:model.live="horaRetiro"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
    </div>
</div>

{{-- Buscadores y listas (dos columnas) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Servicios --}}
    <div>
        <div class="mb-1 flex items-center gap-2 text-gray-800 font-medium">
            <i class="fa-solid fa-screwdriver-wrench text-gray-600"></i> <span>Servicios</span>
        </div>
        <x-searchable-dropdown label="Servicios" icon="fa-solid fa-screwdriver-wrench" :options="$servicios"
            model="id_servicio" search-model="searchServicio" select-method="setServicio" value-key="id_servicio"
            label-key="nombre" :selected="$selectedServicioNombre" />
        {{-- Lista de servicios seleccionados con cantidad --}}
        @if (!empty($serviciosSeleccionados))
            <ul class="mt-2 space-y-2">
                @foreach ($serviciosSeleccionados as $idx => $s)
                    <li class="flex items-center gap-2 bg-gray-50 border rounded px-2 py-1.5">
                        <i class="fa-solid fa-screwdriver-wrench text-gray-500"></i>
                        <span class="flex-1 truncate">{{ $s['nombre'] }}</span>
                        <input type="number" min="1" step="1"
                            class="w-20 border border-gray-300 rounded px-2 py-1 text-sm"
                            wire:model.lazy="serviciosSeleccionados.{{ $idx }}.cantidad" />
                        <button type="button" class="text-red-600 hover:text-red-700"
                            wire:click="quitarServicio({{ $s['id_servicio'] }})" title="Quitar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- Materiales --}}
    {{-- <div>
        <div class="mb-1 flex items-center gap-2 text-gray-800 font-medium">
            <i class="fa-solid fa-box-open text-gray-600"></i> <span>Materiales</span>
        </div>
        <x-searchable-dropdown label="Material" icon="fa-solid fa-box" :options="$materiales" model="id_material"
            search-model="searchMaterial" select-method="setMaterial" value-key="id_material" label-key="nombre"
            :selected="$selectedMaterialNombre" />
        {{-- Lista de materiales seleccionados con cantidad 
        @if (!empty($materialesSeleccionados))
            <ul class="mt-2 space-y-2">
                @foreach ($materialesSeleccionados as $idx => $m)
                    <li class="flex items-center gap-2 bg-gray-50 border rounded px-2 py-1.5">
                        <i class="fa-solid fa-box text-gray-500"></i>
                        <span class="flex-1 truncate">{{ $m['nombre'] }}</span>
                        <input type="number" min="1" step="1"
                            class="w-20 border border-gray-300 rounded px-2 py-1 text-sm"
                            wire:model.lazy="materialesSeleccionados.{{ $idx }}.cantidad" />
                        <button type="button" class="text-red-600 hover:text-red-700"
                            wire:click="quitarMaterial({{ $m['id_material'] }})" title="Quitar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div> --}}
</div>

{{-- Comentarios --}}
<h3 class="mt-4 mb-2 font-semibold text-gray-800 flex items-center gap-2">
    <i class="fa-regular fa-comments text-blue-600"></i>
    Comentarios / Observaciones
</h3>
<textarea wire:model.live="comentariosCierre"
    class="w-full border border-gray-300 rounded-md p-3 text-sm focus:ring-blue-500 focus:border-blue-500"
    placeholder="Observaciones..."></textarea>

{{-- Imágenes y Firma --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
    <div class="space-y-2 text-sm">
        <h3 class="font-semibold flex items-center gap-2">
            <i class="fa-solid fa-upload text-indigo-600"></i> Subir Imagen(es) del Trabajo
        </h3>

        <input type="file" wire:model="newImages" multiple wire:key="newImages-{{ $open ? '1' : '0' }}"
            class="w-full text-sm text-gray-700" />
        @error('newImages.*')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror

        @if ($imagenesTrabajo && count($imagenesTrabajo) > 0)
            <ul class="mt-1 space-y-1">
                @foreach ($imagenesTrabajo as $index => $file)
                    <li class="flex justify-between items-center gap-2 bg-gray-50 px-3 py-1.5 rounded border">
                        <span class="truncate">
                            <i class="fa-regular fa-file-image mr-1 text-gray-500"></i>
                            {{ $file->getClientOriginalName() }}
                        </span>
                        <button type="button" wire:click="removeImage({{ $index }})"
                            class="text-red-600 hover:text-red-700" title="Quitar">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($inspeccion)
            <div class="mt-2">
                <h3 class="font-semibold mb-1 flex items-center gap-2">
                    <i class="fa-regular fa-file-pdf text-rose-600"></i> Documento de Inspección
                </h3>
                <input type="file" wire:model="archivoInspeccion" wire:key="inspeccion-{{ $open ? '1' : '0' }}"
                    class="w-full border border-gray-300 rounded-md px-2 py-2 text-sm" />
                @error('archivoInspeccion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </div>

    <div x-data="{
        signaturePad: null,
        init() {
            const canvas = this.$refs.canvas;
            const parent = canvas.parentElement;
            const resizeCanvas = () => {
                canvas.width = parent.clientWidth;
                canvas.height = 150;
                if (this.signaturePad) this.signaturePad.clear();
            };
            resizeCanvas();
            this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgba(255,255,255,0)', penColor: 'rgb(0,0,0)' });
            new ResizeObserver(resizeCanvas).observe(parent);
        }
    }" x-init="init()" x-effect="if ($wire.open) signaturePad.clear()"
        class="relative text-sm">
        <h3 class="font-semibold mb-2 flex items-center gap-2">
            <i class="fa-solid fa-signature text-emerald-600"></i> Firma
        </h3>
        <div class="relative w-full" style="height: 150px;">
            <canvas x-ref="canvas" class="absolute inset-0 border rounded-md bg-white"></canvas>
        </div>
        <div class="flex justify-end gap-2 mt-2">
            <button type="button" @click="signaturePad.clear()"
                class="px-3 py-1.5 rounded-md border border-gray-300 hover:bg-gray-50 text-gray-700">
                <i class="fa-solid fa-eraser mr-1"></i> Limpiar
            </button>
            <button type="button" @click="$wire.firmaCapturada(signaturePad.toDataURL('image/png'))"
                class="px-3 py-1.5 rounded-md bg-blue-600 text-white hover:bg-blue-700 shadow">
                <i class="fa-solid fa-check mr-1"></i> OK
            </button>
        </div>
    </div>
</div>
