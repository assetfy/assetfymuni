**Documentación del Componente NavegationMenu**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `NavegationMenu` es un componente de Livewire que se encarga de gestionar el menú de navegación para los usuarios dentro de la aplicación. Este menú permite al usuario cambiar entre diferentes perfiles, recibir notificaciones y acceder a distintos paneles, dependiendo de su rol y estado actual dentro de la aplicación.

## Dependencias
Este componente depende de varios modelos y clases que se utilizan para obtener y procesar los datos del usuario. A continuación se listan las dependencias:

- **Modelos**:
  - `App\Models\SolicitudesServiciosModel`: Maneja la información relacionada con las solicitudes de servicios realizadas por el usuario.
  - `App\Models\UsuariosEmpresasModel`: Relaciona usuarios con empresas y gestiona los estados de asociación.
  - `App\Models\EmpresasModel`: Gestiona la información de las empresas registradas en la aplicación.
  - `App\Models\TiposEmpresaModel`: Maneja los diferentes tipos de empresas disponibles en el sistema.
  - `App\Models\User`: Modelo del usuario que almacena información del usuario autenticado.

- **Facades**:
  - `Illuminate\Support\Facades\Session`: Permite manipular la sesión del usuario para almacenar y recuperar datos temporales.
  - `Illuminate\Support\Facades\Auth`: Facilita la autenticación y la obtención del usuario actual.

## Propiedades
- **empresa**: Almacena la información de las empresas disponibles en el sistema.
- **auditorias**: Almacena las auditorías relacionadas con el usuario.
- **notificaciones**: Contiene las notificaciones pendientes del usuario.
- **lista**: Utilizada para almacenar listas de datos temporales.
- **cuitSeleccionado**: CUIT de la empresa seleccionada por el usuario.
- **tipoMenu**: Define el tipo de menú a mostrar, por defecto es 'usuarios'.
- **estadoActual**: Estado actual del usuario autenticado.
- **panelActual**: Panel actual del usuario, puede variar dependiendo del tipo de usuario (Ej. Empresa, Prestadora, etc.).
- **empresas**: Empresas asociadas al usuario autenticado.
- **estado**: Estado de las empresas del usuario.
- **ubicaciones**: Ubicaciones asociadas al usuario.
- **solicitudes**: Solicitudes de servicios realizadas por el usuario.
- **hasEmpresas**: Booleano que indica si el usuario tiene empresas asociadas.
- **notificacionesCount**: Número de notificaciones pendientes del usuario.
- **user**: Instancia del usuario autenticado.

## Métodos
### mount()
Este método se ejecuta al inicializar el componente y carga la información relevante del usuario, como empresas, estado, notificaciones y solicitudes de servicios.

### render()
Renderiza la vista del menú de navegación, dependiendo del tipo de menú (`usuarios`, `empresa`, etc.).

### refreshData()
Actualiza los datos del componente, recargando las empresas, estado, notificaciones y solicitudes del usuario.

### updateTipo($cuitSeleccionado)
Actualiza el tipo de usuario seleccionado, cargando el panel correspondiente y redirigiendo al usuario según el tipo de empresa seleccionada.

### ActualizarUserYentidad($user, $panel_actual, $cuitSeleccionado)
Actualiza la entidad y el panel actual del usuario en la base de datos.

### actualizarEstado($nuevoEstado, $cuit)
Actualiza el estado de una relación entre usuario y empresa a 'Aceptado' o 'Rechazado'.

### loadEmpresas($user)
Carga las empresas disponibles en el sistema y asocia las empresas aceptadas al usuario autenticado.

### loadEstado($user)
Carga el estado actual del usuario autenticado.

### loadSolicitudes($user)
Carga las solicitudes de servicios realizadas por el usuario que están esperando confirmación.

### loadNotificaciones($user)
Carga las notificaciones pendientes del usuario y cuenta la cantidad de notificaciones.

### getPanelActual($cuitSeleccionado)
Obtiene el panel actual del usuario según el CUIT seleccionado. Determina el tipo de empresa y devuelve el panel correspondiente (Ej. Empresa, Prestadora, Controladora, etc.).

### redirigirUser($cuitSeleccionado, $user, $verificacion)
Determina la URL a la que debe redirigir al usuario según el panel actual y realiza la redirección.

### actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
Actualiza el estado de la relación entre el usuario y la empresa en la base de datos.

### reloadNotificaciones()
Vuelve a cargar las notificaciones del usuario para mantener la información actualizada.

## Vista del Componente
El componente se representa mediante una vista que contiene las siguientes secciones:

- **Botón de Toggle Sidebar**: Permite a los usuarios móviles abrir o cerrar la barra lateral para facilitar la navegación.
- **Dropdown de Perfiles**: Permite al usuario cambiar entre los perfiles disponibles (Ej. Usuario, Empresa). Este dropdown se refresca cada 30 segundos para mantener la información actualizada.
- **Dropdown de Notificaciones**: Muestra las notificaciones pendientes del usuario. Incluye invitaciones a unirse a empresas y solicitudes pendientes de aprobación.
- **Dropdown de Usuario**: Contiene opciones para acceder al perfil del usuario y para cerrar la sesión.

La interfaz está diseñada para ser intuitiva, con un diseño responsive que se adapta a diferentes tamaños de pantalla. Los elementos de la navegación incluyen transiciones suaves y botones interactivos para mejorar la experiencia del usuario.
