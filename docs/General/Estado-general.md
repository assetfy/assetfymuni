**Documentación del componente EstadoGeneral**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `EstadoGeneral` es un componente de Livewire que utiliza la funcionalidad de `LivewireTable` para listar y crear y editar registros del modelo `EstadoGeneralModel`. Este componente permite a los usuarios ver el estado general de diferentes elementos de la aplicación, con opciones de búsqueda y edición directa desde la tabla.

### Propiedades
- **$title**: Título del componente, definido como "Estado General".
- **$createForm**: Nombre del componente de creación predeterminado, en este caso `createEstadoGeneral`.
- **$useSelection**: Propiedad booleana que indica si la selección de filas está habilitada. En este componente, está deshabilitada (`false`).

### Métodos
- **createEstadoGeneral()**: Método que despacha el evento `CreateEstadogeneral` para redirigir al componente `general.create-estado-general`, permitiendo la creación de un nuevo registro de estado general.

- **columns()**: Define las columnas que se mostrarán en la tabla de estado general:
  - **Nombre**: Columna que muestra el nombre del estado. Es sortable y searchable.
  - **Descripción**: Columna que muestra la descripción del estado.
  - **Acciones**: Columna con un botón que permite editar un registro mediante el evento `EditarEstadoGeneral`, con el valor del ID del registro seleccionado.

### Vista del Componente
El componente `EstadoGeneral` proporciona una vista de tabla que incluye:

- **Listado de Estados Generales**: La tabla muestra las columnas de "Nombre", "Descripción" y "Acciones". Cada fila tiene un botón de "Editar" que permite al usuario modificar el estado general.
- **Creación de Estados Generales**: Al hacer clic en el botón de creación, se despacha un evento para abrir el componente `general.create-estado-general`, donde se pueden agregar nuevos registros.

