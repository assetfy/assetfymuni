**Documentación del Componente EditAsignacionesRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditAsignacionesRoles` se utiliza para gestionar la edición de las asignaciones de roles dentro del sistema. Permite editar los roles asignados, permisos, y usuarios asociados a cada asignación, proporcionando una manera fácil y organizada de administrar los roles de los usuarios.

## Dependencias
Este componente depende de varios modelos, traits y servicios:

- **Modelos**:
  - `AsignacionesRolesModel`: Gestiona la relación entre roles y usuarios.
  - `UsuariosEmpresasModel`: Para obtener información sobre los usuarios asociados a las empresas.
  - `PermisosRolesModel`: Gestiona los permisos asociados a los roles.
  - `EmpresasModel`: Para gestionar la información de las empresas.
  - `PermisosModel`: Para manejar los permisos del sistema.
  - `RolesModel`: Para manejar los roles disponibles.
  - `User`: Modelo de usuarios para gestionar la información de los mismos.

- **Servicios**:
  - `MiddlewareInvoker`: Para verificar si el usuario tiene los permisos necesarios para realizar ciertas acciones.

- **Traits**:
  - `VerificacionTrait`: Proporciona métodos para verificar y actualizar los registros.

## Propiedades
- `$open`: Bandera booleana que indica si el modal de edición de asignación de roles está abierto o cerrado.
- `$id_rol`: Identificador del rol asignado.
- `$cuit`: CUIT de la empresa asociada al usuario.
- `$asignacionRoles`: Datos de la asignación de roles actual.
- `$upRol`: Valor actualizado del rol.
- `$upPermiso`: Valor actualizado del permiso.
- `$upUsuario`: Usuario actualizado.
- `$upUnicoRol`: Identificador único del rol.
- `$id_relacion_empresa`: Identificador de la relación entre el usuario y la empresa.
- `$rolesFiltrados`: Roles filtrados según el permiso seleccionado.
- `$userIds`: Identificadores de usuarios filtrados según el CUIT de la empresa.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.roles.edit-asignaciones-roles`). Proporciona los datos necesarios para la vista, como los roles, permisos, empresas y usuarios.

### `openModalEditarPermisos($data)`
Se ejecuta cuando se escucha el evento `openModalEditarPermisos`. Verifica si el usuario tiene los permisos necesarios para realizar la acción y luego abre el modal para editar la asignación.

### `openModalAsignacion($data)`
Carga los datos de la asignación de roles según el identificador proporcionado. Inicializa las propiedades del componente con los datos de la asignación actual y abre el modal de edición.

### `update()`
Valida y actualiza los datos de la asignación de roles. Usa el trait `VerificacionTrait` para verificar los datos y emitir un evento para refrescar la tabla de Livewire.

### `close()`
Cierra el modal de edición y restablece las propiedades del componente a sus valores predeterminados.

### `Permiso($value)`
Llama al método `selectPermiso()` para seleccionar un permiso específico.

### `selectPermiso($value)`
Filtra los roles según el permiso seleccionado y actualiza la lista de roles disponibles.

### `updatedUsuarioEmpresa()`
Actualiza la relación de la empresa según el usuario seleccionado.

### `updateRelacionEmpresa($usuario_empresa)`
Actualiza el identificador de la relación entre la empresa y el usuario según el CUIT y el usuario proporcionado.

## Vista del Componente
La vista del componente `edit-asignaciones-roles` contiene un modal que permite al usuario editar la asignación de roles. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Edición (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Editar Asignación de Roles" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Campo de Razon Social**: Muestra la razón social de la empresa asociada según el CUIT proporcionado.
    - **Select para Usuario**: Permite seleccionar un usuario de la lista de usuarios filtrados.
    - **Select para Rol**: Permite seleccionar un rol de la lista de roles disponibles.
    - **Select para Permiso**: Permite seleccionar un permiso de la lista de permisos disponibles.
  
  - **Footer del Modal**:
    - **Botón "Actualizar" (`x-danger-button`)**: Ejecuta la acción de guardar los cambios de la asignación, desencadenando el método `update()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de actualizar.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

