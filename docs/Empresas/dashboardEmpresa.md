**Documentación del componente Dashboard de Empresa**

### Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Filtros](#filtros)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `DashboardEmpresa` es un componente Livewire que permite gestionar la visualización y administración de datos importantes relacionados con una empresa, como las solicitudes de servicio, ubicaciones, activos y usuarios. Este componente proporciona varias pestañas para filtrar información y simplificar la gestión de las actividades de la empresa.

### Propiedades

- **$tab**: Determina la pestaña activa actualmente en el componente. Puede tomar valores como `todos`, `cotizaciones` y `finalizacion`.
- **$empresa**: Almacena la información de la empresa actual.
- **$id_usuario**: Identificador del usuario asociado a la empresa.
- **$ubicaciones**: Ubicaciones asociadas a la empresa.
- **$activos**: Activos registrados por la empresa.
- **$logo**: URL del logo de la empresa.
- **$users**: Lista de usuarios asociados a la empresa.
- **$search**: Filtro de búsqueda para filtrar solicitudes.

### Listeners

- **setTab($tab)**: Método que cambia la pestaña activa y resetea la paginación.
- **updatedSearch()**: Listener que se activa cuando cambia el valor de `search`, reiniciando la paginación para reflejar resultados actualizados.

### Filtros

- **$search**: Utilizado para filtrar las solicitudes mostradas en las pestañas activas, permitiendo buscar activos específicos.

### Métodos

- **mount()**: Se ejecuta al cargar el componente, inicializando las propiedades con datos relevantes de la empresa y sus entidades asociadas.
- **render()**: Renderiza la vista del componente, incluyendo solicitudes por cotizar y de visita según la pestaña seleccionada.
- **getSolicitudesPorCotizar()**: Retorna una colección de solicitudes que esperan cotización.
- **getSolicitudesDeVisita()**: Retorna una colección de solicitudes que requieren una visita.
- **getActivos()**: Obtiene todos los activos de la empresa.
- **getUbicaciones($empresa)**: Retorna todas las ubicaciones de la empresa.
- **getUserId($empresa)**: Obtiene el ID del usuario autenticado si pertenece a la empresa.
- **getUsers()**: Retorna todos los usuarios registrados.
- **getEmpresas()**: Obtiene la empresa basada en el `cuit` de la sesión actual.
- **logoEmpresa()**: Retorna el logo de la empresa actual.
- **updateCotizar($servicioId)**: Abre un modal para editar el estado de cotización de un servicio.
- **redirectToServicios($servicioId)**: Redirige a la vista de servicios a realizar para un servicio dado.

### Vista del Componente

La vista del componente se compone de un contenedor principal donde se incluyen las secciones siguientes:

1. **Header de Bienvenida**: Muestra el nombre y la foto de perfil del usuario asociado a la empresa.
2. **Barra de Búsqueda y Pestañas**: Permite al usuario buscar servicios específicos e interactuar con diferentes pestañas para ver todas las solicitudes, solicitudes por cotizar, o aquellas listas para finalizar.
3. **Contenido según la Pestaña Activa**: Según la pestaña seleccionada, muestra la información correspondiente:
   - **Pestaña "Todos"**: Muestra tanto solicitudes por cotizar como solicitudes de visita.
   - **Pestaña "Cotizaciones"**: Muestra las solicitudes que esperan cotización.
   - **Pestaña "Finalización"**: Muestra las solicitudes listas para una visita final.

Cada solicitud es presentada en una tarjeta con la siguiente estructura:

- **Imagen del Activo**: Si el activo tiene una foto asociada, esta se muestra en la parte superior de la tarjeta. Si no, se muestra una imagen por defecto.
- **Detalles del Activo**: Incluyen el nombre del activo, su ubicación y tipo.
- **Botones de Acción**: Dependiendo del estado de la solicitud, se presenta un botón para cotizar o para visitar el activo.

