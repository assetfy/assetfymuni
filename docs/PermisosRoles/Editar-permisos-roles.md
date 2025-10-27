**Documentación del Componente EditarPermisosRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditarPermisosRoles` se utiliza para editar los permisos y roles asignados a un usuario dentro del sistema. Permite visualizar la información actual del usuario, el rol asignado, y modificar dicho rol y sus permisos si es necesario. Esta funcionalidad es clave para gestionar el acceso a las funcionalidades del sistema, garantizando que los usuarios tengan los permisos adecuados.

## Dependencias
Este componente utiliza varios modelos, servicios y traits para su correcto funcionamiento:

- **Modelos**:
  - `AsignacionesRolesModel`: Para gestionar las asignaciones de roles y permisos a los usuarios.
  - `UsuariosEmpresasModel`: Para obtener la información de las empresas asociadas a los usuarios.
  - `PermisosRolesModel`: Para gestionar los permisos vinculados a un rol.
  - `PermisosModel`: Para manejar la lista de permisos disponibles.
  - `RolesModel`: Para gestionar los roles existentes en el sistema.
  - `User`: Para obtener la información del usuario actual.

- **Servicios**:
  - `MiddlewareInvoker`: Verifica si el usuario tiene permisos para realizar la acción de editar roles y permisos.

- **Traits**:
  - `VerificacionTrait`: Proporciona funciones útiles para verificar y actualizar registros.

## Propiedades
- `$id_rol`: Identificador del rol que se va a actualizar.
- `$id_permiso`: Identificador del permiso.
- `$updaterol`: Rol actualizado seleccionado por el usuario.
- `$udatepermiso`: Permiso actualizado seleccionado por el usuario.
- `$roles_permisos`: Lista de roles y permisos disponibles para el usuario.
- `$permisosUser`: Información de los permisos asignados al usuario.
- `$roles`: Información del rol asignado al usuario actual.
- `$permiso`: Lista de permisos asociados al rol.
- `$user`: Información del usuario actual.
- `$open`: Bandera booleana que indica si el modal de edición de roles y permisos está abierto o cerrado.
- `$mostrarBotonModificar`: Bandera booleana que determina si se debe mostrar el botón para modificar la relación entre permisos y roles.
- `$rolesDisponibles`: Lista de roles que pueden asignarse al usuario, excluyendo el rol que ya tiene asignado.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.permisos-roles.editar-permisos-roles`).

### `openModalEditarPermisos($data)`
Escucha el evento `openModalEditarPermisos`. Verifica si el usuario tiene los permisos adecuados para editar roles y permisos utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si tiene permiso, se llama al método `opendModal($data)`.

### `actualizar()`
Llama al método `actualizarPermiso()` para realizar la actualización de los permisos del usuario.

### `opendModal($data)`
Abre el modal para editar roles y permisos. Carga la información del usuario, los roles y los permisos correspondientes:
- Carga el usuario asociado a la empresa.
- Carga la información del rol asignado.
- Carga todos los roles disponibles para el usuario, excluyendo el rol que ya tiene asignado.
- Inicializa los permisos del rol actual.
- Establece `$open` como `true` para abrir el modal.

### `actualizarPermiso()`
Actualiza el rol del usuario y guarda los cambios en la base de datos. Luego emite el evento `lucky` y cierra el modal estableciendo `$open` como `false`.

### `updatePermisos($value)`
Actualiza la lista de permisos que están asignados al rol seleccionado. También establece `$mostrarBotonModificar` como `true` para mostrar el botón de modificación.

### `close()`
Cierra el modal y restablece las propiedades del componente a sus valores predeterminados.

## Vista del Componente
La vista del componente `editar-permisos-roles` contiene un modal que permite al usuario editar los permisos y el rol asignado a un usuario. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Relación permisos y roles" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Usuario**: Muestra el nombre del usuario al que se le están editando los permisos y roles.
    - **Rol Asignado**: Muestra el rol actualmente asignado al usuario.
    - **Select para Roles Disponibles**: Permite al usuario seleccionar un rol de la lista de roles disponibles.
    - **Lista de Permisos Disponibles**: Muestra los permisos disponibles asociados al rol seleccionado, distribuidos en columnas de hasta cinco elementos.
  
  - **Footer del Modal**:
    - **Botón "Modificar Relación" (`x-danger-button`)**: Ejecuta la acción de guardar los cambios realizados en la relación entre el rol y los permisos seleccionados, desencadenando el método `actualizar()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesan los cambios.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

