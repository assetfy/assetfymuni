**Documentación del Componente CreateAsignacionesRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateAsignacionesRoles` se utiliza para asignar roles a los usuarios en el sistema. Permite seleccionar un usuario, un rol y luego crear una asignación específica entre el rol y el usuario dentro de una empresa. Este componente facilita la gestión y administración de accesos dentro del sistema.

## Dependencias
Este componente depende de varios modelos, traits y otros servicios para su correcto funcionamiento:

- **Modelos**:
  - `RolesModel`: Para gestionar los roles disponibles.
  - `EmpresasModel`: Para obtener la información de las empresas.
  - `PermisosRolesModel`: Para gestionar los permisos asociados a cada rol.
  - `UsuariosEmpresasModel`: Para gestionar la relación entre los usuarios y las empresas.
  - `AsignacionesRolesModel`: Para crear la asignación de un rol a un usuario.
  - `User`: Para obtener los usuarios registrados en el sistema.

- **Servicios**:
  - `MiddlewareInvoker`: Para verificar si el usuario tiene los permisos necesarios para realizar ciertas acciones.

- **Helpers**:
  - `IdHelper`: Para obtener el identificador de la empresa asociada al usuario.

## Propiedades
- `$open`: Bandera booleana que indica si el modal de asignación de roles está abierto o cerrado.
- `$id_rol`: Identificador del rol seleccionado para asignar al usuario.
- `$id_unico_rol`: Identificador único del rol.
- `$usuario_empresa`: Identificador del usuario asociado a la empresa.
- `$cuit`: CUIT de la empresa asociada al usuario actual.
- `$rolesFiltrados`: Roles disponibles para asignar, excluyendo los ya asignados.
- `$id_relacion_empresa`: Identificador de la relación entre el usuario y la empresa.
- `$usuario`: Información del usuario seleccionado.
- `$permisos`: Lista de permisos asociados al rol seleccionado.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.roles.create-asignaciones-roles`).

### `openModalAsignarRol($data = null)`
Se ejecuta cuando se escucha el evento `openModalAsignarRol`. Verifica si el usuario tiene permisos para realizar esta acción utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si tiene permisos, abre el modal y, si se pasa `$data`, carga la información del usuario asociado a la empresa.

### `updated($propertyName)`
Valida las propiedades que se actualizan en el componente.

### `mount()`
Inicializa el componente, obteniendo el identificador de la empresa (`cuit`) asociada al usuario actual.

### `save()`
Valida los campos necesarios y crea la asignación de un rol al usuario utilizando el modelo `AsignacionesRolesModel`. Luego, emite los eventos `lucky` y `refreshLivewireTable` para notificar la creación de la asignación y refrescar las tablas correspondientes.

### `close()`
Cierra el modal de asignación de roles y restablece todas las propiedades del componente a sus valores predeterminados.

### `updatedIdRol()`
Actualiza la lista de permisos asociados al rol seleccionado utilizando el modelo `PermisosRolesModel`.

### `updatedUsuarioEmpresa()`
Actualiza la relación de la empresa con el usuario seleccionado.

### `updateRelacionEmpresa($usuario_empresa)`
Obtiene la relación entre la empresa y el usuario, estableciendo el valor de `$id_relacion_empresa` si existe una relación.

## Vista del Componente
La vista del componente `create-asignaciones-roles` contiene un modal que permite asignar roles a usuarios. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Asignar Rol" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Razón Social (`x-label`)**: Muestra la razón social de la empresa asociada al usuario actual.
    - **Usuario (`x-label`)**: Permite seleccionar un usuario para asignar el rol o muestra el nombre del usuario seleccionado.
    - **Rol (`x-label`)**: Permite seleccionar un rol de la lista de roles disponibles.
  
  - **Footer del Modal**:
    - **Botón "Asignar rol" (`x-danger-button`)**: Ejecuta la acción de guardar la asignación del rol al usuario, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar la asignación.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

