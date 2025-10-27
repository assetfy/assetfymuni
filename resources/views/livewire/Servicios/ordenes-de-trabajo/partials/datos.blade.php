 @php
     $trabajoList = $trabajo ?? [];
     $trabajoCount = count($trabajoList);
 @endphp

 <!-- Detalles del Trabajo -->
 <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
     <h2 class="text-lg font-semibold text-[#2F2F2F] mb-3 flex items-center gap-2">
         <i class="fa-solid fa-briefcase text-indigo-600"></i>
         Detalles del Trabajo
     </h2>
     <div class="text-sm text-gray-700">
         <!-- Aquí podés sumar chips/resúmenes si lo necesitás -->
     </div>
 </div>
 <!-- Imágenes de Trabajo + Firma lado a lado -->
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
     <!-- Carrusel de Imágenes de Trabajo -->
     <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 max-h-80 overflow-auto">
         <h3 class="font-semibold text-lg mb-3 border-b pb-2 flex items-center gap-2">
             <i class="fa-regular fa-images text-green-600"></i>
             Imágenes de Trabajo
         </h3>
         <div x-data="{ idxWork: 0, maxWork: {{ $trabajoCount }} }" x-cloak>
             @if ($trabajoCount > 0)
                 <div class="relative w-full h-56 rounded-lg border bg-gray-50 overflow-hidden">
                     @foreach ($trabajo as $i => $ruta)
                         <img x-show="idxWork === {{ $i }}"
                             src="{{ Storage::disk('s3')->temporaryUrl($ruta, now()->addMinutes(10)) }}"
                             alt="Trabajo {{ $i + 1 }}"
                             class="absolute inset-0 w-full h-full object-contain p-2" />
                     @endforeach

                     <button
                         class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                         @click="idxWork = idxWork > 0 ? idxWork - 1 : maxWork - 1">
                         <i class="fa-solid fa-chevron-left"></i>
                     </button>
                     <button
                         class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-2 bg-white/80 hover:bg-white shadow"
                         @click="idxWork = idxWork < maxWork - 1 ? idxWork + 1 : 0">
                         <i class="fa-solid fa-chevron-right"></i>
                     </button>
                 </div>
                 <div class="flex justify-center gap-1 mt-3">
                     @foreach ($trabajo as $i => $ruta)
                         <button class="h-1.5 w-4 rounded-full transition"
                             :class="idxWork === {{ $i }} ? 'bg-indigo-600' : 'bg-gray-300'"
                             @click="idxWork = {{ $i }}"></button>
                     @endforeach
                 </div>
             @else
                 <p class="text-center text-gray-500 mt-4">No hay imágenes de trabajo para esta orden.
                 </p>
             @endif
         </div>
     </div>

     <!-- Firma Guardada -->
     <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 max-h-80 overflow-auto">
         <h3 class="font-semibold text-lg mb-3 border-b pb-2 flex items-center gap-2">
             <i class="fa-solid fa-id-card-clip text-emerald-600"></i>
             Firma Guardada
         </h3>
         @if ($firma)
             <div class="w-full h-48 flex items-center justify-center">
                 <img src="{{ Storage::disk('s3')->temporaryUrl($firma->ruta_archivo, now()->addMinutes(10)) }}"
                     alt="Firma de cierre" class="max-h-full object-contain" />
             </div>
         @else
             <p class="text-center text-gray-500 mt-4">No hay firma guardada para esta orden.</p>
         @endif
     </div>
