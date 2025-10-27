**Documentación del Componente CreateAtributoSubcategoria**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **CreateAtributoSubcategoria** permite la creación de un nuevo atributo asociado a una subcategoría. A través de un formulario modal, los usuarios pueden especificar los detalles del atributo, como si es obligatorio, único, y la selección de tipo, categoría, y subcategoría.

## Dependencias
### Namespaces y Paquetes Utilizados
- **App\Models\AtributosSubcategoriaModel**: Modelo utilizado para la creación de atributos-subcategoría.
- **App\Services\MiddlewareInvoker**: Verifica permisos antes de permitir la creación del atributo.
- **App\Traits\VerificacionTrait**: Trait para validar los permisos del usuario.
- **App\Models\TiposModel, CategoriaModel, SubcategoriaModel, AtributosModel**: Modelos utilizados para cargar los datos relacionados a tipos, categorías, subcategorías y atributos.
- **Livewire\WithFileUploads**: Permite la carga de archivos en el componente (aunque no es utilizado directamente en este formulario).
- **Livewire\Component**: Proporciona la funcionalidad para construir componentes interactivos con Livewire.

## Propiedades
- **$open**: Define si el modal está abierto o cerrado.
- **$unico, $obligatorio_carga_ini, $obligatorio_carga_inicial**: Propiedades para determinar si el atributo es único y si es obligatorio durante la carga inicial.
- **$id_atributo, $id_tipo, $id_categoria, $id_subcategoria**: IDs de los elementos seleccionados durante la creación del atributo-subcategoría.
- **$searchTipo, $searchCategoria, $searchSubcategoria, $searchAtributo**: Propiedades para manejar la búsqueda en los dropdowns de selección.
- **$tiposCargados, $categoriasCargadas, $subcategoriasCargadas, $atributosCargados**: Almacenan los valores cargados para los selectores dependientes.
- **$selectedTipoNombre, $selectedCategoriaNombre, $selectedSubcategoriaNombre, $selectedAtributoNombre**: Almacenan los nombres seleccionados para mostrar en los dropdowns.

## Métodos
### `save()`
Valida los datos ingresados según las reglas establecidas y llama al método **registro()** para crear el nuevo atributo-subcategoría.

### `mount()`
Carga los tipos y atributos al montar el componente para que estén disponibles en los dropdowns.

### `registro()`
Crea el nuevo atributo-subcategoría en la base de datos y emite eventos para actualizar la tabla relacionada y cerrar el modal.

### `updated($propertyName)`
Valida una propiedad específica al ser actualizada.

### `setTipo($id)`
Establece el tipo seleccionado y carga las categorías relacionadas.

### `cargarCategorias($id)`
Carga las categorías que pertenecen al tipo seleccionado.

### `updatedSearchTipo()`
Actualiza los tipos cargados según el valor de búsqueda ingresado.

### `setCategoria($id)`
Establece la categoría seleccionada y carga las subcategorías relacionadas.

### `cargarSubcategorias($id)`
Carga las subcategorías que pertenecen a la categoría seleccionada.

### `updatedSearchCategoria()`
Actualiza las categorías cargadas según el valor de búsqueda ingresado.

### `updatedSearchAtributo()`
Actualiza los atributos cargados según el valor de búsqueda ingresado.

### `setAtributo($id)`
Establece el atributo seleccionado y cierra el dropdown correspondiente.

### `setSubcategoria($id)`
Establece la subcategoría seleccionada y cierra el dropdown correspondiente.

### `crearAtributoSubcategoria()`
Verifica si el usuario tiene los permisos necesarios para crear un atributo-subcategoría y abre el modal si es así.

### `close()`
Cierra el modal y resetea todas las propiedades para su reutilización.

### `updatedOpen($value)`
Recarga los tipos y atributos cuando el modal se abre.

### `render()`
Renderiza la vista del componente, ubicada en **livewire.subcategoria.create-atributo-subcategoria**.

## Modal del Componente
El componente utiliza un modal de **Livewire** para permitir la creación de un nuevo atributo-subcategoría. La vista incluye los siguientes elementos:

- **Dropdowns Dependientes**: Permiten seleccionar Tipo, Categoría, Subcategoría y Atributo, con opciones de búsqueda.
- **Campos de Selección**: Incluyen opciones para definir si el atributo es obligatorio o único.
- **Botones de Acción**:
  - **Cancelar**: Cierra el modal sin guardar los cambios.
  - **Crear Atributo-Subcategoría**: Guarda los datos del nuevo atributo-subcategoría si la validación es exitosa.
- **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la solicitud.

