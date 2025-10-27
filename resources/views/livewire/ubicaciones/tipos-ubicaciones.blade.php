<div>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 py-12">

        <body class="antialiased font-sans bg-gray-200">
            <div class="container mx-auto px-4 sm:px-8">
                <div class="py-8">
                    <div>
                        <h2 class="text-2xl font-semibold leading-tight">TIPOS DE UBICACIONES</h2>
                    </div>
                    <div class="px-6 py-4 flex items-center">
                        <x-input class="flex-1 mr-4" placeholder="Búsqueda por sigla/nombre" type="text"
                            wire:model.live="search" />
                        @livewire('ubicaciones.crear-tipos-ubicaciones')
                    </div>
                    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                            @if ($tiposUbicaciones->count())
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                                wire:click="order('id_tipo')">
                                                Id
                                                <i class="fas {{ $this->sortIcon('id_tipo') }} float-right mt-1"></i>
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                                wire:click="order('nombre')">
                                                Nombre
                                                <i class="fas {{ $this->sortIcon('nombre') }} float-right mt-1"></i>
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Editar
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tiposUbicaciones as $tipos)
                                            <tr wire:key="{{ $tipos->id_tipo }}">
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">{{ $tipos->id_tipo }}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">{{ $tipos->nombre }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center font-semibold custom-bg text-black px-6 py-4">
                                    SIN REGISTROS PARA MOSTRAR
                                </div>
                            @endif
                            @if ($tiposUbicaciones->hasPages())
                                <div class="px-6 py-3">
                                    {{ $tiposUbicaciones->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </div>
</div>
</div>
