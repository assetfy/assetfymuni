<div class="container mx-auto px-0">
    <!-- Encabezado -->
    <div id="header-welcome-unique" class="header-welcome">
        <!-- De acuerdo a la autenticacion del usuario, muestra el campo de foto de perfil-->
        <img id="profile-photo-unique" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
        <div>
            <h1 class="text-xl font-bold">Hola {{$empresa->razon_social}}</h1>
            <p>{{ auth()->user()->name }} - {{ auth()->user()->email }}</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row lg:h-full">
        <div class="lg:w-2/3 flex flex-col lg:h-full">
            @livewire('estado.tabla-estado')
        </div>

        <!-- Performance -->
        <div class="w-full lg:w-1/3 bg-white p-4 flex flex-col mt-4 lg:mt-0 lg:ml-4">
            <!-- Contenedores de los gráficos -->
            <div id="solicitudesChart" class="chart-container mb-8" style="height: 350px;"></div>
            <div id="serviciosPeticionesPorMesChart" class="chart-container mt-4" style="height: 350px;"></div>
        </div>
    </div>

    <style>
        .default-logo {
            width: 50px;
            height: 50px;
            background-color: #f0f0f0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            border-radius: 50%;
        }
    </style>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Asignando datos para gráfico de barras...');

        // Asignar datos específicos para el gráfico de barras
        window.solicitudesAprobadasData = @json($monthlyData['solicitudesAprobadasCount']);
        window.solicitudesPendientesData = @json($monthlyData['solicitudesPendientesCount']);
        window.serviciosCount = @json($monthlyData['serviciosCount']);
        window.peticionesCount = @json($monthlyData['peticionesCount']);
        window.currentMonth = @json($monthlyData['currentMonth']);

        console.log('Datos de barras asignados:', {
            solicitudesAprobadasData: window.solicitudesAprobadasData,
            solicitudesPendientesData: window.solicitudesPendientesData,
            serviciosCount: window.serviciosCount,
            peticionesCount: window.peticionesCount,
            currentMonth: window.currentMonth
        });

        // Destruye cualquier gráfico existente antes de inicializar uno nuevo
        if (window.chartSolicitudes || window.chartServiciosPeticionesPorMes) {
            destroyCharts();
        }

        initBarCharts();
    });
</script>
