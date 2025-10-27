<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Título del Dashboard -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Dashboard de Reportes</h2>

    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
        @foreach ([['Total de Bienes', 'blue-600', count($lista_activos)], ['Bienes En uso', 'green-600', count($lista_activos_normal)], ['Bienes en Estado Baja', 'orange-600', count($lista_activos_baja)], ['Cotizaciones', 'yellow-600', count($cotizaciones)], ['Servicios', 'purple-600', count($servicios)], ['Ubicaciones', 'red-600', count($lista_ubicaciones)]] as [$title, $color, $value])
        <div class="bg-white shadow-md rounded-md p-4 flex flex-col items-center text-center">
            <h3 class="text-sm font-semibold text-gray-700">{{ $title }}</h3>
            <p class="mt-2 text-3xl font-bold text-{{ $color }}">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <!-- Lista de Activos -->
    {{-- @if (count($lista_activos) > 0)
        <div class="bg-white shadow-md rounded-md p-4 mb-10 overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Lista de Activos</h3>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nombre</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tipo</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Estado</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Ubicación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lista_activos as $activo)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-gray-700">{{ $activo->nombre }}</td>
    <td class="px-4 py-2 text-gray-700">{{ $activo->tipo }}</td>
    <td class="px-4 py-2 text-gray-700">{{ $activo->estado }}</td>
    <td class="px-4 py-2 text-gray-700">{{ $activo->ubicacion ?? 'Sin ubicación' }}</td>
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@endif --}}

<!-- Lista de Ubicaciones y Cotizaciones -->
{{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        @if (count($lista_ubicaciones) > 0)
            <div class="bg-white shadow-md rounded-md p-4 overflow-x-auto">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Lista de Ubicaciones</h3>
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nombre</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Provincia</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-semibold">Ciudad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista_ubicaciones as $ubicacion)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">{{ $ubicacion->ubicacion }}</td>
<td class="px-4 py-2 text-gray-700">{{ $ubicacion->provincia }}</td>
<td class="px-4 py-2 text-gray-700">{{ $ubicacion->ciudad }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif

@if (count($cotizaciones) > 0)
<div class="bg-white shadow-md rounded-md p-4 overflow-x-auto">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Lista de Cotizaciones Solicitadas</h3>
    <table class="min-w-full table-auto text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Bienes</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tipo de Servicio</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Precio</th>
                <th class="px-4 py-3 text-left text-gray-600 font-semibold">Estado de presupuesto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cotizaciones as $cotizacion)
            <tr class="border-t">
                <td class="px-4 py-2 text-gray-700">{{ $cotizacion->nombre }}</td>
                <td class="px-4 py-2 text-gray-700">{{ $cotizacion->servicio_nombre }}</td>
                <td class="px-4 py-2 text-gray-700">${{ number_format($cotizacion->precio, 2) }}</td>
                <td class="px-4 py-2 text-gray-700">{{ $cotizacion->estado_presupuesto }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
</div> --}}

<!-- Lista de Servicios Realizados -->
{{-- @if (count($servicios) > 0)
        <div class="bg-white shadow-md rounded-md p-4 overflow-x-auto mb-10">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Lista de Servicios Realizados</h3>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nombre</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Tipo</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Precio</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Comentarios</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($servicios as $servicio)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-gray-700">{{ $servicio->nombre }}</td>
<td class="px-4 py-2 text-gray-700">{{ $servicio->servicio_nombre }}</td>
<td class="px-4 py-2 text-gray-700">${{ number_format($servicio->precio, 2) }}</td>
<td class="px-4 py-2 text-gray-700">{{ $servicio->comentarios }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif --}}

<!-- Gráficos -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Estado de Activos -->
    <div class="bg-white shadow-md rounded-md p-3">
        <h3 class="text-base font-semibold text-gray-700 mb-2 text-center">Estado de Bienes</h3>
        <div id="statesChart" class="w-full h-[380px] overflow-hidden flex items-center justify-center"></div>
    </div>

    <!-- Distribución Geográfica -->
    <div class="bg-white shadow-md rounded-md p-3">
        <h3 class="text-base font-semibold text-gray-700 mb-2 text-center">Distribución Geográfica</h3>
        <div id="locationsChart" class="w-full h-[380px] overflow-hidden flex items-center justify-center"></div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <!-- Tipos de Activos -->
    <div class="bg-white shadow-md rounded-md p-3">
        <h3 class="text-base font-semibold text-gray-700 mb-2 text-center">Tipos de Bienes</h3>
        <div id="typesChart" class="w-full h-[380px] overflow-hidden flex items-center justify-center"></div>
    </div>

    <!-- Resumen General -->
    <div class="bg-white shadow-md rounded-md p-3">
        <h3 class="text-base font-semibold text-gray-700 mb-2 text-center">Resumen General</h3>
        <div id="summaryChart" class="w-full h-[380px] overflow-hidden flex items-center justify-center"></div>
    </div>
</div>
</div>