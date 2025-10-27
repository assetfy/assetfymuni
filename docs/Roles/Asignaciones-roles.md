**Documentación del Componente AsignacionesRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `AsignacionesRoles` se utiliza para gestionar la asignación de roles a usuarios dentro de un sistema de gestión de usuarios y permisos. Permite la visualización, edición y creación de asignaciones de roles, lo cual facilita la administración de acceso y permisos para los diferentes usuarios de una empresa.

## Dependencias
Este componente depende de varios modelos, helpers y otras funcionalidades:

- **Modelos**:
  - `AsignacionesRolesModel`: Utilizado para gestionar las asignaciones de roles a usuarios.
  - `RolesModel`: Para obtener información sobre los roles disponibles en el sistema.

- **Helpers**:
  - `IdHelper`: Utilizado para obtener el identificador de la empresa asociada al usuario actual.

## Propiedades
- `$title`: Título del componente, que se muestra como encabezado en la interfaz. En este caso, se establece como "Asignación de Roles".
- `$createForm`: Nombre del componente Livewire para la creación de nuevas asignaciones de roles (`CrearAsignacionesRoles`).
- `$editForm`: Nombre del componente Livewire para la edición de asignaciones de roles (`roles.edit-asignaciones-roles`).
- `$value`: Valor utilizado para almacenar el identificador de la empresa.
- `$cuit`: CUIT de la empresa asociada al usuario actual.
- `$useSelection`: Bandera booleana que indica si se permite la selección de filas dentro del componente. En este caso, se establece como `false`.

## Métodos

### `query() : Builder`
Este método define la consulta para obtener las asignaciones de roles asociadas a los roles de una empresa específica. Utiliza el helper `getRoles()` para obtener los roles correspondientes y realiza la consulta en el modelo `AsignacionesRolesModel`.

### `getRoles()`
Obtiene los roles relacionados con la empresa asociada al usuario actual utilizando el helper `IdHelper`. Este método devuelve una colección de roles de la empresa.

### `update($value)`
Este método se encarga de abrir el modal de edición para una asignación de roles específica. Utiliza el valor proporcionado para abrir el modal (`roles.edit-asignaciones-roles`) a través del método `dispatch()`.

### `columns() : array`
Define las columnas que se muestran en la tabla de asignaciones de roles. Las columnas incluyen:
- **Rol**: Nombre del rol asignado.
- **Permiso**: Nombre del permiso asociado al rol.
- **Usuario**: Nombre del usuario al cual se le asigna el rol.
- **Empresa**: Razón social de la empresa asociada.

### `CrearAsignacionesRoles()`
Este método abre el modal para crear una nueva asignación de roles utilizando el componente (`roles.create-asignaciones-roles`) a través del método `dispatch()`.

## Vista del Componente
El componente `AsignacionesRoles` se basa en la biblioteca `LivewireTables` de RamonRietdijk, lo que permite la creación de tablas dinámicas con filtros, búsquedas y ordenamiento. El componente ofrece una tabla con información relevante sobre las asignaciones de roles:

- **Encabezado**: El título del componente se muestra como "Asignación de Roles".
- **Columnas de la Tabla**:
  - **Rol**: Nombre del rol que se ha asignado.
  - **Permiso**: Permisos asociados al rol seleccionado.
  - **Usuario**: Nombre del usuario que tiene el rol asignado.
  - **Empresa**: Nombre de la empresa a la que está relacionado el usuario.

Además, el componente tiene la capacidad de abrir un modal tanto para la creación como para la edición de asignaciones de roles, facilitando la administración y modificación de roles de usuarios en el sistema.

