 <!-- Previsualización de Proveedores -->
 @if (!empty($previewDataProveedores))
 <!-- Buscador para Proveedores -->
 <div>
     <input type="text" wire:model.lazy="searchProveedores" placeholder="Buscar por nombre en Proveedores..."
         class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
 </div>

 @php
 // Ajusta las claves según las que se usen en tu importación.
 $columnsToDisplay = [
 'razon_social' => 'Razón Social',
 'cuit' => 'CUIT',
 'localidad' => 'Localidad',
 'provincia' => 'Provincia',
 'ciudad' => 'Ciudad',
 'codigo_postal' => 'Código Postal',
 'calle' => 'Calle',
 'altura' => 'Altura',
 ];
 @endphp

 <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
     <h4 class="font-semibold text-lg flex items-center">
         <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
     </h4>
     <!-- Tabla con borde -->
     <table class="min-w-full border-collapse mt-2 border border-gray-300">
         <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
             <tr>
                 @foreach ($columnsToDisplay as $key => $label)
                 <th class="px-4 py-2 font-medium border border-gray-300">
                     {{ $label }}
                 </th>
                 @endforeach
             </tr>
         </thead>
         <tbody class="text-gray-600">
             @forelse ($paginatedProveedores as $index => $row)
             <tr class="hover:bg-gray-50 transition">
                 @foreach ($columnsToDisplay as $key => $label)
                 <td class="px-4 py-2 border border-gray-300">
                     {{ $row[$key] ?? '' }}
                 </td>
                 @endforeach
             </tr>
             @empty
             <tr>
                 <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
             </tr>
             @endforelse
         </tbody>
     </table>
     <div class="flex justify-first mt-2">
         <button wire:click="confirmarProveedores"
             class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold transition hover:bg-red-700 flex items-center gap-2">
             <i class="fas fa-save"></i> Importar Proveedores
         </button>
     </div>
     <div class="flex justify-first mt-4 mb-20">
         {{ $paginatedProveedores->links('livewire.custom-pagination') }}
     </div>
 </div>
 @endif