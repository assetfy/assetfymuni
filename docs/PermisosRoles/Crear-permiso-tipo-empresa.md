**Documentación del Componente CrearPermisoTipoEmpresa**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearPermisoTipoEmpresa` se utiliza para gestionar la asignación de rutas a un permiso específico según el tipo de empresa dentro de un sistema de permisos y roles. Permite al usuario seleccionar un tipo de empresa y un permiso, para posteriormente asociar las rutas disponibles, facilitando la administración de los accesos en el sistema.

## Dependencias
Este componente depende de varios modelos, servicios y otros recursos para su correcto funcionamiento:

- **Modelos**:
  - `PermisosModel`: Para manejar la lista de permisos disponibles en el sistema.
  - `TiposEmpresaModel`: Para gestionar los tipos de empresa disponibles.
  - `RutasModel`: Para gestionar las rutas que se pueden asignar.
  - `PermisoPorEmpresaModel`: Para gestionar las asociaciones entre permisos, tipos de empresa y rutas.

- **Servicios**:
  - `MiddlewareInvoker`: Para verificar si el usuario tiene los permisos necesarios para realizar ciertas acciones.

## Propiedades
- `$permisos`: Colección de permisos disponibles para la asignación.
- `$tipo_empresa`: Colección de tipos de empresa disponibles.
- `$rutas`: Colección de rutas disponibles para asignar.
- `$id_tipo`: Identificador del tipo de empresa seleccionado (inicialmente `null`).
- `$selectedPermiso`: Identificador del permiso seleccionado.
- `$selectedTipoEmpresa`: Identificador del tipo de empresa seleccionado.
- `$selectedRutas`: Array de identificadores de rutas seleccionadas.
- `$open`: Bandera booleana que indica si el modal de creación está abierto o cerrado.
- **Listeners**: `permisosRolescargarRutas` es un evento escuchado que permite abrir el modal.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.permisos-roles.crear-permiso-tipo-empresa`).

### `permisosRolescargarRutas()`
Se ejecuta cuando se escucha el evento `permisosRolescargarRutas`. Verifica si el usuario tiene permisos utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene permisos, establece `$open` como `true` para abrir el modal.

### `mount()`
Inicializa el componente cargando los permisos y los tipos de empresa disponibles. Define `$rutas` como una colección vacía inicialmente.

### `cargarRutas($value)`
Se utiliza para cargar las rutas que pueden ser asignadas a un permiso, basándose en el tipo de empresa seleccionado. Primero, verifica que el tipo de empresa esté seleccionado. Si hay un permiso y un tipo de empresa seleccionados, obtiene las rutas disponibles que no están ya asignadas al permiso especificado.

### `save()`
Verifica si se han seleccionado un permiso, un tipo de empresa y al menos una ruta. Si se cumplen estos requisitos, crea nuevas asociaciones en el modelo `PermisoPorEmpresaModel` para cada ruta seleccionada. Después de guardar, emite el evento `lucky` y restablece la colección de rutas, cerrando el modal.

### `close()`
Cierra el modal restableciendo las propiedades seleccionadas (`selectedPermiso`, `selectedTipoEmpresa`, `selectedRutas`) a sus valores predeterminados, y establece `$open` como `false`.

## Vista del Componente
La vista del componente `crear-permiso-tipo-empresa` contiene un modal que permite al usuario asociar rutas a un permiso para un tipo de empresa específico. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.

  - **Título del Modal**: Muestra el texto "Registra las rutas" para indicar la acción que se va a realizar.

  - **Contenido del Modal**:
    - **Select para Tipo de Empresa**: Permite al usuario seleccionar un tipo de empresa de la lista disponible. Si no se selecciona un tipo de empresa, los campos dependientes quedarán deshabilitados.
    
    - **Select para Permiso (`select`)**: Permite al usuario seleccionar un permiso de la lista. Solo está habilitado si se ha seleccionado un tipo de empresa.
    
    - **Checkbox para Rutas**: Si hay rutas disponibles, se muestra una lista de rutas con la opción de seleccionarlas mediante checkboxes para asociarlas al permiso y tipo de empresa seleccionado.

  - **Footer del Modal**:
    - **Botón "Crear Permiso por Empresa" (`x-danger-button`)**: Ejecuta la acción de guardar la asociación entre el permiso, el tipo de empresa y las rutas seleccionadas, desencadenando el método `save()`.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

