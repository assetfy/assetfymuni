**Documentación del Componente PermisosRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `PermisosRoles` se utiliza para gestionar la relación entre permisos y roles dentro del sistema. Este componente permite listar los permisos y roles asignados a una empresa o un usuario específico. Además, facilita la creación y asignación de permisos y roles mediante la funcionalidad del formulario de creación.

## Dependencias
Este componente depende de varios modelos y helpers para su correcto funcionamiento:

- **Modelos**:
  - `PermisosRolesModel`: Para gestionar los permisos y roles dentro del sistema.

- **Helpers**:
  - `IdHelper`: Para obtener el identificador de la empresa asociada al usuario autenticado.

- **Servicios de Autenticación**:
  - `Auth`: Para obtener información del usuario autenticado y su panel actual.

## Propiedades
- `$model`: Define el modelo asociado al componente, en este caso `PermisosRolesModel`.
- `$title`: Título del encabezado, utilizado en la vista del componente. Valor predeterminado: "Permisos y roles".
- `$createForm`: Nombre del componente de creación predeterminado. Valor: `CrearPermisosRoles`.
- `$empresa`: Variable que almacena el identificador de la empresa asociada al usuario, si corresponde.
- `$user`: Variable que almacena el identificador del usuario actual.
- `$useSelection`: Bandera que indica si se debe utilizar la selección de filas. En este caso, está deshabilitado (valor `false`).

## Métodos

### `asignar()`
Este método determina si el usuario autenticado pertenece a una empresa o si se debe trabajar con el usuario individualmente. Si el panel actual del usuario es "Empresa", se asigna el identificador de la empresa a `$empresa` utilizando `IdHelper::idEmpresa()`. En caso contrario, se asigna el identificador del usuario a `$user`.

### `query() : Builder`
Este método construye la consulta utilizada para obtener los permisos y roles relacionados con el usuario o empresa autenticada. Utiliza el resultado del método `asignar()` para filtrar la información basada en el CUIT de la empresa o el identificador del usuario. Devuelve una instancia del `Builder` con la consulta personalizada.

### `columns() : array`
Define las columnas que se mostrarán en la tabla del componente:
- **Id permiso**: Identificador del permiso asignado.
- **Rol**: Identificador del rol asociado.
- **Creador**: Usuario o empresa que creó la relación de permiso-rol.

Cada columna es sortable y searchable, permitiendo al usuario ordenar y buscar en la tabla.

### `CrearPermisosRoles()`
Este método dispara el evento `CrearPermisosRoles`, el cual está vinculado al componente `crear-permisos-roles` para abrir el formulario de creación de permisos y roles.

## Vista del Componente
Este componente no tiene una vista explícita en el código proporcionado, ya que hereda la vista de la clase `LivewireTable` del paquete `RamonRietdijk\LivewireTables`. Sin embargo, se sabe que la tabla mostrará los permisos y roles con las columnas definidas en el método `columns()`. Además, se incluye un botón de creación que abre el formulario `CrearPermisosRoles` cuando se invoca.

El encabezado de la tabla tendrá como título "Permisos y roles" y podrá crear nuevos registros mediante el formulario de creación especificado.