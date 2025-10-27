<div>
    <div id="calendar" class="calendar-container" data-events='@json($events)'></div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.15/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.15/main.min.css" rel="stylesheet">
    <style>
        /* Contenedor del calendario */
        .calendar-container {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            height: 500px;
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            font-size: 14px;
            overflow: hidden;
        }

        /* Barra de herramientas del calendario */
        .fc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e293b; /* Azul oscuro */
        }

        .fc-button {
            background-color: #1d4ed8; /* Azul fuerte */
            border: none;
            color: #ffffff;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .fc-button:hover {
            background-color: #2563eb; /* Azul más brillante */
        }

        /* Días del calendario */
        .fc-daygrid-day-top {
            font-size: 0.85rem;
            font-weight: bold;
            color: #4b5563; /* Gris oscuro */
            padding: 0.5rem;
        }

        /* Eventos */
        .fc-daygrid-event {
            background-color: #3b82f6 !important; /* Azul claro */
            color: #ffffff !important;
            border-radius: 0.375rem;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
        }

        .fc-event:hover {
            background-color: #1d4ed8 !important; /* Azul más oscuro al pasar el cursor */
            color: #ffffff !important;
        }

        /* Botones de navegación */
        .fc-prev-button,
        .fc-next-button {
            font-size: 1rem;
            padding: 0.4rem 0.8rem;
        }

        /* Vista de día, semana y mes */
        .fc-view-harness {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /* Sombra en la vista de calendario */
        .fc-view {
            box-shadow: inset 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        /* Scroll en dispositivos pequeños */
        .fc-scroller {
            overflow-y: auto !important;
        }

        /* Fondo de los días sin eventos */
        .fc-daygrid-day {
            background-color: #f9fafb; /* Fondo gris claro */
        }

        /* Días actuales resaltados */
        .fc-day-today {
            background-color: #eef2ff !important; /* Fondo azul muy claro */
            border-left: 4px solid #1d4ed8 !important; /* Resaltado azul */
        }
    </style>
    @endpush

    @push('scripts')
    @vite(['resources/js/calendar.js'])
    @endpush
</div>
