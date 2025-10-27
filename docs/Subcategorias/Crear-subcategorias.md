**Documentación del Componente CreateSubcategoria**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General

El componente `CreateSubCategoria` es un componente de Laravel Livewire que permite la creación de nuevas subcategorías dentro de la aplicación. Este componente proporciona una interfaz para que el usuario ingrese los datos necesarios, que incluyen información general de la subcategoría, su categoría y tipo relacionados, y una imagen asociada. Además, el componente se encarga de validar los datos, subir imágenes y manejar la lógica de selección y filtrado en los dropdowns de tipo y categoría.

## Dependencias

El componente `CreateSubCategoria` depende de las siguientes clases y servicios:
- **MiddlewareInvoker**: Verifica los permisos del usuario para realizar acciones específicas.
- **VerificacionTrait**: Proporciona funcionalidades adicionales para verificaciones.
- **Livewire\WithFileUploads**: Utilizado para manejar la subida de imágenes.
- **Modelos Utilizados**:
  - `SubcategoriaModel`
  - `CategoriaModel`
  - `TiposModel`

## Propiedades

- **$open**: Booleano que indica si el modal está abierto o cerrado.
- **$sigla**: Sigla de la subcategoría, campo requerido con máximo 10 caracteres.
- **$id_tipo**: Identificador del tipo asociado a la subcategoría.
- **$nombre**: Nombre de la subcategoría, requerido con un máximo de 50 caracteres.
- **$descripcion**: Descripción de la subcategoría, requerida con un máximo de 100 caracteres.
- **$id_categoria**: Identificador de la categoría asociada a la subcategoría.
- **$movil_o_fijo**: Define si la subcategoría es móvil o fija.
- **$se_relaciona**: Indica si la subcategoría tiene relación con otra.
- **$imagen**: Imagen asociada a la subcategoría, subida a través del componente.
- **$selectedTipoNombre** y **$selectedCategoriaNombre**: Almacenan los nombres de los tipos y categorías seleccionados.
- **$tipoPrueba**: Contiene la lista de tipos cargados para ser seleccionados.
- **$categorias2**: Lista de categorías cargadas según el tipo seleccionado.

## Métodos

- **save()**: Valida los datos del formulario y llama al método `registro()` para crear la subcategoría.
- **registro()**:
  - Guarda la imagen en el almacenamiento público y registra la nueva subcategoría en la base de datos.
  - Emite el evento `refreshLivewireTable` para actualizar la vista.
  - Cierra el modal al finalizar.
- **setTipo($id)**: Asigna el tipo seleccionado y carga las categorías relacionadas con ese tipo.
- **cargarCategoria($id)**: Carga las categorías correspondientes al tipo seleccionado, o resetea la selección si no hay un tipo activo.
- **updatedSearchTipo()**: Filtra los tipos basándose en el término de búsqueda proporcionado.
- **setCategoria($id)**: Asigna la categoría seleccionada y actualiza la propiedad `selectedCategoriaNombre`.
- **updatedSearchCategoria()**: Filtra las categorías que pertenecen al tipo seleccionado según el término ingresado.
- **crearSubcategoria()**: Verifica los permisos del usuario para crear una nueva subcategoría y abre el modal si corresponde.
- **close()**: Resetea todas las propiedades del componente y cierra el modal.
- **removeImagen()**: Elimina la imagen actualmente seleccionada.

## Vista del Componente

La vista del componente consiste en un modal que contiene un formulario para ingresar los datos de la subcategoría. El modal se abre y cierra mediante la propiedad `$open`, la cual se actualiza a través de los eventos y acciones del componente.

### Elementos Principales de la Vista

- **Dropdown de Tipo y Categoría**: Dropdowns personalizados que permiten al usuario buscar y seleccionar un tipo y categoría específicos.
- **Formulario de Datos**: Contiene los campos necesarios como `Nombre`, `Sigla`, `Descripción`, `Movil o Fijo`, `Se Relaciona` y la opción para subir una `Imagen`.
- **Carga de Imágenes**: El formulario permite cargar una imagen relacionada con la subcategoría, con una vista previa antes de confirmar la selección.
- **Botones de Acción**: Incluye un botón para cancelar y cerrar el modal, y otro para confirmar la creación de la nueva subcategoría. Durante la carga, un indicador de "Cargando..." se muestra para mejorar la experiencia del usuario.

El modal ofrece una experiencia de usuario mejorada con transiciones suaves, mensajes de error claros y la capacidad de eliminar la imagen seleccionada antes de guardar.

