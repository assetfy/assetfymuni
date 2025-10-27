**Documentación del Componente CrearSolicitudServicio**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearSolicitudServicio` se encarga de gestionar la creación de solicitudes de servicio para un activo específico dentro del sistema. Permite seleccionar un servicio, una empresa prestadora, establecer una descripción y seleccionar una fecha y hora para realizar el servicio. Además, incluye validaciones para asegurar la consistencia de los datos antes de registrar la solicitud.

## Dependencias
- `ServiciosActividadesEconomicasModel`: Para gestionar las actividades económicas relacionadas con los servicios.
- `ServiciosSubcategoriasModel`: Para obtener las subcategorías relacionadas con los servicios.
- `SolicitudesServiciosModel`: Para crear el registro de la solicitud de servicio.
- `EmpresasActividadesModel`: Para gestionar las actividades relacionadas con las empresas.
- `ServiciosModel`: Para obtener los servicios disponibles.
- `EmpresasModel`: Para obtener información de las empresas.
- `ActivosModel`: Para gestionar los activos relacionados con las solicitudes de servicio.
- `Session`: Para manejar la sesión del usuario.
- `Carbon`: Para trabajar con fechas y horas.

## Propiedades
- `$servicios`: Colección de servicios disponibles para el activo seleccionado.
- `$prestadoras`: Empresas prestadoras que pueden ofrecer el servicio seleccionado.
- `$subcategoria`: Subcategoría del activo.
- `$id_servicio`: Identificador del servicio seleccionado.
- `$id_activo`: Identificador del activo para el cual se solicita el servicio.
- `$prestadora`: CUIT de la empresa prestadora seleccionada.
- `$empresa_solicitante`: CUIT de la empresa solicitante del servicio.
- `$fechaHora`: Fecha y hora seleccionada para la prestación del servicio.
- `$descripcion`: Descripción de la solicitud de servicio.
- `$user`: Usuario que realiza la solicitud.
- `$empresaActividad`: Actividades relacionadas con la empresa.
- `$activo`: Activo relacionado con la solicitud de servicio.
- `$actividades`: Actividades económicas relacionadas con los servicios disponibles.
- `$open`: Bandera booleana que indica si el modal para crear una solicitud está abierto o cerrado.

## Métodos

### `mount(ActivosModel $value)`
Inicializa el componente y establece el identificador del activo.

### `openModal($data)`
Abre el modal de creación de solicitud de servicio. Recibe el identificador del activo y lo utiliza para cargar los datos necesarios.

### `cargarDatos()`
Carga los datos relacionados con el usuario y el activo. Inicializa los servicios disponibles para el activo.

### `datosServicios()`
Obtiene los servicios disponibles para el activo basándose en la subcategoría, categoría y tipo del activo.

### `cargarPrestadoras($id_servicio)`
Carga las empresas prestadoras que ofrecen el servicio seleccionado.

### `empresasPrestadora($cuit)`
Obtiene las empresas prestadoras que corresponden al servicio seleccionado.

### `updated($propertyName)`
Valida la propiedad que se ha actualizado para asegurar que cumpla con las reglas establecidas.

### `save()`
Valida los datos y guarda la solicitud de servicio. Incluye una validación adicional para asegurarse de que la fecha y hora no sean anteriores a la actual.

### `validarFechaHora()`
Valida que la fecha y hora seleccionada para el servicio no sean anteriores a la fecha y hora actuales.

### `panel()`
Determina si el usuario actual pertenece al panel de la empresa y establece la empresa solicitante.

### `crearRegistro($descripcion, $fechaHoraFormateada)`
Crea un registro en la tabla `SolicitudesServiciosModel` con los datos proporcionados.

### `close()`
Cierra el modal y reinicia las propiedades del componente.

### `render()`
Devuelve la vista asociada al componente (`livewire.servicios.activos.crear-solicitud-servicio`).

## Vista del Componente
La vista del componente `crear-solicitud-servicio` contiene un modal que permite al usuario crear una nueva solicitud de servicio para un activo. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal enlazado a la propiedad `$open` para abrir o cerrar según su valor.
  
  - **Título del Modal**: Muestra el texto "Servicios" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Selección de Servicios**: Permite al usuario seleccionar un servicio de la lista de servicios disponibles. Si no hay servicios disponibles para el activo, se muestra un mensaje indicando tal situación.
    - **Selección de Prestadoras**: Permite seleccionar una empresa prestadora si hay disponibles. También se puede ingresar una descripción y la fecha y hora del servicio.

  - **Footer del Modal**:
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios.
    - **Botón "Solicitar Servicio" (`x-danger-button`)**: Ejecuta la acción de guardar la solicitud de servicio si se cumplen las validaciones correspondientes.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar la solicitud.

