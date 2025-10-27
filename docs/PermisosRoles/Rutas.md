**Documentación del Componente Rutas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas Definidas](#columnas-definidas)

## Descripción General
El componente `Rutas` se utiliza para gestionar la visualización de las rutas disponibles dentro del sistema. Muestra una lista de todas las rutas con su información correspondiente, como el nombre y la URL. Este componente permite a los usuarios ver y gestionar las rutas a través de una interfaz sencilla, utilizando Livewire y el paquete `LivewireTables` de RamonRietdijk.

## Dependencias
Este componente depende de las siguientes clases y modelos:

- **Modelos**:
  - `RutasModel`: Modelo que representa las rutas almacenadas en la base de datos.

- **Paquetes**:
  - `RamonRietdijk\LivewireTables\Livewire\LivewireTable`: Clase base para la creación de tablas con Livewire.
  - `RamonRietdijk\LivewireTables\Columns\Column`: Para definir las columnas de la tabla que se mostrará.

## Propiedades
- `$model`: Especifica el modelo asociado al componente, en este caso, `RutasModel`.
- `$title`: Define el título del encabezado de la tabla, que en este componente es "Lista de Rutas en el sistema".
- `$createForm`: Nombre del componente para la creación de nuevas rutas, que se define como `permisosRolescargarRutas`.
- `$useSelection`: Especifica si se permite la selección de filas en la tabla, y se establece como `false` para este componente.

## Métodos

### `columns()`
Define las columnas que serán visibles en la tabla de rutas. Las columnas definidas son:
- **Id Ruta** (`id_ruta`): Muestra el identificador de cada ruta.
- **Nombre de la ruta** (`nombre`): Permite visualizar el nombre de cada ruta. Esta columna es searchable, lo que significa que se puede buscar por el nombre de la ruta.
- **URL** (`ruta`): Muestra la dirección URL asociada a la ruta.

### `permisosRolescargarRutas()`
Este método despacha un evento llamado `permisosRolescargarRutas` para abrir el componente `cargar-rutas`, que permite registrar nuevas rutas en el sistema.

## Columnas Definidas
- **Id Ruta**: Identificador único para cada ruta.
- **Nombre de la ruta**: Descripción textual que identifica cada ruta dentro del sistema. Esta columna tiene habilitada la opción de búsqueda.
- **URL**: La dirección URL que representa la ruta en el sistema, permitiendo el acceso a diferentes recursos o funcionalidades dentro de la aplicación.

