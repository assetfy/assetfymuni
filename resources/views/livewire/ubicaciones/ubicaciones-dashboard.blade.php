<div class="flex flex-col lg:flex-row px-4">
    <!-- Componente de Tarjetas -->
    <div class="w-full lg:w-3/4 px-4 mb-8 lg:mb-0">
        @livewire('activos.dashboard-activos', [
            'id_ubicacion' => request()->route('id_ubicacion'),
            'showCreateButton' => false, // Ocultar botón de crear
            'showAllButtons' => false, // Ocultar todos los demás botones
            'showQrButton' => false
        ])        
    </div>

    <!-- Calendario de servicios -->
    <div class="w-full lg:w-1/4 px-4">
        <div class="p-4 h-full border border-gray-300 rounded-lg">
            <h3 class="text-xl font-semibold mb-4 text-black text-center">Calendario de servicios</h3>
            <!-- Calendario -->
            <livewire:calendar />

            <h4 class="text-lg font-semibold mt-4">Próximos servicios</h4>
            <div class="mt-2 space-y-2">
                @if ($servicios->isEmpty())
                    <p class="text-gray-500">Sin servicios próximos</p>
                @else
                    @foreach ($servicios as $servicio)
                        @php
                            $serviceDate = $servicio->fecha_modificada
                                ? \Carbon\Carbon::parse($servicio->fecha_modificada)
                                : \Carbon\Carbon::parse($servicio->fechaHora);
                        @endphp
                        <div class="service-date p-2 rounded bg-blue-100 mb-2">
                            <p class="text-sm font-semibold">{{ $serviceDate->format('jS F') }}</p>
                            <p class="text-sm">{{ $servicio->descripcion }}</p>
                            <p class="text-sm text-gray-500">{{ $serviceDate->format('H:i') }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
