**Documentación del Componente ServiciosSubcategorias**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **ServiciosSubcategorias** se encarga de gestionar la visualización y administración de subcategorías de servicios mediante una tabla interactiva. Los usuarios pueden buscar, ordenar y filtrar las subcategorías, además de tener la opción de crear nuevas entradas a través de un formulario modal.

## Dependencias

### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Base para la tabla interactiva con Livewire.
- **RamonRietdijk\LivewireTables\Filters\SelectFilter**: Utilizado para definir filtros en la tabla.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **App\Models\ServiciosSubcategoriasModel**: Modelo que representa las subcategorías de servicios.

## Propiedades
- **\$model**: Define el modelo utilizado por la tabla, en este caso, **ServiciosSubcategoriasModel**.
- **\$title**: Título que se muestra en la parte superior de la tabla ("Servicios Subcategoria").
- **\$createForm**: Nombre del componente utilizado para crear nuevas subcategorías ("CreateServiciosSubcategoria").
- **\$userId**: Identificador del usuario actual.
- **\$useSelection**: Define si se permite la selección de filas en la tabla (valor predeterminado `false`).

## Métodos

### `CreateServiciosSubcategoria()`
Llama al método **dispatch()** para abrir el componente de creación de subcategorías. Utiliza el evento **CreateServiciosSubcategoria** y redirige al componente `servicios.subcategorias.create-servicios-subcategorias`.

### `columns()`
Este método define las columnas que se muestran en la tabla. Las columnas son:
- **Servicio**: Muestra el nombre del servicio y permite la búsqueda y ordenación.
- **Subcategoría**: Muestra el nombre de la subcategoría.
- **Categoría**: Muestra el nombre de la categoría correspondiente.
- **Tipos**: Muestra el nombre del tipo de servicio.
- **Requiere Fotos**: Indica si la carga inicial requiere fotos.

### `filters()`
Este método define los filtros disponibles para la tabla. Los filtros son:
- **Tipo**: Permite filtrar por tipo de servicio.
- **Categoría**: Permite filtrar por categoría de servicio.

### `getTipos()`
Este método devuelve una lista de todos los tipos de servicio que tienen subcategorías asociadas. Utiliza el modelo **ServiciosSubcategoriasModel** para obtener los datos.

### `getCategorias()`
Este método devuelve una lista de todas las categorías que tienen subcategorías asociadas. Utiliza el modelo **ServiciosSubcategoriasModel** para obtener los datos.

## Vista del Componente
El componente **ServiciosSubcategorias** está diseñado para ser utilizado junto con una vista que permite la administración de las subcategorías de servicios. La vista se basa en la tabla interactiva generada por Livewire.

