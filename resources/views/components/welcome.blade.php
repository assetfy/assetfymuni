<!-- Estilos personalizados -->
<style>
    .title-personalizado {
        font-size: 4rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 2rem;
        color: black;
    }
    .custom-container .card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 18rem;
        background-color: #f3f4f6;
        transition: transform 0.7s ease-in-out;
    }
    .custom-container .card:hover {
        cursor: pointer;
        background-color: #bfdbfe;
        transform: scale(1.05);
    }
    .progress-bar-container {
        width: 100%;
        background-color: #e5e7eb;
        border-radius: 0.375rem;
        height: 2rem;
        margin-bottom: 1.25rem;
    }
    .progress-bar {
        height: 2rem;
        border-radius: 0.375rem;
    }
    .badge {
        position: absolute;
        top: 0;
        right: 0;
        padding: 0.5rem;
        border-radius: 9999px;
    }
    .animate-bounce {
        animation: bounce 2s infinite;
    }
    .animate-swing {
        animation: swing 2s infinite;
    }
    .animate-tada {
        animation: tada 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-30px);
        }
        60% {
            transform: translateY(-15px);
        }
    }

    @keyframes swing {
        20% {
            transform: rotate(15deg);
        }
        40% {
            transform: rotate(-10deg);
        }
        60% {
            transform: rotate(5deg);
        }
        80% {
            transform: rotate(-5deg);
        }
        100% {
            transform: rotate(0deg);
        }
    }

    @keyframes tada {
        0% {
            transform: scale(1);
        }
        10%, 20% {
            transform: scale(0.9) rotate(-3deg);
        }
        30%, 50%, 70%, 90% {
            transform: scale(1.1) rotate(3deg);
        }
        40%, 60%, 80% {
            transform: scale(1.1) rotate(-3deg);
        }
        100% {
            transform: scale(1) rotate(0);
        }
    }
</style>

@php
    $steps = [
        [
            'condition' => $activosCount == 0 && $ubicacionesCount == 0 && $serviciosCount == 0,
            'progress' => '1%',
            'cards' => [
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-location-dot',
                    'iconColor' => 'red-500',
                    'animation' => 'animate-bounce',
                    'title' => 'Crea una ubicación',
                    'text' => 'Puedes generar cuantas ubicaciones necesites, y especificar a tu gusto!'
                ],
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-parachute-box',
                    'iconColor' => 'yellow-500',
                    'animation' => 'animate-swing',
                    'title' => 'Crea tu primer activo',
                    'text' => 'Recuerda que cuando generes una ubicación, se generará un activo de la misma por defecto.'
                ],
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-flag-checkered',
                    'iconColor' => 'green-500',
                    'animation' => 'animate-tada',
                    'title' => '¡Comienza a administrar!',
                    'text' => 'Ya estás listo para buscar soluciones, servicios, etc para tu activo!'
                ],
            ],
        ],
        [
            'condition' => $activosCount == 0 && $ubicacionesCount > 0 && $serviciosCount == 0,
            'progress' => '33%',
            'cards' => [
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-location-dot',
                    'iconColor' => 'red-500',
                    'animation' => 'animate-bounce',
                    'title' => 'Crea una ubicación',
                    'text' => 'Puedes generar cuantas ubicaciones necesites, y especificar a tu gusto!'
                ],
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-parachute-box',
                    'iconColor' => 'yellow-500',
                    'animation' => 'animate-swing',
                    'title' => 'Crea tu primer activo',
                    'text' => 'Recuerda que cuando generes una ubicación, se generará un activo de la misma por defecto.'
                ],
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-flag-checkered',
                    'iconColor' => 'green-500',
                    'animation' => 'animate-tada',
                    'title' => '¡Comienza a administrar!',
                    'text' => 'Ya estás listo para buscar soluciones, servicios, etc para tu activo!'
                ],
            ],
        ],
        [
            'condition' => $activosCount > 1 && $ubicacionesCount > 0 && $serviciosCount == 0,
            'progress' => '66%',
            'cards' => [
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-location-dot',
                    'iconColor' => 'red-500',
                    'animation' => 'animate-bounce',
                    'title' => 'Crea una ubicación',
                    'text' => 'Puedes generar cuantas ubicaciones necesites, y especificar a tu gusto!'
                ],
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-parachute-box',
                    'iconColor' => 'yellow-500',
                    'animation' => 'animate-swing',
                    'title' => 'Crea tu primer activo',
                    'text' => 'Recuerda que cuando generes una ubicación, se generará un activo de la misma por defecto.'
                ],
                [
                    'badgeColor' => 'yellow',
                    'badgeIcon' => 'fa-solid fa-clock',
                    'icon' => 'fa-solid fa-flag-checkered',
                    'iconColor' => 'green-500',
                    'animation' => 'animate-tada',
                    'title' => '¡Comienza a administrar!',
                    'text' => 'Ya estás listo para buscar soluciones, servicios, etc para tu activo!'
                ],
            ],
        ],
        [
            'condition' => $activosCount > 1 && $ubicacionesCount > 1 && $serviciosCount > 0,
            'progress' => '100%',
            'cards' => [
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-location-dot',
                    'iconColor' => 'red-500',
                    'animation' => 'animate-bounce',
                    'title' => 'Crea una ubicación',
                    'text' => 'Puedes generar cuantas ubicaciones necesites, y especificar a tu gusto!'
                ],
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-parachute-box',
                    'iconColor' => 'yellow-500',
                    'animation' => 'animate-swing',
                    'title' => 'Crea tu primer activo',
                    'text' => 'Recuerda que cuando generes una ubicación, se generará un activo de la misma por defecto.'
                ],
                [
                    'badgeColor' => 'green',
                    'badgeIcon' => 'fa-regular fa-circle-check',
                    'icon' => 'fa-solid fa-flag-checkered',
                    'iconColor' => 'green-500',
                    'animation' => 'animate-tada',
                    'title' => '¡Comienza a administrar!',
                    'text' => 'Ya estás listo para buscar soluciones, servicios, etc para tu activo!'
                ],
            ],
        ],
    ];
@endphp

@foreach ($steps as $step)
    @if ($step['condition'])
        <div class="container mx-auto text-center rounded-lg mt-8 mb-8 bg-white">
            <h1 class="title-personalizado">Primeros Pasos</h1>
            <div class="custom-container mb-5">
                <div class="progress-bar-container">
                <div class="progress-bar" style="width: {{ $step['progress'] }}; background-color: #73A2C7"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($step['cards'] as $card)
                        <div class="card border text-center mt-4 border-{{ $card['badgeColor'] }}-300 shadow-lg">
                            <span class="badge bg-{{ $card['badgeColor'] }}-400 text-black">
                                <i class="{{ $card['badgeIcon'] }} fa-lg"></i>
                            </span>
                            <div class="card-body">
                                <i class="{{ $card['icon'] }} fa-5x mb-3 text-{{ $card['iconColor'] }} {{ $card['animation'] }}"></i>
                                <h5 class="card-title text-lg font-bold">{{ $card['title'] }}</h5>
                                <p class="card-text">{{ $card['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endforeach
