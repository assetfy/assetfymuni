<div>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 py-12">
        <div class="container mx-auto px-4 sm:px-8">
            <div class="py-8">
                <div>
                    <h2 class="text-2xl font-semibold leading-tight">Usuarios</h2>
                </div>
                <div class="flex items-center mb-6" style="padding-top: 20px">
                    <input type="text" placeholder="Buscar Usuario" class="px-4 py-2 border rounded-lg mr-4" style="width: 400px;" wire:model.live="search">
                    @livewire('usuarios.create-tipos-usuarios')
                </div>
                <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                    <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                        @if ($usuarios->count())
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" wire:click.live="order('nombre')"><i class="fas {{ $this->sortIcon('name') }} float-right mt-1"></i>
                                        Nombre
                                    </th>
                                    <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" wire:click.live="order('descripcion')">
                                        Email
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Tipo
                                    </th>
                                    <th class="cursor-pointer px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" wire:click="order('cuil')">
                                        CUIL
                                        <i class="fas {{ $this->sortIcon('cuil') }} float-right mt-1"></i>
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                <tr wire:key="{{ $usuario->name }}">
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ $usuario->name }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ $usuario->email }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu{{ $usuario->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ $usuario->tipo == 1 ? 'Administrador' : 'Comun' }}
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenu{{ $usuario->id }}">
                                                @if($usuario->tipo == 2)
                                                <li>
                                                    <a wire:click="updateTipo({{ $usuario->id }}, 1)" class="dropdown-item @if ($usuario->tipo == 1) active @endif" href="#">Administrador</a>
                                                </li>
                                                @else
                                                <li>
                                                    <a wire:click="updateTipo({{ $usuario->id }}, 2)" class="dropdown-item @if ($usuario->tipo == 2) active @endif" href="#">Comun</a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ $usuario->cuil }}
                                        </p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <span wire:click="toggleEstado({{ $usuario->id }})" class="cursor-pointer
                                                    @if ($usuario->estado == 1) text-blue-500 @else text-red-500 @endif">
                                            {{ $usuario->estado == 1 ? 'Activado' : 'Desactivado' }}
                                        </span>
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
                        @if ($usuarios->hasPages())
                        <div class="px-6 py-3">
                            {{ $usuarios->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>