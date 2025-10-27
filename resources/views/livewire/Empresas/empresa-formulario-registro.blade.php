<div class="min-h-screen flex items-center justify-center bg-gray-100">
    @if ($step === 1)
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-4xl">
            <h2 class="text-center text-2xl font-bold text-blue-900 mb-8">¿Eres una persona que desea registrar un activo o eres un proveedor de servicios?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($tiposEmpresas as $tipoEmpresa)
                    <div class="relative flex flex-col items-center p-6 bg-blue-800 rounded-lg shadow-lg cursor-pointer border-2 {{ $selectedTipoEmpresa === $tipoEmpresa->id ? 'border-blue-400' : 'border-transparent' }}"
                         wire:click="selectTipoEmpresa({{ $tipoEmpresa->id }})">
                        <img src="{{ asset('images/'.$tipoEmpresa->imagen) }}" alt="{{ $tipoEmpresa->nombre }}" class="w-32 h-32 mb-4">
                        <h3 class="text-lg font-semibold text-white">{{ $tipoEmpresa->nombre }}</h3>
                        @if ($selectedTipoEmpresa === $tipoEmpresa->id)
                            <div class="absolute top-0 right-0 mt-2 mr-2 bg-white text-blue-600 rounded-full p-1">
                                <i class="fas fa-check"></i>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="flex justify-center mt-8">
                <button class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50" {{ $selectedTipoEmpresa ? '' : 'disabled' }} wire:click="$set('step', 2)">Continuar</button>
            </div>
        </div>
    @elseif ($step === 2)
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-4xl">
            <h2 class="text-center text-3xl font-bold text-blue-900 mb-8">Vamos a dar de alta los datos de tu compañía...</h2>
            <form wire:submit.prevent="save" class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
                <!-- Campos del formulario -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de la compañía</label>
                        <input type="text" wire:model.defer="razon_social" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CUIL</label>
                        <input type="text" wire:model.defer="cuit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Domicilio Fiscal</label>
                        <input type="text" wire:model.defer="domicilio" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Código Postal</label>
                        <input type="text" wire:model.defer="codigo_postal" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Localidad</label>
                        <input type="text" wire:model.defer="localidad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provincia</label>
                        <input type="text" value="Corrientes" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">País</label>
                        <input type="text" value="Argentina" disabled class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Tipo de Servicio</label>
                        <input type="text" wire:model.defer="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    @if ($selectedTipoEmpresa == 1)
                        <div class="col-span-2 mb-4">
                            <label class="block text-sm font-medium text-gray-700">Buscar Actividad</label>
                            <input type="text" wire:model.live="cod" wire:keydown="BuscarActividad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        @if ($actividad !== null)
                        <div class="mb-4">
                            <label for="selectActividad" class="block text-sm font-medium text-gray-700">Seleccionar Actividad:</label>
                            <select id="selectActividad" wire:model="selectedActividad" wire:loading.attr="disabled" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="">Seleccione una actividad</option>
                                @foreach ($actividad as $act)
                                <option value="{{ $act->COD_ACTIVIDAD }}">{{ $act->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($actividad !== null && $actividad->isEmpty())
                        <div class="alert alert-danger" wire:loading.remove>
                            Actividad no encontrada
                        </div>
                        @endif
                        @endif
                    @endif
                </div>
                <div class="mb-4">
                    <label for="constancia_afip" class="block text-sm font-medium text-gray-700">Adjuntar Documentos:</label>
                    <input type="file" wire:model="constancia_afip" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @error('constancia_afip') <span class="error text-red-500">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="logo" class="block text-sm font-medium text-gray-700">Logo:</label>
                    <input type="file" wire:model="logo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div class="flex justify-between items-center mt-8">
                    <button type="button" wire:click="goBack" class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">Atrás</button>
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Guardar</button>
                </div>
            </form>
        </div>
    @elseif ($step === 3)
        <!-- Aquí puedes agregar el contenido del paso 3 -->
    @endif
</div>
