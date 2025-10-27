**Documentación del componente CreateEstadoGeneral**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `CreateEstadoGeneral` permite la creación de un nuevo registro de estado general en el sistema. Se presenta como un modal que contiene un formulario con campos para el nombre y la descripción del estado. Este componente incluye validación de datos antes de almacenar la información en la base de datos.

### Propiedades
- **$open**: Booleano que indica si el modal está abierto o cerrado.
- **$nombre**: Almacena el nombre del estado general a crear.
- **$descripcion**: Almacena la descripción del estado general.

### Listeners
- **CreateEstadogeneral**: Listener que se activa para abrir el modal cuando se requiere crear un nuevo estado general.

### Métodos
- **updated($propertyName)**: Valida la propiedad especificada cuando se actualiza. Utilizado para la validación en tiempo real de los campos del formulario.

- **save()**: Valida todos los datos del formulario y los guarda en la base de datos si son válidos. Además, cierra el modal una vez completada la operación.

- **render()**: Renderiza la vista principal del componente.

- **close()**: Restablece las propiedades `nombre` y `descripcion` y cierra el modal.

- **registro()**: Registra el nuevo estado general en la base de datos utilizando el modelo `EstadoGeneralModel`.

- **validar()**: Valida los datos del formulario y llama al método `registro()` si los datos son válidos.

- **CreateEstadogeneral()**: Muestra el modal para crear un nuevo estado general. Verifica los permisos del usuario antes de permitir la acción mediante `MiddlewareInvoker`.

### Vista del Componente
El componente `CreateEstadoGeneral` tiene una vista que se presenta como un modal utilizando `x-dialog-modal`. La vista contiene:

- **Formulario de Creación de Estado General**:
  - **Campo Nombre**: Un campo de selección que permite elegir entre varios estados como "Baja", "Service", y "Normal/Funcionando".
  - **Campo Descripción**: Un campo de entrada de texto para describir el estado general.

- **Botones del Footer**:
  - **Cancelar**: Botón que cierra el modal y descarta cualquier cambio realizado.
  - **Crear Estado General**: Botón que guarda el nuevo registro después de validar los datos.

