**Documentación del componente DetalleEstado**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Filtros](#filtros)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `DetalleEstado` es un componente de Livewire que permite al usuario visualizar y gestionar el estado de una empresa en el sistema. Utiliza información almacenada en los modelos `AuditoriasModel`, `EmpresasModel`, y `NotificacionesModel` para mostrar detalles relevantes y, si es necesario, eliminar registros de auditoría y notificación. Este componente también incluye un modal que permite la edición de los detalles relacionados con el estado de una empresa.

### Propiedades

- **$cuit**: Almacena el CUIT de la empresa relacionada con la notificación o auditoría.
- **$registro**: Contiene la información del registro obtenido de `NotificacionesModel` para una empresa específica.
- **$empresas**: Almacena una colección de todas las empresas registradas en el sistema, utilizada para mostrar información adicional.
- **$descripcion**: Describe el motivo del rechazo o del estado actual de la empresa.
- **$emisora**: Almacena la empresa emisora de la notificación.
- **$open**: Indica si el modal de detalles está abierto o cerrado.

### Listeners

- **Abrir Modal**: Se utiliza el listener `$set('open', true)` para abrir el modal que muestra los detalles del estado de la empresa.
- **Cerrar Modal**: Se utiliza `$set('open', false)` para cerrar el modal cuando el usuario hace clic en "Cancelar".

### Filtros

No se utilizan filtros específicos en este componente. La lógica de selección se maneja directamente en el método `mount()` y en la vista para filtrar las empresas.

### Métodos

- **render()**: Este método renderiza la vista principal del componente, que incluye el modal y el botón que permite al usuario acceder a los detalles del estado.

- **mount(AuditoriasModel $value)**: Método de inicialización del componente. Este método se ejecuta al montar el componente y se encarga de cargar la información del registro de notificación, la emisora y la descripción del estado a partir de `NotificacionesModel` y `EmpresasModel`. Se inicializan las propiedades del componente con la información obtenida, y se carga la colección de empresas desde `EmpresasModel`.

- **visto()**: Este método elimina los registros relacionados en `AuditoriasModel` y `NotificacionesModel` cuando el estado ha sido visualizado por el usuario. Posteriormente, se invoca el método `eventos()` para gestionar cualquier acción adicional necesaria tras la eliminación de registros.

### Vista del Componente

La vista del componente `DetalleEstado` incluye los siguientes elementos:

- **Botón Editar**: Permite al usuario abrir el modal para ver o editar los detalles del estado de la empresa. El modal contiene la información relevante y acciones que el usuario puede realizar, como marcar la notificación como vista o cerrar el modal.

- **Modal de Detalle**: Un modal que muestra detalles del estado de la empresa, incluyendo la razón del rechazo, la empresa emisora, y otros comentarios pertinentes. El modal también incluye botones para cancelar la operación o marcar la notificación como vista, eliminando así los registros relacionados del sistema.
