**Documentación del Componente ServiciosVistaFormulario**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **ServiciosVistaFormulario** permite a los usuarios solicitar un servicio a través de un formulario interactivo. El usuario puede seleccionar un activo, elegir un servicio, seleccionar prestadoras disponibles, y proporcionar una descripción del trabajo requerido, así como una fecha y hora para el servicio.

## Dependencias
### Namespaces y Paquetes Utilizados
- **App\Models\ActivosModel**: Representa los activos gestionados por los usuarios.
- **App\Models\ServiciosActividadesEconomicasModel**: Maneja las actividades económicas vinculadas a servicios.
- **App\Models\ServiciosSubcategoriasModel**: Relaciona subcategorías con servicios.
- **App\Models\SolicitudesServiciosModel**: Gestiona las solicitudes de servicios hechas por los usuarios.
- **App\Models\EmpresasActividadesModel** y **App\Models\EmpresasModel**: Gestionan información sobre las empresas.
- **Illuminate\Support\Facades\Session** y **Illuminate\Support\Facades\Auth**: Gestionan la sesión y autenticación del usuario.
- **Carbon\Carbon**: Maneja fechas y horarios.

## Propiedades
- **$user**: Almacena la información del usuario autenticado.
- **$empresa_solicitante**: Representa la empresa que está solicitando el servicio.
- **$activos**, **$filteredActivos**: Colección de activos disponibles y los activos filtrados por búsqueda.
- **$subcategoria**, **$servicios**, **$filteredServicios**: Subcategorías y servicios disponibles, así como sus versiones filtradas.
- **$prestadoras**: Prestadoras disponibles para el servicio seleccionado.
- **$fechaHora**, **$descripcion**, **$cod**, **$errorMessage**: Propiedades relacionadas con la solicitud del servicio.

## Métodos

### `mount()`
Inicializa el componente cargando los datos del usuario, sus activos, y configurando los valores predeterminados de las colecciones filtradas.

### `datos()` y `panel()`
- **datos()**: Carga la información necesaria para la vista, incluyendo los activos del usuario.
- **panel()**: Determina si el usuario está solicitando el servicio en nombre de una empresa o de manera individual.

### `getUserId()`
Obtiene el ID del usuario autenticado o el CUIT de la empresa seleccionada.

### `fetchActivos($id)`
Devuelve los activos del usuario basándose en el ID proporcionado.

### `updatedSearchActivo()`
Actualiza los activos filtrados según el valor ingresado en la barra de búsqueda y resetea las selecciones si no coinciden con los activos encontrados.

### `setIdActivo($id)`
Asigna un activo seleccionado y carga los servicios relacionados con ese activo.

### `cargarDatosServicios()`
Carga los servicios relacionados con el activo seleccionado, basándose en el tipo, categoría y subcategoría del activo.

### `updatedSearchServicio()`
Actualiza los servicios filtrados según la búsqueda y resetea las selecciones si no hay coincidencias.

### `setIdServicio($id)`
Selecciona un servicio y llama al método **prestadora()** para cargar las prestadoras disponibles.

### `prestadora()`
Carga las empresas prestadoras disponibles para el servicio seleccionado.

### `updatedSearchPrestadora()`
Actualiza las prestadoras filtradas según la búsqueda del usuario.

### `removePrestadora($cuit)`
Elimina una prestadora seleccionada de la lista de prestadoras vinculadas a la solicitud.

### `getPrestadorasSeleccionadasProperty()`
Devuelve las prestadoras seleccionadas por el usuario.

### `render()`
Renderiza la vista del componente.

### `save()`
Guarda la solicitud del servicio si la fecha es válida, ejecuta la validación de los campos, y realiza la transacción para crear la solicitud en la base de datos.

### `validacion()`
Valida los campos del formulario antes de guardar la solicitud.

### `fechaEsValida()`
Verifica que la fecha y hora seleccionadas sean posteriores a la fecha y hora actual.

### `crearRegistro()`
Crea un registro de solicitud de servicio en la base de datos.

### `formatoFecha($fecha)`
Formatea la fecha y hora ingresadas para almacenarlas en la base de datos.

### `close()`
Restablece todas las propiedades del formulario y cierra el modal.

## Vista del Componente
El componente incluye un formulario interactivo con las siguientes secciones:

- **Selección de Activos**: Permite seleccionar un activo de los que posee el usuario.
- **Selección de Servicios**: Muestra los servicios disponibles para el activo seleccionado.
- **Fecha y Hora**: Campo para seleccionar la fecha y hora en la que se requiere el servicio.
- **Selección de Prestadoras**: Permite buscar y seleccionar empresas prestadoras para realizar el servicio.
- **Descripción de la Tarea**: Campo de texto para describir la tarea solicitada.

El formulario permite al usuario enviar una solicitud de servicio una vez se han completado todos los campos requeridos. También cuenta con un indicador de carga que se muestra mientras se guarda la solicitud.

