<div>
    @if($activosatributos)
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 py-12">
        <body class="antialiased font-sans bg-gray-200">
            <div class="container mx-auto px-4 sm:px-8">
                <div class="py-8">
                    <div>
                        <h2 class="text-2xl font-semibold leading-tight">Activos Atributos</h2>
                    </div>
                    <div class="px-6 py-4 flex items-center">
                        <x-input class="flex-1 mr-4" placeholder="Busqueda" type="text" wire:model.live="search" />
                    </div>
                    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Atributos
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Activo
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Subcategoria
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Categoria
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Categoria
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Campo
                                            </th>
                                            <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Campo Numerico
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Editar
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($activosatributos as $atributos)
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @foreach ($atributo as $atr)
                                                            @if ($atributos->id_atributo == $atr->id_atributo)
                                                                {{ $atr->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @foreach ($activos as $activo)
                                                            @if ($atributos->id_activo == $activo->id_activo)
                                                                {{ $activo->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @foreach ($subcat as $sub)
                                                            @if ($atributos->id_subcategoria_activo == $sub->id_subcategoria)
                                                                {{ $sub->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @foreach ($categorias as $cat)
                                                            @if ($atributos->id_categoria_activo == $cat->id_categoria)
                                                                {{ $cat->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        @foreach ($tipos as $tipo)
                                                            @if ($atributos->id_tipo_activo == $tipo->id_tipo)
                                                                {{ $tipo->nombre }}
                                                            @endif
                                                        @endforeach
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    @foreach($tiposcampos as $tipocampo)
                                                        @if($tipocampo->nombre == 'Numerico')
                                                            @php
                                                                $datosAsociados = false; // Reiniciar para cada tipo de campo alfanumérico
                                                            @endphp

                                                            @foreach($atributo as $atr)
                                                                @if($atributos->id_atributo == $atr->id_atributo)
                                                                    @if (!is_null($atributos->campo))
                                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                                            {{ $atributos->campo }}
                                                                        </p>
                                                                        @php
                                                                            $datosAsociados = true;
                                                                        @endphp
                                                                    @endif
                                                                @endif
                                                            @endforeach

                                                            @if (!$datosAsociados)
                                                                <p>No hay datos asociados</p>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    @foreach($tiposcampos as $tipocampo)
                                                        @if($tipocampo->nombre == 'Alfanumerico')
                                                            @php
                                                                $datosAsociadosEncontrados = false; // Reiniciar para cada tipo de campo alfanumérico
                                                            @endphp

                                                            @foreach($atributo as $atr)
                                                                @if($atributos->id_atributo == $atr->id_atributo)
                                                                    @if (!is_null($atributos->campo_numerico))
                                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                                            {{ $atributos->campo_numerico }}
                                                                        </p>
                                                                        @php
                                                                            $datosAsociadosEncontrados = true;
                                                                        @endphp
                                                                    @endif
                                                                @endif
                                                            @endforeach

                                                            @if (!$datosAsociadosEncontrados)
                                                                <p>No hay datos asociados</p>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td class="px-2 py-5 border-b border-gray-200 bg-white text-sm">
                                                    @livewire('atributos.editar-nuevos-atributos', ['atributo' => $atributos], key($atributos->id_atributox))
                                                </td>
                                    </tbody>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </div>
</div>
@endif
