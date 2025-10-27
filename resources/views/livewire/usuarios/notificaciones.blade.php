<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Notificaciones</h2>
            </div>
            <div class="mt-4 flex space-x-1">
                <button wire:click="cambiarSeccion('todos')" class="btn-empresa rounded-lg">Todos</button>
                <button wire:click="cambiarSeccion('invitaciones')" class="btn-empresa rounded-lg">Invitaciones</button>
                <button wire:click="cambiarSeccion('auditorias')" class="btn-empresa rounded-lg">Auditorías</button>
                <button wire:click="cambiarSeccion('solicitudes')" class="btn-empresa rounded-lg">Solicitudes</button>
            </div>
        </div>

        <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
            @if ($selectedSection == 'todos')
                <h3 class="text-xl font-semibold leading-tight px-4 py-2">Todos</h3>
                @if ($notificaciones->count() > 0 || $auditorias->count() > 0 || $solicitudes->count() > 0)
                    @foreach ($notificaciones as $notificacion)
                        @foreach ($empresas as $empresa)
                        @if ($empresa->cuit == $notificacion->cuit)
                        <div class="flex items-center p-4 border-b border-gray-200">
                            <div class="flex-shrink-0">
                                <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                            </div>
                            <div class="ml-4 flex-grow">
                                <p class="block px-4 py-2 text-sm text-gray-700">
                                    <strong>{{ $empresa->razon_social }}</strong> te invita a unirte
                                </p>
                            </div>
                            <div class="ml-4 flex space-x-2">
                                <button wire:click="actualizarEstado('Aceptado', '{{ $empresa->cuit }}')" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button wire:click="actualizarEstado('Rechazado', '{{ $empresa->cuit }}')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    @endforeach

                    @foreach ($auditorias->unique('id') as $auditoria)
                    <div class="flex items-center p-4 border-b border-gray-200">
                        <div class="flex-shrink-0">
                            <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                        </div>
                        <div class="ml-4 flex-grow">
                            <a href="{{ route('estado') }}" class="ajax-link" @click.prevent="currentPage = 'estado'">
                                <p class="block px-4 py-2 text-sm text-gray-700">
                                    Auditoría realizada el <strong>{{ $auditoria->fecha_creacion }}</strong> con estado <strong>{{ $auditoria->estado }}</strong>
                                </p>
                            </a>
                        </div>
                    </div>
                    @endforeach

                    @foreach ($solicitudes as $solicitud)
                    <div class="flex items-center p-4 border-b border-gray-200">
                        <div class="flex-shrink-0">
                            <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                        </div>
                        <div class="ml-4 flex-grow">
                            @foreach ($prestadoras as $prestador)
                                @if ($prestador->cuit == $solicitud->empresa_prestadora)
                                <a href="{{ route('usuarios-servicios') }}" class="ajax-link" @click.prevent="currentPage = 'usuarios-servicios'">
                                    <p class="block px-4 py-2 text-sm text-gray-700">
                                        La empresa prestadora <strong>{{$prestador->razon_social }}</strong> con estado <strong>{{ $solicitud->estado_presupuesto}}</strong>
                                    </p>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center font-semibold text-black px-6 py-4 bg-yellow-100">
                    SIN INFORMACIÓN PARA MOSTRAR
                </div>
                @endif
            @elseif ($selectedSection == 'invitaciones')
                <h3 class="text-xl font-semibold leading-tight px-4 py-2">Invitaciones</h3>
                @if ($notificaciones->count() > 0)
                    @foreach ($notificaciones as $notificacion)
                        @foreach ($empresas as $empresa)
                        @if ($empresa->cuit == $notificacion->cuit)
                        <div class="flex items-center p-4 border-b border-gray-200">
                            <div class="flex-shrink-0">
                                <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                            </div>
                            <div class="ml-4 flex-grow">
                                <p class="block px-4 py-2 text-sm text-gray-700">
                                    <strong>{{ $empresa->razon_social }}</strong> te invita a unirte
                                </p>
                            </div>
                            <div class="ml-4 flex space-x-2">
                                <button wire:click="actualizarEstado('Aceptado', '{{ $empresa->cuit }}')" class="text-green-500 hover:text-green-700">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button wire:click="actualizarEstado('Rechazado', '{{ $empresa->cuit }}')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    @endforeach
                @else
                <div class="text-center font-semibold text-black px-6 py-4 bg-yellow-100 ">
                    SIN INVITACIONES PARA MOSTRAR
                </div>
                @endif
            @elseif ($selectedSection == 'auditorias')
                <h3 class="text-xl font-semibold leading-tight px-4 py-2">Auditorías</h3>
                @if ($auditorias->count() > 0)
                    @foreach ($auditorias->unique('id') as $auditoria)
                    <div class="flex items-center p-4 border-b border-gray-200">
                        <div class="flex-shrink-0">
                            <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                        </div>
                        <div class="ml-4 flex-grow">
                            <a href="{{ route('estado') }}" class="ajax-link" @click.prevent="currentPage = 'estado'">
                                <p class="block px-4 py-2 text-sm text-gray-700">
                                    Auditoría realizada el <strong>{{ $auditoria->fecha_creacion }}</strong> con estado <strong>{{ $auditoria->estado }}</strong>
                                </p>
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center font-semibold text-black px-6 py-4 bg-yellow-100">
                    SIN AUDITORÍAS PARA MOSTRAR
                </div>
                @endif
            @elseif ($selectedSection == 'solicitudes')
                <h3 class="text-xl font-semibold leading-tight px-4 py-2">Solicitudes de Servicios</h3>
                @if ($solicitudes->count() > 0)
                    @foreach ($solicitudes as $solicitud)
                    <div class="flex items-center p-4 border-b border-gray-200">
                        <div class="flex-shrink-0">
                            <span class="block w-3 h-3 bg-blue-500 rounded-full mt-1"></span>
                        </div>
                        <div class="ml-4 flex-grow">
                            @foreach ($prestadoras as $prestador)
                                @if ($prestador->cuit == $solicitud->empresa_prestadora)
                                <a href="{{ route('usuarios-servicios') }}" class="ajax-link" @click.prevent="currentPage = 'usuarios-servicios'">
                                    <p class="block px-4 py-2 text-sm text-gray-700">
                                        La empresa prestadora <strong>{{$prestador->razon_social }}</strong> con estado <strong>{{ $solicitud->estado_presupuesto }}</strong>
                                    </p>
                                </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center font-semibold text-black px-6 py-4 bg-yellow-100">
                    SIN SOLICITUDES PARA MOSTRAR
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
