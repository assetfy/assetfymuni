**Documentación del Componente Notificaciones**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Propiedades](#propiedades)
4. [Métodos](#métodos)
5. [Vista del Componente](#vista-del-componente)
6. [Consideraciones de Uso](#consideraciones-de-uso)
7. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El componente `Notificaciones` es un componente de Livewire encargado de gestionar y mostrar las notificaciones relacionadas con el usuario autenticado en la aplicación. Estas notificaciones incluyen invitaciones de empresas, auditorías pendientes, y solicitudes de servicios en espera de confirmación. Además, el componente ofrece la funcionalidad de cambiar el estado de la relación entre el usuario y una empresa.

### Dependencias
El componente depende de varios modelos para gestionar la información relacionada con las notificaciones y otros aspectos del usuario:

- **Modelos**:
  - `App\Models\UsuariosEmpresasModel`: Maneja la relación entre usuarios y empresas, así como sus estados de asociación.
  - `App\Models\AuditoriasModel`: Gestiona las auditorías asociadas al usuario.
  - `App\Models\SolicitudesServiciosModel`: Contiene las solicitudes de servicios realizadas por el usuario y su estado.
  - `App\Models\EmpresasModel`: Gestiona la información de las empresas registradas.

- **Facades**:
  - `auth()`: Utilizado para obtener el usuario actualmente autenticado.

### Propiedades
- **user**: Instancia del usuario autenticado.
- **notificaciones**: Contiene las notificaciones pendientes del usuario, relacionadas con empresas.
- **auditorias**: Lista de auditorías relacionadas con el usuario.
- **empresas**: Empresas vinculadas a las notificaciones del usuario.
- **solicitudes**: Solicitudes de servicios realizadas por el usuario en estado de espera.
- **prestadoras**: Empresas prestadoras vinculadas a las solicitudes de servicios pendientes.
- **selectedSection**: Sección seleccionada para visualizar notificaciones (por defecto es 'todos').

### Métodos
- **mount()**: Inicializa el componente cargando la información del usuario, las notificaciones, las empresas vinculadas, auditorías y solicitudes pendientes. Además, notifica al usuario si hay nuevas solicitudes o invitaciones.
- **render()**: Renderiza la vista del componente, incluyendo las notificaciones, auditorías, solicitudes y empresas asociadas.
- **notificacionesAuditoria()**: Recupera todas las auditorías relacionadas con el usuario autenticado.
- **notificaciones()**: Obtiene las notificaciones del usuario sobre su asociación con empresas (por ejemplo, estado 'En Revisión').
- **notificacionessolicitudServicios()**: Recupera las solicitudes de servicios realizadas por el usuario que están esperando confirmación.
- **actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)**: Actualiza el estado de la asociación entre el usuario y la empresa especificada.
- **actualizarEstado($nuevoEstado, $cuit)**: Actualiza el estado de la asociación entre el usuario y la empresa y refresca la lista de notificaciones.
- **cambiarSeccion($seccion)**: Cambia la sección de notificaciones seleccionada por el usuario.
- **notifyUser($message)**: Muestra un mensaje de notificación al usuario utilizando JavaScript.

### Vista del Componente
La vista del componente `Notificaciones` incluye diferentes elementos que permiten la gestión y visualización de las notificaciones:

- **Botón de Toggle Sidebar**: Permite abrir o cerrar la barra lateral en dispositivos móviles para facilitar la navegación.
- **Dropdown de Perfiles**: Permite al usuario cambiar entre los diferentes perfiles disponibles, como usuario o empresa. Este dropdown se refresca periódicamente para asegurar que la información esté actualizada.
- **Dropdown de Notificaciones**: Muestra las notificaciones pendientes del usuario, incluyendo invitaciones para unirse a empresas y solicitudes de servicios que requieren atención.
- **Dropdown de Usuario**: Contiene opciones para acceder al perfil del usuario y para cerrar la sesión de manera segura.

### Consideraciones de Uso
- **Actualización Periódica**: El dropdown de notificaciones y de perfiles se refrescan cada 30 segundos para mantener la información al día.
- **Responsividad**: La interfaz del componente está diseñada para adaptarse a diferentes tamaños de pantalla, asegurando una buena experiencia tanto en dispositivos móviles como en escritorios.
- **Visualización de Notificaciones**: Se muestra al usuario un icono con una marca visual si tiene notificaciones pendientes.

### Ejemplos de Uso
El componente `Notificaciones` se puede utilizar en cualquier parte de la aplicación para proporcionar al usuario un acceso rápido y sencillo a sus notificaciones, auditorías y solicitudes de servicios.



