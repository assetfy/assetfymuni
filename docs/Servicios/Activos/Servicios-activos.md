**Documentación del Componente ServiciosActivos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `ServiciosActivos` es un componente de tabla dinámica que muestra los servicios solicitados para un activo específico. Utiliza la biblioteca de Livewire Tables proporcionada por `RamonRietdijk`. Este componente permite visualizar información detallada de los servicios relacionados con los activos, como el tipo de servicio, descripción, estado y empresa prestadora. Además, permite aplicar filtros sobre los registros mostrados, como el servicio y la fecha.

## Propiedades
- `$model`: Modelo de datos que será gestionado por el componente. En este caso, es `SolicitudesServiciosModel`.
- `$title`: Título del encabezado de la tabla. En este caso, se establece como "SERVICIOS SOLICITADOS PARA EL ACTIVO".
- `$createForm`: Nombre del componente de creación predeterminado (vacío en este caso).
- `$editForm`: Nombre del componente de edición predeterminado (vacío en este caso).
- `$cuit`: CUIT de la empresa asociada al usuario.
- `$empresa`: Instancia del modelo de empresa, relacionada al usuario.
- `$valor`: Valor del identificador, que puede ser el CUIT o el CUIL según corresponda.
- `$identificadores`: Identificadores completos de la entidad actual.
- `$previousUrl`: URL anterior del usuario.
- `$useSelection`: Indica si se permite la selección de filas en la tabla. Se establece como `false`.

## Métodos

### `mount()`
Llama al método `mount` de la clase padre para inicializar la configuración. También inicializa el activo relacionado con la solicitud y guarda la URL previa del usuario.

### `initializeActivo()`
Obtiene el identificador del activo a través de la ruta y lo almacena en la sesión para ser utilizado durante la generación de la tabla.

### `query()`
Devuelve la consulta para obtener los servicios solicitados para el activo especificado. Utiliza el identificador almacenado en la sesión.

### `asignar()`
Asigna los identificadores de la empresa o el usuario, dependiendo de los valores obtenidos mediante `IdHelper`. Este método determina si el valor será el CUIT o el CUIL, según el contexto.

### `columns()`
Define las columnas a mostrar en la tabla. Dependiendo de si la empresa tiene un CUIT asociado, se mostrarán diferentes columnas utilizando los métodos `columnsEmpresa` o `columnsUsuarios`.

### `commonColumns()`
Devuelve un arreglo de columnas comunes que se mostrarán para todas las filas, independientemente de si se está visualizando como empresa o como usuario.

### `columnsEmpresa()`
Agrega una columna específica para la empresa solicitante al conjunto de columnas comunes.

### `columnsUsuarios()`
Agrega una columna específica para el solicitante al conjunto de columnas comunes.

### `filters()`
Devuelve un arreglo de filtros aplicables a la tabla. Incluye un filtro de selección de servicios y un filtro de fecha.

### `getServicios()`
Obtiene los servicios disponibles para las solicitudes registradas en la tabla y devuelve un arreglo de opciones para el filtro de selección de servicios.

### `isSelectable($row)`
Define la lógica para determinar si una fila es seleccionable. En este caso, devuelve siempre `false`, lo cual significa que ninguna fila es seleccionable.

## Vista del Componente
El componente `ServiciosActivos` no tiene una vista específica definida, ya que se basa en la generación de una tabla dinámica utilizando las herramientas de Livewire Tables de `RamonRietdijk`. La tabla generada mostrará la siguiente información:

### Columnas de la Tabla
- **Foto**: Imagen asociada al servicio, con un tamaño de 75x75 px.
- **Servicio**: Nombre del servicio solicitado.
- **Tipo**: Tipo del servicio solicitado.
- **Categoría**: Categoría del servicio.
- **Subcategoría**: Subcategoría del servicio.
- **Fecha**: Fecha en que se realizará el servicio, formateada como `Y-m-d`.
- **Descripción**: Descripción del servicio solicitado.
- **Estado**: Estado del servicio solicitado.
- **Empresa Prestadora**: Nombre de la empresa que prestará el servicio.
- **Empresa Solicitante**: (Solo visible para empresas) Empresa que solicitó el servicio.
- **Solicitante**: (Solo visible para usuarios) Nombre del usuario solicitante del servicio.

### Filtros de la Tabla
- **Servicios**: Filtra los registros por el servicio seleccionado.
- **Fecha**: Filtra los registros por la fecha del servicio.

