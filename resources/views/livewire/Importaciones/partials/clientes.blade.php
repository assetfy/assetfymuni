@if (!empty($previewDataClientes))
<!-- Buscador para Clientes -->
<div>
    <!-- Cambiamos la variable de estado a "searchClientes" -->
    <input type="text" wire:model.lazy="searchClientes" placeholder="Buscar por nombre de cliente..."
        class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
</div>

@php
// Ajustamos las columnas según las que se usen en la importación de clientes.
$columnsToDisplay = [
'name' => 'Nombre',
'email' => 'Correo electrónico',
'cuil' => 'CUIL',
'password' => 'Contraseña',
];
@endphp

<div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
    <h4 class="font-semibold text-lg flex items-center">
        <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos (Clientes):
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
            @forelse ($paginatedClientes as $index => $row)
            <tr class="hover:bg-gray-50 transition">
                @foreach ($columnsToDisplay as $key => $label)
                <td class="px-4 py-2 border border-gray-300">
                    {{ $row[$key] ?? '' }}
                </td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">
                    Sin resultados
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-first mt-2">
        <!-- Cambiamos la llamada al método de Livewire para confirmar clientes -->
        <button wire:click="confirmarClientes"
            class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
            <i class="fas fa-save"></i> Importar Clientes
        </button>
    </div>
    <div class="flex justify-first mt-4 mb-20">
        {{ $paginatedClientes->links('livewire.custom-pagination') }}
    </div>
</div>
@endif