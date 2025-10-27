**Documentación del Componente DashboardUsuario**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `DashboardUsuario` es un componente de Livewire que se encarga de mostrar un panel de control personalizado para los usuarios de la aplicación. Este dashboard incluye información relevante del usuario, como activos, ubicaciones, servicios solicitados, tipos de propiedades, notificaciones y empresas asociadas. Además, se integra un calendario y un carrusel de ubicaciones para una navegación más intuitiva.

## Dependencias
Este componente depende de varios modelos y clases que se utilizan para obtener y procesar los datos del usuario. A continuación se listan las dependencias:

- **Modelos**:
  - `App\Models\SolicitudesServiciosModel`: Modelo utilizado para gestionar las solicitudes de servicios realizadas por el usuario o relacionadas con sus activos.
  - `App\Models\ServiciosActivosModel`: Modelo que relaciona los servicios con los activos correspondientes.
  - `App\Models\UsuariosEmpresasModel`: Modelo que gestiona la relación entre los usuarios y las empresas, incluyendo estados como "Aceptado" o "Rechazado".
  - `App\Models\UbicacionesModel`: Modelo para gestionar las ubicaciones asociadas al usuario, incluyendo detalles como dirección, tipo de propiedad y cantidad de activos.
  - `App\Models\ActivosModel`: Modelo que almacena la información de los activos del usuario.
  - `App\Models\TiposModel`: Modelo utilizado para definir los diferentes tipos de activos disponibles en la aplicación.
  - `App\Models\ActividadesEconomicasModel`: Modelo que contiene las actividades económicas asociadas a las empresas.
  - `App\Models\EmpresasModel`: Modelo para gestionar la información de las empresas, incluyendo la relación con los usuarios y servicios.
  - `App\Models\User`: Modelo estándar de usuario para gestionar la información del usuario autenticado.

- **Helpers**:
  - `App\Helpers\IdHelper`: Helper utilizado para obtener identificadores del usuario y la empresa, que se emplean en las consultas para filtrar datos específicos del usuario.

- **Componentes de Livewire**: Se integra con otros componentes de Livewire, como el calendario (`livewire:calendar`). Esto permite mostrar un calendario interactivo de servicios directamente en el dashboard del usuario.

## Propiedades
- **datosDashboard**: Almacena todos los datos del dashboard, incluyendo activos, servicios, ubicaciones, etc.
- **open**: Define si un modal está abierto o no.
- **servicioId**: Identificador del servicio seleccionado.
- **user**: Instancia del usuario autenticado.

## Métodos
### mount()
Inicializa el componente cargando los datos necesarios para el dashboard.

### refreshDashboardData()
Refresca la información mostrada en el dashboard, incluyendo activos, servicios, ubicaciones, notificaciones, y otros datos asociados al usuario.

### render()
Renderiza la vista `livewire.usuarios.dashboard-usuario` con los datos del dashboard.

### countActivos($id)
Retorna el número de activos que posee un usuario o empresa.

### countUbicaciones($id)
Retorna el número de ubicaciones registradas para un usuario o empresa.

### countServicios($activos)
Retorna el número de servicios solicitados para una lista de activos.

### getActivos($id)
Obtiene todos los activos del usuario autenticado.

### getUbicaciones($id)
Obtiene todas las ubicaciones del usuario, junto con la cantidad de activos presentes en cada ubicación.

### getNotificaciones($id)
Obtiene notificaciones relevantes para el usuario, tales como invitaciones pendientes de aceptar.

### getServicios()
Obtiene los servicios solicitados por el usuario autenticado que están esperando confirmación.

### getTipos()
Obtiene los tipos de activos disponibles en el sistema.

### getUserId()
Obtiene el identificador del usuario autenticado.

### getEmpresas($datos)
Obtiene las empresas prestadoras asociadas a los servicios proporcionados.

### getActividad($value)
Obtiene la actividad económica asociada a una empresa.

### activosServiciosSolicitud()
Obtiene las solicitudes de servicios que están esperando confirmación de la prestadora.

### activosServiciosPendiente()
Obtiene los servicios pendientes de confirmación por parte de la prestadora.

### activosServiciosVisita()
Obtiene los servicios que ya fueron confirmados y están esperando ser realizados.

### actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
Actualiza el estado de un usuario-empresa a "Aceptado" o "Rechazado".

### actualizarEstado($nuevoEstado, $cuit)
Método público que permite actualizar el estado de una relación entre usuario y empresa.

### empresasDatos()
Obtiene todas las empresas disponibles en el sistema.

### update($servicio)
Abre un modal con la información del servicio para permitir su modificación.

## Vista del Componente
El componente se representa mediante una vista que contiene distintas secciones, tales como:

- **Columna Izquierda**: Muestra un resumen del usuario con su foto de perfil, activos clasificados por tipo, y una sección de novedades que se refresca automáticamente cada 30 segundos.
- **Categorías de Activos**: Muestra tarjetas con los diferentes tipos de activos y la cantidad de activos correspondientes.
- **Novedades**: Notificaciones sobre servicios y solicitudes recibidas, con opciones para aceptar o rechazar.
- **Columna Derecha**: Contiene el calendario de servicios y el carrusel de ubicaciones del usuario. Se pueden alternar entre la vista de calendario y la vista de ubicaciones utilizando un botón de alternancia.
- **Calendario de Servicios**: Muestra las fechas de los próximos servicios programados.
- **Carrusel de Ubicaciones**: Permite navegar por las distintas ubicaciones registradas del usuario, mostrando detalles como la dirección, el tipo de propiedad y los activos presentes.

La interfaz está diseñada con un enfoque en la experiencia del usuario, incluyendo transiciones suaves para cambiar entre la vista de calendario y la de ubicaciones. También se proporciona un campo de búsqueda para facilitar la localización rápida de ubicaciones.

