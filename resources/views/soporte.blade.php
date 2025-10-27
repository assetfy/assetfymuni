<x-app-layout>
    <div class="mx-auto w-full max-w-screen-lg 2xl:max-w-screen-xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white w-full rounded-2xl shadow-sm ring-1 ring-black/5">
            <form id="soporte-form" action="{{ route('soporte.store') }}" method="POST" enctype="multipart/form-data"
                x-data="soporteForm()" class="p-6 sm:p-8 lg:p-10 space-y-8">
                @csrf

                <!-- Encabezado -->
                <header class="space-y-2">
                    <h1 class="text-2xl font-semibold text-gray-900">Generación de un caso para Soporte</h1>
                    <p class="text-sm text-gray-600">
                        Completá los campos y adjuntá material si lo necesitás. Mientras más contexto, más rápido te
                        ayudamos.
                    </p>
                </header>

                <!-- Asunto -->
                <section>
                    <div class="flex items-baseline justify-between gap-3">
                        <label for="asunto" class="block text-sm font-medium text-gray-800">Asunto</label>
                        <span class="text-xs text-gray-500" x-text="`${asunto.length}/120`"></span>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        Una frase breve que resuma el problema o solicitud. Ej.: <em>“Error al adjuntar PDF en
                            Órdenes”</em>
                    </p>

                    <div class="relative mt-2">
                        <i class="fa-solid fa-heading absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="asunto" name="asunto" x-model="asunto" maxlength="120" required
                            placeholder="Contanos en una línea de qué se trata"
                            class="w-full pl-12 pr-12 py-3 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition ring-1 ring-gray-200" />
                    </div>
                </section>

                <!-- Descripción -->
                <section>
                    <div class="flex items-baseline justify-between gap-3">
                        <label for="descripcion" class="block text-sm font-medium text-gray-800">Descripción</label>
                        <span class="text-xs text-gray-500" x-text="`${descripcion.length}/2000`"></span>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">
                        Detallá lo que esperabas que ocurra, lo que ocurrió, pasos para reproducir y el impacto. Podés
                        pegar capturas.
                    </p>

                    <div class="relative mt-2">
                        <i class="fa-solid fa-align-left absolute left-4 top-4 text-gray-400"></i>
                        <textarea id="descripcion" name="descripcion" x-model="descripcion" maxlength="2000" required rows="7"
                            placeholder="Ej.: 1) Voy a Órdenes → 2) Hago clic en Adjuntar → 3) Selecciono PDF → 4) Aparece el mensaje “x”. Ocurre desde hoy 10:30. Impacto: no puedo cerrar órdenes."
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition ring-1 ring-gray-200 resize-y"></textarea>
                    </div>
                </section>

                <!-- Adjuntos -->
                <section x-data x-on:dragover.prevent class="space-y-3">
                    <label class="block text-sm font-medium text-gray-800">Adjuntos <span
                            class="text-gray-400 font-normal">(opcional)</span></label>
                    <p class="text-xs text-gray-500">
                        PNG, JPG, PDF — hasta 10&nbsp;MB por archivo. Arrastrá y soltá, o hacé clic para seleccionar.
                    </p>

                    <!-- Área de drop / selector -->
                    <div class="rounded-xl bg-gray-50 ring-1 ring-dashed ring-gray-300 hover:ring-blue-400 transition p-6 cursor-pointer"
                        :class="{ 'ring-blue-400 bg-blue-50/50': dropping }" @dragenter.prevent="dropping = true"
                        @dragleave.prevent="dropping = false" @drop.prevent="handleDrop($event)"
                        @click="$refs.input.click()">
                        <div class="flex items-center gap-3 text-gray-700">
                            <i class="fa-solid fa-paperclip text-gray-400"></i>
                            <span class="text-sm font-medium">Elegir archivos o soltarlos aquí</span>
                        </div>

                        <input x-ref="input" type="file" id="adjunto" name="adjunto[]" multiple class="hidden"
                            @change="syncList($event)" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt" />
                    </div>

                    <!-- Lista de archivos -->
                    <template x-if="files.length">
                        <ul class="mt-2 space-y-2">
                            <template x-for="(f, i) in files" :key="i">
                                <li
                                    class="flex items-center justify-between gap-3 rounded-lg bg-white ring-1 ring-gray-200 px-3 py-2">
                                    <div class="flex items-center gap-2 truncate">
                                        <i class="fa-regular fa-file text-gray-400"></i>
                                        <span class="text-sm text-gray-700 truncate" x-text="f.name"></span>
                                        <span class="text-xs text-gray-400" x-text="niceSize(f.size)"></span>
                                    </div>
                                    <button type="button" @click="remove(i)"
                                        class="text-xs text-red-600 hover:underline">Quitar</button>
                                </li>
                            </template>
                        </ul>
                    </template>
                </section>

                <!-- Botón -->
                <div class="flex items-center justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition shadow-sm">
                        <i class="fa-solid fa-paper-plane"></i>
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alpine helpers (usa Alpine de Jetstream) -->
    <script>
        function soporteForm() {
            return {
                asunto: '',
                descripcion: '',
                files: [],
                dropping: false,

                // Copia la lista del input a "files" para mostrar
                syncList(e) {
                    this.files = Array.from(e.target.files ?? []);
                },

                // Soporte drag & drop
                handleDrop(e) {
                    this.dropping = false;
                    const dropped = Array.from(e.dataTransfer.files ?? []);
                    if (!dropped.length) return;

                    // Rellena el input real con DataTransfer (para que se envíe)
                    const dt = new DataTransfer();
                    [...(this.$refs.input.files ?? []), ...dropped].forEach(f => dt.items.add(f));
                    this.$refs.input.files = dt.files;
                    this.files = Array.from(this.$refs.input.files);
                },

                remove(index) {
                    const dt = new DataTransfer();
                    this.files
                        .filter((_, i) => i !== index)
                        .forEach(f => dt.items.add(f));
                    this.$refs.input.files = dt.files;
                    this.files = Array.from(this.$refs.input.files);
                },

                niceSize(bytes) {
                    if (!bytes && bytes !== 0) return '';
                    const kb = bytes / 1024,
                        mb = kb / 1024;
                    return mb >= 1 ? `${mb.toFixed(1)} MB` : `${Math.ceil(kb)} KB`;
                }
            }
        }
    </script>
</x-app-layout>
