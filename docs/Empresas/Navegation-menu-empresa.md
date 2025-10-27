**Documentación del componente NavegationMenu de Empresas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `NavegationMenu` de Livewire se encarga de gestionar la navegación del usuario a través de diferentes perfiles y tipos de empresa. Dependiendo del tipo de empresa o el rol del usuario, el componente determina qué menú y opción se debe mostrar. Este componente ofrece un menú de navegación adaptable para el panel de control de las empresas y puede cambiar entre diferentes vistas según las preferencias y selección del usuario.

### Propiedades

- **$panelActual**: Determina el panel que está activo actualmente, como Usuario, Estado, Empresa, etc.
- **$estadoActual**: Estado actual del panel.
- **$tipoMenu**: Define el tipo de menú que se muestra; por defecto, es "empresas".
- **$empresas**: Lista de las empresas asociadas al usuario.
- **$empresa**: La empresa actual seleccionada.
- **$lista**: Lista de empresas aceptadas para el usuario.
- **$estado**: Estado de las empresas asociadas.
- **$ubicaciones**: Ubicaciones asociadas a la empresa seleccionada.
- **$solicitudes**: Solicitudes de empresa para autorización.
- **$cuitSeleccionado**: CUIT de la empresa seleccionada.
- **$actividad**: Actividades pendientes de autorización.
- **$tipo**: Tipo de usuario en la empresa.
- **$servicios**: Solicitudes de servicios de la empresa.
- **$tipoEmpresa**: Tipo de la empresa seleccionada.
- **$cuitEmpresaSeleccionado**: CUIT de la empresa seleccionada en la sesión.
- **$user**: Datos del usuario autenticado.
- **$logo**: Logo de la empresa seleccionada.

### Métodos

- **mount()**: Inicializa el componente cargando la información del CUIT seleccionado, la empresa, los servicios y las actividades.

- **datosActualizado()**: Actualiza el CUIT seleccionado en la sesión, basado en el usuario autenticado.

- **loadData()**: Carga los datos relevantes para el panel de navegación, incluyendo la empresa y el estado actual.

- **logoEmpresa()**: Obtiene el logo de la empresa seleccionada.

- **listaEmpresa()**: Genera una lista de empresas aceptadas asociadas al usuario.

- **CargarEmpresaDatos()**: Carga la información de la empresa seleccionada y verifica si se necesitan cargar solicitudes.

- **CargarSolicitudes($cuitEmpresaSeleccionado)**: Carga las solicitudes de empresas que están en revisión.

- **loadSolicitudesActividad()**: Carga actividades pendientes de autorización si el tipo de empresa es "4" (Estado).

- **loadSolicitudesServicios()**: Carga solicitudes de servicios que están pendientes para la empresa seleccionada.

- **tipoUserEmpresa()**: Determina el tipo de usuario dentro de la empresa seleccionada.

- **tipoEmpresa()**: Determina el tipo de la empresa seleccionada.

- **render()**: Renderiza la vista del componente `NavegationMenu`, incluyendo el menú y las opciones de navegación.

- **cargarUbicaciones()**: Carga las ubicaciones de la empresa seleccionada.

- **updateTipo($cuitSeleccionado)**: Actualiza el tipo de panel de acuerdo con el CUIT seleccionado. Si el CUIT pertenece a un usuario, redirige al dashboard del usuario; si es una empresa, cambia el panel según el tipo de empresa.

- **updateUserPanel($cuit)**: Actualiza el panel del usuario en la base de datos, según el CUIT proporcionado.

- **comprobacion($cuit)**: Determina el tipo de empresa según su CUIT y devuelve el nombre del panel correspondiente.

- **actualizarDatosSession($cuitSeleccionado)**: Actualiza la sesión con el CUIT seleccionado.

- **loadDataForUsuarios($user)**: Carga datos específicos para usuarios cuando el panel cambia a "Usuario".

### Vista del Componente

La vista del componente `NavegationMenu` incluye los siguientes elementos:

- **Logo de la Empresa**: Muestra el logo de la empresa si está disponible, o las iniciales del nombre de la empresa si no hay logo disponible.
- **Botón de Menú Lateral**: Permite al usuario abrir o cerrar el menú lateral.
- **Selección de Perfil**: Un botón desplegable que muestra la lista de empresas a las que el usuario tiene acceso. Permite cambiar entre diferentes empresas o cambiar al perfil de usuario.
- **Notificaciones**: Muestra un icono de campana que indica las notificaciones pendientes (solicitudes, actividades, servicios) según el tipo de empresa.
- **Menú de Usuario**: Incluye opciones como "Perfil" y "Desconectarse" para gestionar la sesión del usuario actual.

Esta vista asegura que el usuario pueda navegar entre diferentes secciones del sistema de manera intuitiva, dependiendo de su perfil y los permisos que tenga dentro de la empresa.