**Documentación del Componente UsuariosServiciosAutorizacion**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Propiedades](#propiedades)
4. [Métodos](#métodos)
5. [Vista del Componente](#vista-del-componente)
6. [Consideraciones de Uso](#consideraciones-de-uso)
7. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El componente `UsuariosServiciosAutorizacion` es un componente de Livewire que se encarga de gestionar la autorización de servicios solicitados por los usuarios. Permite al usuario visualizar los detalles de un servicio solicitado, aceptar o rechazar el presupuesto del servicio, y actualizar la información relacionada.

### Dependencias
El componente depende de varios modelos y servicios para gestionar la información y proporcionar una interfaz interactiva para el usuario:

- **Modelos**:
  - `App\Models\ActivosModel`: Maneja los activos relacionados con el servicio.
  - `App\Models\EmpresasModel`: Gestiona la información de las empresas que prestan el servicio.
  - `App\Models\ServiciosModel`: Contiene la información de los servicios disponibles.
  - `App\Models\SolicitudesServiciosModel`: Maneja las solicitudes de servicios realizadas por los usuarios.
  - `App\Models\UbicacionesModel`: Almacena las ubicaciones relacionadas con los activos.

- **Servicios**:
  - `App\Services\MiddlewareInvoker`: Servicio utilizado para verificar permisos antes de realizar ciertas acciones.

- **Facades**:
  - `auth()`: Utilizado para obtener el usuario actualmente autenticado.

### Propiedades
- **open**: Booleano que controla la apertura del modal de autorización.
- **servicios**: Contiene la información del servicio actual que está siendo visualizado o editado.
- **presupuesto, precio, activo, ubicacion, servicio, aceptacion, rutaDocumento, fechaHora, empresa, descripcion, mensaje**: Propiedades utilizadas para almacenar y mostrar información del servicio, activo, empresa, y otros datos relevantes durante el proceso de autorización.
- **listeners**: Define los eventos a los que el componente escucha (ej. `openModal`, `openModaltabla`, `update`, `openModalServiciosSolicitados`).

### Métodos
- **mount(SolicitudesServiciosModel $value)**: Inicializa el componente cargando la información del servicio solicitado si está disponible.
- **openModal($data)**: Abre el modal para el servicio especificado, verificando primero los permisos del usuario.
- **openModalServiciosSolicitados($data)**: Verifica permisos y luego llama al método `openModaltabla` para abrir el modal correspondiente.
- **openModaltabla($value)**: Carga los datos del servicio solicitado y abre el modal para autorización.
- **update($servicioId)**: Actualiza la información del servicio especificado y abre el modal.
- **fechaModificacion()**: Establece la fecha y hora del servicio, actualizando el mensaje si la fecha fue modificada previamente.
- **cargarDatos()**: Carga la información del activo, ubicación, servicio, y empresa relacionados con la solicitud.
- **actualizar()**: Guarda los cambios realizados al servicio y cierra el modal.
- **guardarDatos()**: Almacena los datos actualizados del servicio en la base de datos, dependiendo de si el presupuesto fue aceptado o rechazado.
- **formatFechaHora($fechaHora)**: Formatea la fecha y hora en un formato adecuado para almacenarla.
- **close()**: Cierra el modal y reinicia ciertas propiedades del componente.
- **render()**: Renderiza la vista del componente, mostrando las solicitudes de servicios del usuario.

### Vista del Componente
La vista del componente `UsuariosServiciosAutorizacion` utiliza un modal para mostrar la información del servicio solicitado y permitir al usuario tomar decisiones sobre la autorización del mismo. Los elementos clave incluyen:

- **Modal de Solicitud**: Un modal que muestra detalles del servicio solicitado, incluyendo el activo asociado, el servicio solicitado, la fecha y hora, el precio, y la aceptación del presupuesto.
- **Formulario de Autorización**: Permite al usuario aceptar o rechazar el presupuesto. Si se rechaza, se solicita un motivo de rechazo.
- **Acciones del Usuario**: Los botones en el modal permiten al usuario actualizar la información o cancelar la acción.

### Consideraciones de Uso
- **Permisos de Usuario**: Todas las acciones importantes, como abrir el modal, actualizar la información o guardar datos, están protegidas por el servicio `MiddlewareInvoker` para asegurar que el usuario tiene permisos adecuados.
- **Estados del Servicio**: El componente maneja diferentes estados del presupuesto, como 'Aceptado' o 'Rechazado', y proporciona mensajes claros al usuario sobre el estado del servicio.
- **Manejo del Modal**: La propiedad `open` controla si el modal está abierto o cerrado, proporcionando una interfaz amigable para gestionar los servicios solicitados.

### Ejemplos de Uso
El componente `UsuariosServiciosAutorizacion` se utiliza en aplicaciones donde los usuarios necesitan revisar y gestionar los servicios que han solicitado. Es ideal para casos en los que se desea proporcionar una experiencia interactiva que permita a los usuarios aceptar o rechazar presupuestos de servicios, manteniendo una trazabilidad clara de las decisiones tomadas.
