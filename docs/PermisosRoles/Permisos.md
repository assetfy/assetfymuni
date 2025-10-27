**Documentación del Componente Permisos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas del Componente](#columnas-del-componente)

## Descripción General
El componente `Permisos` es una tabla que extiende `LivewireTable` del paquete `RamonRietdijk\LivewireTables`. Este componente se utiliza para gestionar los permisos dentro del sistema de una forma interactiva y eficiente. Permite visualizar, editar y gestionar los permisos disponibles, con un enfoque principal en la facilidad de administración mediante una interfaz intuitiva.

## Dependencias
- **Paquetes**:
  - `RamonRietdijk\LivewireTables`: Utilizado para implementar la tabla interactiva que permite realizar operaciones CRUD con facilidad.

- **Modelos**:
  - `PermisosModel`: Modelo que representa los permisos del sistema, utilizado para obtener y gestionar los datos de la tabla.

## Propiedades
- **`$model`**: Representa el modelo utilizado para poblar la tabla, en este caso, `PermisosModel::class`.
- **`$title`**: Establece el título del encabezado de la tabla. En este componente se ha configurado como "Permisos".
- **`$createForm`**: Define el nombre del componente predeterminado para la creación de nuevos permisos, en este caso, `CrearPermisos`.
- **`$useSelection`**: Indica si se permite la selección de filas dentro de la tabla. En este caso, se ha desactivado estableciendo su valor en `false`.

## Métodos

### `columns()`
Este método define las columnas que se mostrarán en la tabla. Cada columna tiene un título y, opcionalmente, acciones que se pueden realizar en cada fila. En este componente, se definen dos columnas:
- **`Nombre`**: Muestra el nombre del permiso.
- **`Acciones`**: Permite realizar acciones sobre cada permiso, como editarlo, a través de un botón "Editar".

### `CrearPermisos()`
Este método despacha un evento (`CrearPermisos`) que abre el componente `crear-permisos`. Este método facilita la creación de nuevos permisos desde la interfaz de la tabla.

## Columnas del Componente

### Columnas Definidas en `columns()`

1. **Columna: `Nombre`**
   - **Propiedad**: `nombre`
   - **Funcionalidad**: Muestra el nombre del permiso. 

2. **Columna: `Acciones`**
   - **Contenido**: Botón HTML para editar el permiso.
   - **Funcionalidad**: 
     - El botón ejecuta el evento `openEditarPermisos`, pasando el identificador del permiso al cual pertenece la fila correspondiente.
     - El botón está estilizado con clases de CSS que le otorgan un aspecto destacado (botón azul).

   - **Configuración Adicional**:
     - **`clickable(false)`**: Define que la columna no es clicable como un todo, ya que el botón dentro de ella se maneja de forma individual.
     - **`asHtml()`**: Permite que el contenido de la columna sea interpretado como HTML para mostrar el botón "Editar" correctamente.