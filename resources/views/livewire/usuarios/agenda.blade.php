<div class="container-fluid px-0 py-4">
    <div class="row gx-0">
        <!-- Columna para el calendario (a la izquierda con marco ajustado) -->
        <div class="col-md-4">
            <div class="p-4">
                <h3 class="text-center text-primary fw-bold mb-4">Próximas visitas pendientes</h3>
                <div id="calendar" data-events='{{ json_encode($activos) }}'
                    style="border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; background: #ffffff;">
                </div>
            </div>
        </div>

        <!-- Columna para la tabla de servicios realizados (a la derecha, sin marco) -->
        <div class="col-md-8">
            <div class="p-4">
                <h3 class="text-center text-secondary fw-bold mb-4">Servicios realizados a activos</h3>
                <div>
                    @livewire('servicios.solicitud-servicios')
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Estilo general */
        body {
            background-color: #ffffff;
            /* Fondo blanco */
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            /* Asegura que ocupe toda la altura de la pantalla */
        }

        .container-fluid {
            width: 100%;
        }

        .row {
            height: 100%;
        }

        .col-md-4,
        .col-md-8 {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        h3 {
            font-size: 1.5rem;
            margin: 0;
        }

        /* Calendario */
        #calendar {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra ligera */
            width: 100%;
            height: auto;
            /* Ajusta automáticamente la altura al contenido */
        }
    </style>
</div>
