**Documentación del Componente UbicacionesDashboard**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `UbicacionesDashboard` se utiliza para visualizar el tablero de control de ubicaciones de activos y servicios asociados. Este tablero permite ver la información de las ubicaciones, los tipos de ubicaciones, y un calendario con los servicios próximos para un activo específico. La interfaz es interactiva y permite a los usuarios navegar por las ubicaciones y sus detalles a través de Livewire y paginación.

## Dependencias
### Modelos
- **ActivosModel**: Gestiona la información de los activos.
- **SolicitudesServiciosModel**: Gestiona las solicitudes de servicios.
- **TiposUbicacionesModel**: Gestiona los tipos de ubicaciones disponibles.
- **UbicacionesModel**: Gestiona las ubicaciones de los activos.

### Servicios
- **IdHelper**: Utilizado para obtener identificadores relacionados con el usuario.

### Traits
- **WithPagination**: Permite la paginación de los resultados dentro del componente.

## Propiedades
- **id**: Identificador de la ubicación.
- **ubicaciones**: Almacena las ubicaciones disponibles.
- **tiposUbicacion**: Contiene los tipos de ubicaciones disponibles.
- **servicios**: Contiene los servicios próximos asociados a la ubicación.
- **serviceDates**: Array con las fechas de los servicios próximos.
- **currentMonth**: Mes actual en el contexto del componente, utilizado para el calendario.
- **currentYear**: Año actual en el contexto del componente.
- **search**: Campo para buscar activos por nombre.

## Métodos
### mount($id_ubicacion)
Este método se ejecuta cuando el componente se monta. Inicializa varias propiedades como el ID de la ubicación, tipos de ubicación, servicios asociados, y la fecha de los servicios. Además, configura el mes y año actual para el calendario.

### render()
Método responsable de renderizar la vista del componente. Obtiene los activos asociados a la ubicación actual e incluye la vista `livewire.ubicaciones.ubicaciones-dashboard`.

### updatingSearch()
Resetea la página de la paginación cada vez que se actualiza el campo de búsqueda.

### fetchTiposUbicaciones()
Obtiene todos los tipos de ubicaciones disponibles desde el modelo `TiposUbicacionesModel`.

### fetchUbicaciones()
Obtiene todas las ubicaciones disponibles desde el modelo `UbicacionesModel`.

### fetchActivosUbicaciones($id, $userId)
Obtiene los activos asociados a la ubicación específica y que pertenecen al usuario actual. Permite aplicar un filtro de búsqueda por el nombre del activo.

### fetchServiciosUbicaciones($userId)
Obtiene los servicios asociados a las ubicaciones del usuario, filtrados por estado. Si no encuentra servicios usando el `userId`, intenta buscar utilizando el ID del usuario autenticado.

### fetchServiceDates()
Obtiene las fechas de los servicios, ya sea la fecha modificada o la fecha de programación del servicio. Las devuelve en un formato de fecha específica.

### abrirModal()
Método que emite el evento para reinicializar el mapa cuando el modal es abierto.

### close()
Cierra el modal y resetea las propiedades del componente. También emite el evento `refreshLivewireTable` para actualizar los datos de la tabla.

## Vista del Componente
El componente incluye una vista en la que se dividen dos secciones principales:

1. **Componente de Tarjetas (Activos)**:
   - Representado por el componente `activos.dashboard-activos`.
   - Muestra los activos asociados a la ubicación actual.
   - El botón para crear activos y otros botones no están visibles, como se especifica en la propiedad `showCreateButton`.

2. **Calendario de Servicios**:
   - **Calendario**: Representado mediante `livewire:calendar`, muestra los servicios programados.
   - **Próximos Servicios**: Lista los próximos servicios con sus descripciones, fechas y horas.

El diseño de la vista se divide en columnas para permitir una interfaz responsiva tanto en pantallas pequeñas como grandes. La columna principal muestra los activos, mientras que la columna secundaria muestra un calendario de servicios.

