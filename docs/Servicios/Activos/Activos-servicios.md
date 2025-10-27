**Documentación del Componente ActivosServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `ActivosServicios` es un `LivewireTable` que se utiliza para mostrar los servicios realizados sobre un activo en el sistema. La información se presenta en una tabla que incluye detalles como el tipo de servicio, la empresa prestadora, las categorías y subcategorías del activo, entre otros. El componente incluye funcionalidad para filtrar columnas específicas según el contexto del usuario (empresa o usuario individual).

## Propiedades
- **\$model**: Especifica el modelo utilizado en el componente (`ServiciosActivosModel`).
- **\$title**: Título del componente, que aparece en la interfaz de usuario. En este caso, es "Servicios realizados al activo".
- **\$createForm**: Nombre del componente de creación predeterminado. Inicializado como cadena vacía.
- **\$editForm**: Nombre del componente de edición predeterminado. Inicializado como cadena vacía.
- **\$cuit, \$empresa, \$valor, \$identificadores**: Propiedades utilizadas para almacenar información del contexto del usuario o empresa asociado.
- **\$previousUrl**: URL previa guardada para permitir la navegación hacia atrás desde el componente.
- **\$useSelection**: Bandera booleana que indica si se permite la selección en la tabla. Inicializada como `false`.

## Métodos

### `mount()`
Este método se ejecuta al inicializar el componente. Invoca el método `initializeActivo()` para inicializar el ID del activo actual, si está disponible en la URL de la petición. También establece `\$previousUrl` como la URL previa del navegador.

### `initializeActivo()`
Inicializa el ID del activo utilizando la URL de la petición. Si el ID está presente, se guarda en la sesión bajo la clave `activo`.

### `query()`
Este método genera la consulta que se utiliza para recuperar los datos del activo seleccionado de la base de datos. Utiliza el ID del activo almacenado en la sesión para filtrar los registros.

### `asignar()`
Este método se encarga de asignar los valores necesarios para el componente, como el CUIT del usuario o empresa actual. Si el CUIT no está disponible, se asigna el valor del CUIL del usuario autenticado.

### `columns()`
Define las columnas de la tabla que se mostrarán en la vista. Según si el usuario tiene asociada una empresa o no, muestra diferentes columnas (utilizando `columnsEmpresa()` o `columnsUsuarios()`).

### `commonColumns()`
Este método define las columnas comunes para todas las vistas, incluyendo información como el servicio, la empresa prestadora, el tipo y la categoría del activo, entre otros.

### `columnsEmpresa()`
Retorna las columnas específicas cuando el usuario está asociado a una empresa. Agrega la columna "Empresa Solicitante" a las columnas comunes.

### `columnsUsuarios()`
Retorna las columnas específicas cuando el usuario es un usuario individual. Agrega la columna "Solicitante" a las columnas comunes.

## Vista del Componente
Este componente `ActivosServicios` es parte de una tabla interactiva que muestra información detallada sobre los servicios realizados a un activo específico. A continuación se describen algunas de las principales columnas y su función:

- **Foto**: Contiene un enlace para ver la imagen del servicio realizado al activo. La columna se muestra como HTML para ofrecer una experiencia de usuario enriquecida, permitiendo abrir la imagen en una nueva pestaña.
- **Servicio**: Muestra el nombre del servicio prestado.
- **Empresa Prestadora**: Muestra la razón social de la empresa que realizó el servicio.
- **Tipo, Categoría, Subcategoría y Activo**: Muestran información detallada sobre la clasificación del activo.
- **Descripción**: Detalles o comentarios sobre el servicio realizado.
- **Estado**: Muestra el estado del servicio realizado.
- **Fecha**: Muestra la fecha en la que se realizó el servicio, formateada como `Y-m-d - H:i`.

