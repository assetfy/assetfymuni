**Documentación del Componente CreateServiciosSubcategorias**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General

El componente **CreateServiciosSubcategorias** se encarga de la creación de relaciones entre servicios y subcategorías dentro del sistema. Permite al usuario seleccionar un servicio, tipo, categoría y subcategoría, y definir si se requieren fotos en la carga inicial. Además, incluye búsquedas dinámicas para facilitar la selección de los elementos.

## Dependencias

### Namespaces y Paquetes Utilizados

- **App\Models\ServiciosSubcategoriasModel**: Modelo que representa la relación entre servicios y subcategorías.
- **App\Services\MiddlewareInvoker**: Utilizado para verificar permisos antes de abrir el modal.
- **App\Models\SubcategoriaModel**: Modelo que representa las subcategorías disponibles.
- **App\Traits\VerificacionTrait**: Trait para realizar verificaciones.
- **App\Models\CategoriaModel**: Modelo que representa las categorías de los servicios.
- **App\Models\ServiciosModel**: Modelo que contiene la información sobre los servicios.
- **App\Models\TiposModel**: Modelo que representa los diferentes tipos de servicios.
- **Livewire\Component**: Base del componente de Livewire.

## Propiedades

- **$open**: Indica si el modal está abierto o cerrado.
- **$unico**: Propiedad que indica si la relación es única.
- **$obligatorio_carga_ini**: Define si la carga inicial es obligatoria.
- **$id_servicio**: ID del servicio seleccionado.
- **$id_categoria**: ID de la categoría seleccionada.
- **$id_subcategoria**: ID de la subcategoría seleccionada.
- **$req_fotos_carga_inicial**: Define si se requieren fotos en la carga inicial.
- **$id_tipo**: ID del tipo seleccionado.
- **$categorias**: Colección de categorías disponibles.
- **$subcategorias**: Colección de subcategorías disponibles.
- **$servicios**: Colección de servicios disponibles.
- **$tipos**: Colección de tipos disponibles.
- **$searchServicio**: Cadena de búsqueda para filtrar servicios.
- **$searchTipo**: Cadena de búsqueda para filtrar tipos.
- **$searchCategoria**: Cadena de búsqueda para filtrar categorías.
- **$searchSubcategoria**: Cadena de búsqueda para filtrar subcategorías.

## Métodos

### `mount()`
Inicializa las colecciones de servicios, tipos, categorías y subcategorías.

### `save()`
Valida y guarda la nueva relación de servicio-subcategoría. Emite eventos para refrescar la tabla y cerrar el modal.

### `updated($propertyName)`
Valida una propiedad específica cuando se actualiza.

### `render()`
Renderiza la vista del componente **create-servicios-subcategorias**.

### `CreateServiciosSubcategoria()`
Verifica los permisos del usuario antes de abrir el modal. Si los permisos son válidos, resetea el formulario y abre el modal.

### `setServicio($id)`
Establece el servicio seleccionado y limpia el campo de búsqueda de servicios.

### `setTipo($id)`
Establece el tipo seleccionado, carga las categorías correspondientes y resetea las selecciones de categoría y subcategoría.

### `cargarCategorias($id)`
Carga las categorías relacionadas con el tipo seleccionado.

### `setCategoria($id)`
Establece la categoría seleccionada, carga las subcategorías correspondientes y limpia el campo de búsqueda de categorías.

### `cargarSubcategorias($id)`
Carga las subcategorías relacionadas con la categoría seleccionada.

### `updatedSearchServicio()`
Actualiza la colección de servicios según la búsqueda realizada.

### `updatedSearchTipo()`
Actualiza la colección de tipos según la búsqueda realizada.

### `updatedSearchCategoria()`
Actualiza la colección de categorías según la búsqueda realizada y el tipo seleccionado.

### `setSubcategoria($id)`
Establece la subcategoría seleccionada y limpia el campo de búsqueda de subcategorías.

### `updatedSearchSubcategoria()`
Actualiza la colección de subcategorías según la búsqueda realizada y la categoría seleccionada.

### `close()`
Cierra el modal y resetea el formulario.

### `resetForm()`
Resetea todas las propiedades del formulario a sus valores iniciales.

## Vista del Componente

El componente utiliza un modal **x-dialog-modal** para la creación de las relaciones entre servicios y subcategorías. En el modal se encuentran los siguientes elementos:

- **Dropdown de Servicio**: Permite seleccionar un servicio de la lista de servicios disponibles.
- **Dropdown de Tipo**: Permite seleccionar un tipo de la lista de tipos disponibles.
- **Dropdown de Categoría**: Permite seleccionar una categoría relacionada con el tipo seleccionado.
- **Dropdown de Subcategoría**: Permite seleccionar una subcategoría relacionada con la categoría seleccionada.
- **Campo Requiere Foto**: Permite definir si se requiere una foto en la carga inicial.

El modal incluye botones para **Cancelar** y **Crear Servicio Subcategoría**, así como un indicador de carga mientras se guarda la información.

