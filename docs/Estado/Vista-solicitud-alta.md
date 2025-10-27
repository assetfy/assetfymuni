**Documentación del componente VistaSolicitudAlta**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `VistaSolicitudAlta` es un componente de Livewire utilizado para visualizar los detalles de una solicitud de habilitación de empresa. Este componente muestra información relevante sobre la empresa, incluyendo su nombre, actividad económica, descripción y ubicación en el mapa. Además, permite al usuario aprobar o rechazar la solicitud y muestra un resumen del rendimiento de los servicios realizados por la empresa durante el mes actual.

### Propiedades
- **$empresa**: Contiene la instancia del modelo de la empresa relacionada con la solicitud.
- **$lat, $long**: Coordenadas geográficas de la empresa para mostrar en el mapa.
- **$monthlyData**: Datos mensuales utilizados para la visualización de gráficos.
- **$serviciosRealizados**: Contador de los servicios realizados por la empresa durante el mes actual.
- **$serviciosPendienteVisita**: Contador de los servicios que están pendientes de una visita.
- **$serviciosPendientesCotizacion**: Contador de los servicios pendientes de aprobación de cotización.
- **$serviciosCotizadosyEsperando**: Contador de los servicios que han sido cotizados y están esperando confirmación.

### Listeners
- **autorizar**: Escucha el evento para autorizar la solicitud de la empresa.
- **noAutorizar**: Escucha el evento para denegar la autorización de la solicitud.

### Métodos
- **mount($solicitud)**: Inicializa las propiedades del componente al ser montado. Carga la información de la empresa y los servicios relacionados, y configura los datos para los gráficos.

- **calcularServicios($solicitud)**: Calcula la cantidad de servicios realizados, pendientes de visita, pendientes de cotización, y servicios cotizados durante el mes actual.

- **ubicacionDatos($empresa)**: Establece las coordenadas geográficas de la empresa para su visualización en el mapa.

- **aceptado()**: Despacha el evento para aprobar la solicitud de la empresa.

- **rechazado()**: Despacha el evento para rechazar la solicitud de la empresa.

- **autorizar()**: Autoriza la solicitud y actualiza el estado de la empresa según el panel del usuario actual (Estado o Empresa reguladora).

- **noAutorizar()**: Deniega la autorización de la solicitud y actualiza el estado de la empresa según el panel del usuario actual.

- **render()**: Renderiza la vista principal del componente, pasando los datos mensuales y las coordenadas para el mapa.

### Vista del Componente
La vista asociada al componente `VistaSolicitudAlta` incluye:

- **Encabezado**: Muestra el título "Solicitud de Habilitación".

- **Detalles de la Empresa**: Incluye el nombre de la empresa, la actividad económica, la descripción de la empresa y un enlace para ver la documentación en PDF.

- **Mapa de Ubicación**: Si la empresa tiene coordenadas, se muestra un mapa interactivo utilizando Leaflet.

- **Acciones**: Si la empresa no ha sido aceptada, se muestran botones para aprobar o rechazar la solicitud. Si la empresa ya ha sido aceptada, se muestra un enlace a la "Tabla de servicios".

- **Gráfico de Rendimiento**: Muestra un gráfico con el rendimiento de los servicios realizados y los servicios pendientes. Además, se incluyen botones para remover o resetear los datos del gráfico.

- **Scripts**: Incluye código JavaScript para inicializar el mapa con Leaflet y preparar los datos para los gráficos. También se verifica que los datos se hayan asignado correctamente para el gráfico Donut.