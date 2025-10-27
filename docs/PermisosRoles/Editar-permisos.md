**Documentación del Componente EditarPermisos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditarPermisos` se utiliza para gestionar la edición de permisos dentro de un sistema de permisos y roles. Permite actualizar el nombre de un permiso existente, facilitando la administración de permisos dentro del sistema.

## Dependencias
Este componente depende de varios modelos, traits y servicios para su correcto funcionamiento:

- **Modelos**:
  - `PermisosModel`: Utilizado para interactuar con la base de datos de permisos y obtener la información del permiso a editar.

- **Servicios**:
  - `MiddlewareInvoker`: Para verificar si el usuario tiene los permisos necesarios para realizar ciertas acciones antes de permitir la edición de permisos.

- **Traits**:
  - `VerificacionTrait`: Proporciona métodos para verificar y realizar acciones relacionadas con la creación y actualización de registros.

## Propiedades
- `$nombre`: Nombre del permiso que se desea editar.
- `$id_permisos`: Identificador del permiso que se desea editar.
- `$permisos`: Permiso que se está editando.
- `$updatedNombre`: Nombre actualizado del permiso que será guardado.
- `$open`: Bandera booleana que indica si el modal de edición de permisos está abierto o cerrado.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.permisos-roles.editar-permisos`).

### `actualizar()`
Este método se encarga de actualizar la información del permiso llamando al método `actualizarPermiso()`. Posteriormente, cierra el modal de edición estableciendo `$open` como `false`.

### `actualizarPermiso()`
Realiza la verificación y actualización del nombre del permiso utilizando el trait `VerificacionTrait`. Una vez completada la actualización, emite el evento `refreshLivewireTable` para refrescar la tabla de permisos.

### `close()`
Cierra el modal de edición de permisos estableciendo `$open` como `false`.

### `openEditarPermisos($data)`
Este método se llama cuando se escucha el evento `openEditarPermisos`. Verifica si el usuario tiene permisos para editar utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene permisos, llama al método `openModal()` con los datos proporcionados.

### `openModal($value)`
Recibe el identificador del permiso a editar y carga la información del permiso utilizando el modelo `PermisosModel`. Si el permiso es encontrado, llama al método `cargarDatos()` y establece `$open` como `true` para abrir el modal.

### `cargarDatos($permisos)`
Carga los datos del permiso en las propiedades correspondientes, como `$updatedNombre`, para que se puedan mostrar y editar en el modal.

## Vista del Componente
La vista del componente `editar-permisos` contiene un modal que permite al usuario editar un permiso existente. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Edición (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Relación permisos y roles" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Usuario y Rol Asignado**: Muestra el nombre del usuario y el rol asignado actualmente.
    - **Select para Roles Disponibles**: Permite seleccionar un rol de la lista de roles disponibles para el usuario.
    - **Lista de Permisos Disponibles**: Muestra una lista de permisos disponibles para el rol seleccionado, cada uno mostrado como un checkbox deshabilitado para indicar los permisos actuales.
  
  - **Footer del Modal**:
    - **Botón "Modificar Relación" (`x-danger-button`)**: Ejecuta la acción de actualizar la relación entre el rol y los permisos seleccionados, desencadenando el método `actualizar()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de actualizar la relación.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.