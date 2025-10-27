# Documentación del Componente Livewire: DashboardActivos

El componente `DashboardActivos` permite la visualización y gestión de activos en un panel de control. Utiliza **Laravel Livewire 3.0** junto con paginación y funcionalidades de búsqueda para proporcionar una experiencia de usuario dinámica e interactiva.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `DashboardActivos` facilita la consulta, búsqueda y administración de activos existentes en la aplicación. Los usuarios pueden visualizar los activos mediante filtros de tipo y ubicación, generar códigos QR para activos específicos, y acceder a opciones para agregar controles, atributos o cambiar la ubicación de un activo.

## Propiedades
- **search**: Cadena de texto utilizada para buscar activos por su nombre.
- **tipos**: Lista de tipos de activos disponible para filtrar.
- **open**: Controla la apertura de modales para ciertas acciones.
- **idTipoSeleccionado, activoBusqueda, id_ubicacion, id_tipo**: Utilizados para almacenar y filtrar la selección del tipo de activo y la ubicación.
- **hideCreateButton, hideFilters, showQrButton, showCreateButton, showAllButtons**: Configuraciones para controlar la visibilidad de botones y filtros en el panel.

## Métodos
### mount()
Inicializa las propiedades del componente con los valores proporcionados, como el tipo de activo o la ubicación. Carga los tipos de activos disponibles y ejecuta la búsqueda inicial de activos.

### updatingSearch()
Resetea la paginación al actualizar el campo de búsqueda, garantizando que los resultados mostrados sean correctos.

### busqueda($id_tipo, $id_ubicacion)
Realiza una consulta de activos basada en el tipo y la ubicación seleccionados, permitiendo una búsqueda más específica.

### render()
Renderiza la vista del componente y devuelve los activos filtrados y otras configuraciones que controlan la visibilidad de los elementos del panel.

### coleccionActivos()
Obtiene la lista de activos según los criterios de búsqueda y filtros aplicados. Esta consulta incluye la relación con el tipo de activo y la ubicación.

### getTipos()
Obtiene todos los tipos de activos disponibles en la base de datos.

### openCreateControlModal($activo), openCreateAtributoModal($activo), openServiciosActivosModal($activo), openModalCambiarUbicacion($activo)
Abren modales específicos para crear un nuevo control de activos, agregar atributos, crear una solicitud de servicio o cambiar la ubicación de un activo. Utilizan `dispatch` para la apertura de estos modales.

### download()
Genera un PDF con los códigos QR de los activos que pertenecen al usuario o la empresa seleccionada. Utiliza **Dompdf** para generar el documento y permite descargarlo directamente.

### getUserId()
Obtiene el ID del usuario o empresa para filtrar los activos correspondientes. Este ID se obtiene de la sesión o de la información del usuario autenticado.

## Eventos Livewire
- **render**: Emitido después de la carga inicial o actualización de la búsqueda para refrescar la vista.
- **openModal**: Utilizado para abrir modales con acciones específicas en relación con los activos.
- **openModalCambiarUbicacion**: Específico para abrir el modal de cambio de ubicación de un activo.

## Vista y Controlador

### Interacción entre Vista y Controlador
El componente `DashboardActivos` interactúa constantemente con la vista mediante eventos y el uso de propiedades enlazadas (`wire:model`). Esto permite una sincronización continua entre los elementos visibles en la vista y los datos almacenados en el componente, ofreciendo una experiencia de usuario sin interrupciones.

### Funcionalidades de Búsqueda y Filtro
Los filtros de búsqueda permiten a los usuarios restringir los resultados mostrados según el tipo de activo o la ubicación. La barra de búsqueda es dinámica y los resultados se actualizan automáticamente conforme el usuario escribe, gracias al método `updatingSearch()`.

### Paginación
El componente utiliza la característica **WithPagination** de Livewire, lo cual permite dividir los activos en varias páginas y navegar fácilmente entre ellos, mejorando la experiencia de visualización de grandes volúmenes de datos.

### Generación de Códigos QR
El método `download()` permite generar un archivo PDF con los códigos QR de los activos, utilizando la librería **Dompdf**. Cada activo tiene un código QR que redirige a una URL específica para ver más detalles del activo.

### Estructura de la Vista
La vista muestra un encabezado principal titulado "Colección de Activos" seguido de una barra de búsqueda y, opcionalmente, un botón para crear nuevos activos. Si hay activos disponibles, estos se presentan en una cuadrícula que varía su disposición según el tamaño de la pantalla (adaptativa). Cada activo se representa como una tarjeta que tiene una parte delantera, que muestra información básica y el código QR del activo, y una parte trasera, donde el usuario puede acceder a diferentes acciones como solicitar servicios, cargar controles, ver detalles de atributos, movimientos, y más.

El comportamiento de la tarjeta incluye una animación de volteo que permite al usuario alternar entre la vista delantera y la vista trasera al hacer clic. Además, se incluyen estilos y media queries para garantizar que el componente sea completamente adaptativo y mantenga un buen diseño en diferentes tamaños de pantalla.

## Dependencias
El componente `DashboardActivos` depende de varios modelos y clases auxiliares:
- **Illuminate\Support\Facades\Auth**: Utilizado para acceder a la información del usuario autenticado.
- **App\Traits\SortableTrait**: Proporciona funcionalidades para ordenar los activos según diferentes criterios.
- **Livewire\WithPagination**: Utilizado para implementar la paginación de los resultados de búsqueda.
- **App\Models\ActivosModel**: Utilizado para realizar consultas relacionadas con los activos.
- **App\Helpers\IdHelper**: Utilizado para obtener el identificador de la empresa o usuario correspondiente.
- **App\Models\TiposModel**: Utilizado para obtener los tipos de activos disponibles.
- **Livewire\Component**: Base para crear componentes en Livewire.
- **Dompdf\Dompdf, Dompdf\Options**: Utilizados para generar PDFs con los códigos QR de los activos.

