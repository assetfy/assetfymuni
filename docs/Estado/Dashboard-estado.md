**Documentación del componente DashboardEstado**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `DashboardEstado` es un componente de Livewire que permite visualizar el estado actual de la empresa y los servicios relacionados con la misma. Este componente se utiliza principalmente para mostrar información relevante sobre las solicitudes de servicios, peticiones y servicios aceptados o pendientes. Además, incluye la generación de datos mensuales para proporcionar una visión clara de las actividades de la empresa durante el mes actual.

### Propiedades
- **$solicitudesAceptadas**: Almacena una colección de las solicitudes que han sido aceptadas.
- **$solicitudPendientes**: Almacena una colección de las solicitudes pendientes de aprobación.
- **$viewType**: Determina el tipo de vista que se mostrará. Puede ser 'all', 'pendientes', o 'aprobados'.
- **$search**: Cadena de búsqueda utilizada para filtrar los resultados de las solicitudes.
- **$servicios**: Contador que almacena la cantidad de servicios realizados durante el mes actual.
- **$peticiones**: Contador que almacena la cantidad de peticiones realizadas durante el mes actual.
- **$empresa**: Instancia del modelo de la empresa correspondiente al usuario.
- **$logo**: Almacena el logo de la empresa.
- **$monthlyData**: Datos calculados para el mes actual, incluyendo solicitudes pendientes, aceptadas, servicios, y peticiones.

### Métodos
- **mount()**: Inicializa las propiedades del componente al ser montado. Configura el tipo de vista, la búsqueda, y carga los datos de la empresa y su logo.

- **render()**: Renderiza la vista principal del componente. Además, llama al método `calculateMonthlyData()` para calcular y actualizar los datos mensuales antes de mostrar la vista.

- **cargarDatos()**: Carga los datos relacionados con la empresa del usuario autenticado, tales como las solicitudes de servicio, peticiones, y detalles de la empresa.

- **pollingRefresh()**: Actualiza los datos de la empresa mediante polling. Es útil para mantener la información en la interfaz siempre actualizada.

- **showAll()**: Cambia el tipo de vista a 'all' (todas las solicitudes) y vuelve a cargar los datos.

- **showPendientes()**: Cambia el tipo de vista a 'pendientes' y vuelve a cargar los datos.

- **showAprobados()**: Cambia el tipo de vista a 'aprobados' y vuelve a cargar los datos.

- **solicitudesReguladora($empresa)**: Método privado que carga las solicitudes relacionadas con la empresa. Filtra las solicitudes en función del tipo de vista (pendientes, aprobados o todas).

- **mostrarSolicitudes($empresa)**: Obtiene la cantidad de servicios realizados en el mes actual.

- **mostrarPeticiones($empresa)**: Obtiene la cantidad de peticiones realizadas en el mes actual.

- **logoEmpresa()**: Obtiene el logo de la empresa utilizando la información del modelo `EmpresasModel`.

- **calculateMonthlyData()**: Calcula los datos mensuales que se mostrarán en la interfaz, tales como el número de solicitudes pendientes, aprobadas, servicios y peticiones del mes actual.

### Vista del Componente
La vista asociada al componente `DashboardEstado` proporciona una interfaz visual que incluye:

- **Encabezado de Bienvenida**: Incluye la foto de perfil del usuario autenticado, el nombre de la empresa, y los datos del usuario.

- **Tabla de Estado**: Utiliza otro componente Livewire (`estado.tabla-estado`) para mostrar la tabla de solicitudes de estado.

- **Sección de Desempeño**: Contiene dos gráficos (“solicitudesChart” y “serviciosPeticionesPorMesChart”) que muestran los datos de las solicitudes aprobadas, pendientes, servicios, y peticiones para el mes actual.

- **Estilos y Scripts**: Incluye algunos estilos personalizados para la vista y scripts que inicializan los gráficos, asegurándose de que los datos mostrados estén siempre actualizados.

