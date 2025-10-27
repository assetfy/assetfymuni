<x-action-section>
    <x-slot name="title">
        <h3 class="text-left text-black -ml-14 font-semibold">{{ __('Eliminar Empresa') }}</h3>
    </x-slot>

    <x-slot name="description">
        <p class="text-justify text-red-600 -ml-10">{{ __('Una vez que elimines tu cuenta de empresa, ésta se cerrará y deberás registrarte nuevamente cuando quieras usar nuestros servicios.') }}</p>
        <br>
        <p class="text-justify -ml-10">{{ __('Eliminar permanentemente tu cuenta de empresa.') }}</p>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600 text-justify">
            {{ __('Una vez que tu cuenta de empresa sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta de empresa, por favor descarga cualquier dato o información que desees conservar.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmEmpresaDeletion" wire:loading.attr="disabled">
                {{ __('Eliminar Empresa') }}
            </x-danger-button>
        </div>

        <!-- Delete Empresa Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingEmpresaDeletion" maxWidth=2xl>
            <x-slot name="title">
                <div class="text-center">
                    {{ __('Eliminar Empresa') }}
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col items-center justify-center text-center">
                    <p>{{ __('¿Estás seguro de que quieres eliminar tu cuenta de empresa?') }}<br>{{ __('Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar tu cuenta permanentemente.') }}</p>

                    <div class="mt-4 w-full flex justify-center" x-data="{ show: false }" x-on:confirming-delete-empresa.window="setTimeout(() => $refs.password.focus(), 250)">
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500
                                                                                            focus:bg-white focus:border-gray-600 focus:outline-none"
                                autocomplete="current-password" placeholder="{{ __('Contraseña') }}" x-ref="password" wire:model="password" wire:keydown.enter="deleteEmpresa" />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                <svg class="h-6 text-gray-700 cursor-pointer" @click="show = !show"
                                    :class="{ 'hidden': show, 'block': !show }" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                    <path fill="currentColor" d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path>
                                </svg>
                                <svg class="h-6 text-gray-700 cursor-pointer" @click="show = !show"
                                    :class="{ 'block': show, 'hidden': !show }" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <x-input-error for="password" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="w-full flex justify-center">
                    <x-secondary-button wire:click="$toggle('confirmingEmpresaDeletion')" wire:loading.attr="disabled">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3 ml-3" wire:click="deleteEmpresa" wire:loading.attr="disabled">
                        {{ __('Eliminar Empresa') }}
                    </x-danger-button>
                </div>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>