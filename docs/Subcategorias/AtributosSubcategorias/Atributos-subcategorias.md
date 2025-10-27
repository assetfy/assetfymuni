**Documentación del Componente AtributosSubcategorias**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas](#columnas)
- [Filtros](#filtros)

## Descripción General
El componente **AtributosSubcategorias** es una tabla interactiva que permite gestionar los atributos de subcategorías. Utiliza **LivewireTable** para proporcionar funcionalidades como ordenamiento, búsqueda, y filtrado. Además, permite la creación de nuevos atributos asociados a las subcategorías.

## Dependencias
### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Utilizado para crear una tabla interactiva con funcionalidades avanzadas.
- **RamonRietdijk\LivewireTables\Filters\SelectFilter**: Utilizado para agregar un filtro selectivo a la tabla.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **App\Models\AtributosSubcategoriaModel**: Modelo utilizado para representar y trabajar con los datos de los atributos de las subcategorías.
- **Illuminate\Database\Eloquent\Model**: Proporciona las capacidades para interactuar con los modelos de Eloquent.

## Propiedades
- **\$model**: Define el modelo principal que la tabla utilizará, en este caso **AtributosSubcategoriaModel**.
- **\$title**: Título del encabezado de la tabla. Se define como **Atributos Subcategorias**.
- **\$createForm**: Define el componente de creación predeterminado, llamado **crearAtributoSubcategoria**.
- **\$useSelection**: Define si se permite la selección de filas en la tabla. En este caso, está deshabilitado.

## Métodos

### `crearAtributoSubcategoria()`
Este método se utiliza para abrir el componente de creación de un nuevo atributo asociado a una subcategoría. Emite un evento llamado **crearAtributoSubcategoria** para redirigir al componente correspondiente.

## Columnas
El componente **AtributosSubcategorias** tiene las siguientes columnas:

- **Atributo**: Esta columna muestra el nombre del atributo asociado a la subcategoría. Es **ordenable** y **buscable**, y utiliza el alias para la calificación.
- **Obligatorio Carga Inicial**: Indica si el atributo es obligatorio al cargar inicialmente la subcategoría.
- **Unico**: Indica si el atributo debe ser único.
- **Acciones**: Esta columna incluye un botón para editar el atributo seleccionado. Utiliza un código HTML para generar el botón que emite un evento **atributoSubcategoria** con el ID del atributo seleccionado.

## Filtros
El componente **AtributosSubcategorias** tiene los siguientes filtros:

### `SelectFilter: Subcategoria`
Este filtro permite filtrar los registros por subcategoría. Utiliza el método **getSubcategoria()** para obtener las opciones disponibles. Este método consulta el modelo **AtributosSubcategoriaModel** y devuelve las subcategorías con sus nombres e IDs como opciones.

## Método `getSubcategoria()`
Obtiene las subcategorías disponibles que tienen atributos asociados. Retorna un arreglo con los nombres de las subcategorías y sus IDs para ser utilizados en el filtro selectivo de la tabla.

