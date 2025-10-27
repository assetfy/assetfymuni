@extends('layouts.landing')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 py-12">
        <div class="mt-4 mb-6">
            <a href="javascript:window.history.back();"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm">
                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver
            </a>

        </div>
        @if ($activo)
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center uppercase">Detalles Activo</h2>
            <!-- Primera Sección: Tipo, Categoría, Subcategoría -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700">Tipo</h3>
                        <p class="mt-2 text-gray-900">{{ $activo->tipo->nombre ?? 'Sin datos' }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700">Categoría</h3>
                        <p class="mt-2 text-gray-900">{{ $activo->categoria->nombre ?? 'Sin datos' }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700">Subcategoría</h3>
                        <p class="mt-2 text-gray-900">{{ $activo->subcategoria->nombre ?? 'Sin datos' }}</p>
                    </div>
                </div>
            </div>
            <!-- Segunda Sección: Activo, Ubicación -->
            <div class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700">Activo</h3>
                        <p class="mt-2 text-gray-900">{{ !empty($activo->nombre) ? $activo->nombre : 'Sin datos' }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-700">Ubicación</h3>
                        <p class="mt-2 text-gray-900">{{ $activo->ubicacion->nombre ?? 'Sin datos' }}</p>
                    </div>
                </div>
            </div>
            <!-- Tercera Sección: Información de Alta y Baja -->
            <div class="mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Información de Alta y Baja</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Estado de Alta</h4>
                            <p class="mt-1 text-gray-900">{{ $activo->estadoAlta->nombre ?? 'Sin datos' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Comentarios de Alta</h4>
                            <p class="mt-1 text-gray-900">
                                {{ !empty($activo->comentarios_sit_alta) ? $activo->comentarios_sit_alta : 'Sin datos' }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Estado Inventario</h4>
                            <p class="mt-1 text-gray-900">
                                {{ !empty($activo->estado_inventario) ? $activo->estado_inventario : 'Sin datos' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cuarta Sección: Estado General y Estado Inventario -->
            <div class="mb-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Estado</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Estado General</h4>
                            <p class="mt-1 text-gray-900">{{ $activo->estadoGeneral->nombre ?? 'Sin datos' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Motivo Baja</h4>
                            <p class="mt-1 text-gray-900">
                                {{ !empty($activo->motivo_baja) ? $activo->motivo_baja : 'Sin datos' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p>No se encontró el activo.</p>
        @endif
    </div>
@endsection
