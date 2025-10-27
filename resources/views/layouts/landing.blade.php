<header>
    <x-app-layout>
    <x-slot name="header">
        <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @yield('section')
        </h2> -->
    </x-slot>

    <div class="py-1">
        <div class="max-w-8x1 sm:px-6 lg:px-9">
            <div>
                @yield('content')
            </div>
        </div>
    </div>
</x-app-layout>
</header>