<div class="p-6 space-y-12">
    {{-- Definición de la función calcularEstrellas solo una vez --}}
    @once
        @php
            if (!function_exists('calcularEstrellas')) {
                /**
                 * Función para calcular cuántas estrellas completas, medias y vacías
                 * se deben mostrar dado un rating en escala 0–5.
                 *
                 * @param float $rating Valor entre 0 y 5.
                 * @return array ['full' => int, 'half' => int, 'empty' => int]
                 */
                function calcularEstrellas($rating)
                {
                    $fullStars = floor($rating);
                    $halfStars = $rating - $fullStars >= 0.5 ? 1 : 0;
                    $emptyStars = 5 - $fullStars - $halfStars;
                    return ['full' => $fullStars, 'half' => $halfStars, 'empty' => $emptyStars];
                }
            }
        @endphp
    @endonce

    @php
        // Calculamos la estructura de estrellas para cada puntaje
        $estrellasGoogle = calcularEstrellas($puntajeGoogle);
        $estrellasAssetFly = calcularEstrellas($puntajeAssetFly);
    @endphp

    <!-- 🔹 Sección de Datos Generales y Clientes Recurrentes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- 📋 Datos Generales -->
        <div class="p-6 space-y-4 bg-white rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-center">📋 Datos Generales</h3>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-700 font-semibold">🔹 Total Global:</span>
                <span class="text-blue-600 text-lg font-bold">{{ $totalGlobal }}</span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-700 font-semibold">✅ % Recomendado:</span>
                <span class="text-green-600 text-lg font-bold">{{ $porcentajeContratacion1 }}%</span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-700 font-semibold">❌ % No Recomendado:</span>
                <span class="text-red-600 text-lg font-bold">{{ $porcentajeContratacion0 }}%</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-700 font-semibold">📌 Servicios realizados:</span>
                <span class="text-blue-600 text-lg font-bold">{{ $serviciosFinalizados }}</span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-700 font-semibold">⭐ Puntaje en Google Places:</span>
                <span class="text-yellow-600 text-lg font-bold">
                    @for ($i = 0; $i < $estrellasGoogle['full']; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    @if ($estrellasGoogle['half'])
                        <i class="fas fa-star-half-alt"></i>
                    @endif
                    @for ($i = 0; $i < $estrellasGoogle['empty']; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                    <span class="ml-2">({{ $puntajeGoogle }} / 5)</span>
                </span>
            </div>
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-gray-700 font-semibold">⭐ Puntaje en Asset Fly:</span>
                <span class="text-yellow-600 text-lg font-bold">
                    @for ($i = 0; $i < $estrellasAssetFly['full']; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    @if ($estrellasAssetFly['half'])
                        <i class="fas fa-star-half-alt"></i>
                    @endif
                    @for ($i = 0; $i < $estrellasAssetFly['empty']; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                    <span class="ml-2">({{ $puntajeAssetFly }} / 5)</span>
                </span>
            </div>
        </div>

        <!-- 🏆 Tabla de Clientes Recurrentes con scroll y paginación (si es paginada) -->
        <div class="p-6 bg-white rounded-lg shadow-md">
            <h3 class="text-xl font-semibold mb-4 text-center">🏆 Clientes Recurrentes</h3>
            @if ($ClientesRecurrentes->count())
                <div class="overflow-y-auto" style="max-height: 300px;">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                            <tr>
                                <th class="p-3 text-left">#</th>
                                <th class="p-3 text-left">Cliente</th>
                                <th class="p-3 text-center">Solicitudes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($ClientesRecurrentes as $index => $cliente)
                                @php
                                    $empresa = $empresas->firstWhere('cuit', $cliente->empresa_solicitante);
                                    $usuario = $usuarios->firstWhere('id', $cliente->id_solicitante);
                                    $nombreCliente = $empresa
                                        ? $empresa->razon_social
                                        : ($usuario
                                            ? $usuario->name
                                            : 'NA');
                                    // Si el objeto tiene el método currentPage() se usa la numeración global; de lo contrario, solo la numeración del loop.
                                    $numero = method_exists($ClientesRecurrentes, 'currentPage')
                                        ? ($ClientesRecurrentes->currentPage() - 1) * $ClientesRecurrentes->perPage() +
                                            $loop->iteration
                                        : $loop->iteration;
                                @endphp
                                <tr class="hover:bg-gray-100 transition">
                                    <td class="p-3 text-gray-600 font-semibold">{{ $numero }}</td>
                                    <td class="p-3 text-gray-800">{{ $nombreCliente }}</td>
                                    <td class="p-3 text-center text-lg font-bold text-blue-600">
                                        {{ $cliente->solicitud_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Mostrar enlaces de paginación solo si están disponibles --}}
                @if (method_exists($ClientesRecurrentes, 'links'))
                    <div class="mt-2">
                        {{ $ClientesRecurrentes->links() }}
                    </div>
                @endif
            @else
                <p class="text-center text-gray-500">No se encontraron clientes recurrentes.</p>
            @endif
        </div>
    </div>

    <!-- 📊 Sección de Gráficos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        @php
            $chartContratacionUrl =
                'https://quickchart.io/chart?w=300&h=300&c=' .
                urlencode(
                    json_encode([
                        'type' => 'doughnut',
                        'data' => [
                            'datasets' => [
                                [
                                    'data' => [(float) $porcentajeContratacion1, (float) $porcentajeContratacion0],
                                    'backgroundColor' => ['#4CAF50', '#FF5733'],
                                ],
                            ],
                        ],
                        'options' => [
                            'plugins' => [
                                'legend' => ['display' => true, 'position' => 'right'],
                                'datalabels' => [
                                    'color' => '#000',
                                    'font' => ['size' => 18, 'weight' => 'bold'],
                                    'formatter' => 'function(value) { return value + "%"; }',
                                ],
                            ],
                        ],
                    ]),
                );
        @endphp

        <div class="text-center flex flex-col items-center space-y-6">
            <h3 class="text-xl font-semibold mb-2">📊 Distribución de Contrataciones</h3>

            <!-- 🔹 Etiquetas en horizontal -->
            <div class="flex justify-center gap-6">
                <p class="flex items-center gap-2">
                    <span class="w-5 h-5 bg-green-500 inline-block rounded-full"></span>
                    <span class="text-gray-700 font-semibold">Recomendado</span>
                </p>
                <p class="flex items-center gap-2">
                    <span class="w-5 h-5 bg-red-500 inline-block rounded-full"></span>
                    <span class="text-gray-700 font-semibold">No Recomendado</span>
                </p>
            </div>

            <!-- 🔹 Gráfico circular -->
            <img src="{{ $chartContratacionUrl }}" alt="Gráfico de Contratación"
                class="w-64 h-64 md:w-72 md:h-72 hover:scale-105 transition">
        </div>

        @php
            $chartMotivosContratarUrl =
                'https://quickchart.io/chart?width=400&height=250&c=' .
                urlencode(
                    json_encode([
                        'type' => 'bar',
                        'data' => [
                            'labels' => ['Diagnóstico', 'Precio', 'Calificación'],
                            'datasets' => [
                                [
                                    'label' => 'Recomiendan',
                                    'data' => [
                                        $rankingMotivos['recomiendan']['diagnostico'] ?? 0,
                                        $rankingMotivos['recomiendan']['precio'] ?? 0,
                                        $rankingMotivos['recomiendan']['calificacion'] ?? 0,
                                    ],
                                    'backgroundColor' => ['#FFCC00', '#2196F3', '#8E44AD'],
                                ],
                            ],
                        ],
                    ]),
                );
            $chartMotivosNoContratarUrl =
                'https://quickchart.io/chart?width=400&height=250&c=' .
                urlencode(
                    json_encode([
                        'type' => 'bar',
                        'data' => [
                            'labels' => ['Diagnóstico', 'Precio', 'Calificación'],
                            'datasets' => [
                                [
                                    'label' => 'No Recomiendan',
                                    'data' => [
                                        $rankingMotivos['no_recomiendan']['diagnostico'] ?? 0,
                                        $rankingMotivos['no_recomiendan']['precio'] ?? 0,
                                        $rankingMotivos['no_recomiendan']['calificacion'] ?? 0,
                                    ],
                                    'backgroundColor' => ['#FFCC00', '#2196F3', '#8E44AD'],
                                ],
                            ],
                        ],
                    ]),
                );
        @endphp

        <div class="text-center">
            <h3 class="text-xl font-semibold mb-2">📊 Motivos para Contratar</h3>
            <img src="{{ $chartMotivosContratarUrl }}" alt="Motivos para Contratar"
                class="mx-auto w-auto max-w-xs md:max-w-md hover:scale-105 transition">
        </div>

        <div class="text-center">
            <h3 class="text-xl font-semibold mb-2">📉 Motivos para NO Contratar</h3>
            <img src="{{ $chartMotivosNoContratarUrl }}" alt="Motivos para NO Contratar"
                class="mx-auto w-auto max-w-xs md:max-w-md hover:scale-105 transition">
        </div>
    </div>
</div>
