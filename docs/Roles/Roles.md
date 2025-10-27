**Documentación del Componente Roles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `Roles` se utiliza para gestionar los roles dentro del sistema, permitiendo la visualización, búsqueda y ordenamiento de los roles existentes, así como la creación de nuevos roles. Este componente facilita la administración de roles, mostrando información relevante como el nombre del rol y la razón social asociada.

## Dependencias
Este componente depende de los siguientes elementos:

- **Modelos**:
  - `RolesModel`: Representa los roles del sistema y se utiliza para obtener y manejar la información relacionada con los roles.
  - `IdHelper`: Helper que se utiliza para obtener el identificador de la empresa asociada al usuario.

- **Servicios**:
  - `RamonRietdijk\LivewireTables\Livewire\LivewireTable`: Se utiliza para proporcionar funcionalidad de tabla interactiva con Livewire.
  - `RamonRietdijk\LivewireTables\Columns\Column`: Proporciona las columnas para las tablas dentro del componente.

## Propiedades
- `$model`: Define el modelo utilizado por el componente, que en este caso es `RolesModel`.
- `$title`: Título del componente que se muestra en la vista, definido como "Roles".
- `$createForm`: Nombre del componente de creación, en este caso es `CrearRoles`, para agregar nuevos roles.
- `$value`: Valor entero utilizado para almacenar el identificador de la empresa.
- `$useSelection`: Define si se habilita la selección de filas, establecido como `false` en este componente.

## Métodos

### `query()`
Devuelve una consulta (`Builder`) con los roles que pertenecen a la empresa obtenida a través del método `getRoles()`. Filtra los roles basándose en el valor de `cuit` proporcionado.

### `getRoles()`
Obtiene los roles disponibles para la empresa asociada al usuario actual. Utiliza `getValue()` para obtener el identificador de la empresa y luego filtra los roles basándose en el `cuit` de la empresa.

### `getValue()`
Obtiene el identificador de la empresa mediante el helper `IdHelper` y lo asigna a la propiedad `$value`.

### `update($value)`
Desencadena el evento `openModal` para abrir el modal de edición de roles. Se pasa el identificador del rol que se desea actualizar, y redirige al componente `roles.editar-roles`.

### `columns()`
Devuelve un array con la configuración de las columnas para la tabla. Las columnas definidas son:
- **Nombre**: Columna que muestra el nombre del rol, con funcionalidad de búsqueda y ordenamiento.
- **Razón Social**: Columna que muestra la razón social asociada, con funcionalidad de búsqueda y ordenamiento.

### `CrearRoles()`
Desencadena el evento `CrearRoles` para abrir el componente `roles.create-roles` y crear un nuevo rol en el sistema.

## Vista del Componente
La vista del componente `Roles` muestra una tabla interactiva que permite gestionar los roles. A continuación se describe cada parte de la vista:

### Estructura de la Tabla
- **Columnas de la Tabla**:
  - **Nombre**: Columna que muestra el nombre del rol. Es sortable y searchable.
  - **Razón Social**: Columna que muestra la razón social de la empresa asociada al rol. También es sortable y searchable.
- **Botones de Acción**:
  - **Editar**: Al hacer clic en el botón de edición, se abre el modal correspondiente para editar el rol seleccionado.

El componente proporciona funcionalidades útiles para el administrador, facilitando la gestión de roles dentro del sistema de forma eficiente.

