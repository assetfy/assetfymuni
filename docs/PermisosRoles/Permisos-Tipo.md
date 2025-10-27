**Documentación del Componente PermisosPorTipo**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `PermisosPorTipo` se utiliza para gestionar y mostrar los permisos asignados a los diferentes tipos de empresa en un sistema de permisos y roles. Utiliza la librería `LivewireTables` de RamonRietdijk para gestionar la visualización en formato de tabla, permitiendo aplicar filtros y realizar búsquedas sobre los permisos, los tipos de empresa, y las rutas asignadas.

## Dependencias
Este componente depende de los siguientes elementos para su correcto funcionamiento:

- **Modelos**:
  - `PermisoPorEmpresaModel`: Modelo que representa la relación entre los permisos y los tipos de empresa.

- **Librería**:
  - `RamonRietdijk\LivewireTables`: Librería utilizada para crear la tabla de visualización interactiva.

## Propiedades
- `$model`: Define el modelo base que se utilizará para construir las consultas de datos en la tabla. En este caso, se trata de `PermisoPorEmpresaModel`.
- `$title`: Define el título del encabezado de la tabla. En este caso es "Permiso por tipo de empresa".
- `$createForm`: Nombre del componente de creación predeterminado, definido como `permisosRolescargarRutas`, que se utiliza para cargar un formulario de creación de permisos.
- `$useSelection`: Esta propiedad, de tipo booleana, se establece en `false` para indicar que no se permitirá la selección de filas en la tabla.

## Métodos

### `columns()`
Este método define las columnas que se mostrarán en la tabla. Las columnas incluyen:
- **Nombre de Permiso** (`permisos.nombre`): Columna que muestra el nombre del permiso. Esta columna es **buscable** para facilitar la ubicación de permisos específicos.
- **Tipo de Empresa** (`tipo_empresa`): Muestra el tipo de empresa al que pertenece el permiso.
- **Ruta** (`rutas.nombre`): Muestra el nombre de la ruta asignada al permiso.
- **Dirección Url** (`rutas.ruta`): Muestra la URL de la ruta asignada al permiso.

### `filters()`
Define los filtros que se pueden aplicar a la tabla. En este caso, se define un filtro de selección (´SelectFilter´) para el campo `tipo_empresa`, permitiendo al usuario filtrar por tipos de empresa específicos:
- **Tipo de Empresa** (`tipo_empresa`): Las opciones disponibles son "Empresa", "Empresa Prestadora", "Empresa reguladora", y "Estado".

### `permisosRolescargarRutas()`
Este método se utiliza para disparar el evento `permisosRolescargarRutas`, lo que redirige al componente `crear-permiso-tipo-empresa` para agregar un nuevo permiso relacionado con un tipo de empresa específico.

## Vista del Componente
El componente `PermisosPorTipo` utiliza `LivewireTables` para generar una tabla interactiva que permite gestionar los permisos asignados a diferentes tipos de empresa. A continuación se describe la estructura de la vista:

### Estructura de la Tabla
- **Título de la Tabla**: El título "Permiso por tipo de empresa" se muestra como encabezado de la tabla.
- **Columnas**: La tabla incluye columnas para "Nombre de Permiso", "Tipo de Empresa", "Ruta" y "Dirección URL", proporcionando información detallada sobre cada permiso.
- **Filtro de Tipo de Empresa**: Un filtro que permite seleccionar entre diferentes tipos de empresa para visualizar los permisos correspondientes.
- **Botón "Crear Permiso por Empresa"**: Se proporciona un botón para redirigir al componente de creación de un permiso nuevo asignado a un tipo de empresa, utilizando el evento `permisosRolescargarRutas`.

