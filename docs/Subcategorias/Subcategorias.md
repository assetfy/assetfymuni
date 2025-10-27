**Documentación del Componente Subcategoria**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **Subcategoria** permite la gestión de las subcategorías dentro de la aplicación. Este componente permite visualizar, filtrar y editar las subcategorías existentes a través de una tabla interactiva. Además, ofrece opciones para crear nuevas subcategorías mediante un formulario modal.

## Dependencias
### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Utilizado para construir la tabla interactiva que muestra las subcategorías.
- **RamonRietdijk\LivewireTables\Filters\SelectFilter**: Utilizado para aplicar filtros en la tabla de subcategorías.
- **RamonRietdijk\LivewireTables\Columns\ImageColumn** y **Column**: Utilizados para definir las columnas de la tabla, incluyendo imágenes y otros atributos.
- **App\Models\SubcategoriaModel**: Modelo que representa la entidad Subcategoría en la base de datos.

## Propiedades
- **\$model**: Define el modelo asociado al componente, en este caso **SubcategoriaModel**.
- **\$title**: Define el título del encabezado de la tabla, que es **Subcategoria**.
- **\$createForm**: Nombre del componente de creación predeterminado, utilizado para abrir el formulario de creación de subcategorías.
- **\$useSelection**: Define si se permite la selección de filas en la tabla. En este caso, se encuentra deshabilitado (valor **false**).

## Métodos

### `columns()`
Este método define las columnas que se mostrarán en la tabla. Algunas de las columnas definidas incluyen:
- **Imagen**: Muestra una imagen relacionada con la subcategoría.
- **Sigla**: La sigla de la subcategoría, con opciones de ordenamiento y búsqueda.
- **Nombre**: El nombre de la subcategoría, también con opciones de ordenamiento y búsqueda.
- **Movil o Fijo**: Especifica si la subcategoría es móvil o fija.
- **Se relaciona**: Indicador de la relación de la subcategoría con otras entidades.
- **Descripción**: Descripción detallada de la subcategoría.
- **Acciones**: Botón para editar la subcategoría seleccionada.

### `filters()`
Define los filtros disponibles para la tabla, permitiendo a los usuarios aplicar filtros por:
- **Tipo**: Utiliza el método **getTipos()** para obtener una lista de tipos relacionados con las subcategorías.
- **Categoría**: Utiliza el método **getCategorias()** para obtener una lista de categorías relacionadas con las subcategorías.

### `getTipos()` y `getCategorias()`
Estos métodos obtienen los tipos y categorías disponibles para ser utilizados en los filtros de la tabla. Se basa en las relaciones establecidas con el modelo **SubcategoriaModel** para asegurar que solo se muestren los tipos y categorías que tengan subcategorías asociadas.

### `crearSubcategoria()`
Este método se encarga de despachar el evento **crearSubcategoria**, que abre el componente de creación de una nueva subcategoría.

## Vista del Componente
El componente **Subcategoria** está basado en una tabla de **Livewire** que muestra las subcategorías con columnas para cada atributo relevante. Además, se incluyen acciones que permiten editar los registros directamente desde la tabla.

### Filtros Disponibles
- **Filtro de Tipo**: Permite al usuario filtrar las subcategorías por su tipo relacionado.
- **Filtro de Categoría**: Permite al usuario filtrar las subcategorías por la categoría relacionada.

### Acciones
- **Editar Subcategoría**: A través del botón **Editar** ubicado en cada fila de la tabla, se puede abrir un modal para editar la subcategoría seleccionada.
- **Crear Subcategoría**: Se utiliza el componente de creación para agregar nuevas subcategorías.

Este componente está diseñado para facilitar la administración de subcategorías, permitiendo agregar, editar y filtrar subcategorías de una manera clara y organizada.

