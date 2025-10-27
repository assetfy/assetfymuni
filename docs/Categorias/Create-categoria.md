# Documentación del Componente Livewire: CreateCategoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CreateCategoria` permite la creación de nuevas categorías dentro del sistema de gestión de activos. Proporciona una interfaz para ingresar información detallada sobre la categoría, como su tipo, sigla, nombre, descripción e imagen. Este componente facilita la administración de categorías de forma eficiente mediante un formulario interactivo.

## Propiedades

- **open**: Indica si el modal de creación de categoría está abierto o cerrado. Valor inicial: **false**.
- **sigla**: Almacena la sigla de la categoría.
- **id_tipo**: Almacena el identificador del tipo de categoría seleccionada.
- **nombre**: Almacena el nombre de la categoría.
- **descripcion**: Almacena la descripción de la categoría.
- **imagen**: Almacena la imagen subida para la categoría.
- **tipoPrueba**: Contiene la información de los tipos disponibles para seleccionar, dependiendo del resultado de la búsqueda.
- **searchTipo**: Almacena el valor del filtro de búsqueda para los tipos.
- **selectedTipoNombre**: Almacena el nombre del tipo de categoría seleccionado.
- **currentPage**: Indica la página actual utilizada para la paginación.
- **perPage**: Indica el número de registros por página para la paginación.
- **hasMorePages**: Indica si existen más páginas disponibles para cargar.

## Métodos

### crearCategoria()
Este método se activa cuando se desea crear una nueva categoría. Envía un evento llamado `crearCategoria` que abre el formulario para agregar una nueva categoría si el usuario tiene permisos.

### save()
Este método se utiliza para guardar una nueva categoría. Valida los campos requeridos y luego llama al método `registro()` para almacenar la categoría en la base de datos.

### registro()
Este método realiza el almacenamiento de la categoría en la base de datos utilizando el modelo `CategoriaModel`. Además, se encarga de subir la imagen seleccionada y guardarla en el almacenamiento público.

### iniciarBusqueda()
Este método reinicia la paginación y ejecuta la búsqueda de los tipos de categoría según el valor ingresado en el campo de búsqueda.

### fetchTipos()
Este método obtiene los tipos de categoría disponibles. Utiliza la caché si el número de tipos es menor o igual a 500. Si el número de registros es mayor, consulta directamente a la base de datos con filtros y paginación manual.

### loadMore()
Este método permite cargar más tipos de categoría cuando existen más páginas disponibles.

### resetPagination()
Este método restablece los valores de la paginación a sus valores iniciales.

### setTipo($id)
Este método establece el tipo de categoría seleccionado y actualiza el nombre correspondiente. Luego, reinicia la paginación y actualiza la lista de tipos.

### close()
Este método cierra el modal de creación de categoría y restablece los campos del formulario.

## Vista y Controlador
El componente tiene un formulario modal que se muestra cuando la propiedad **open** es verdadera. La vista incluye campos para:
- **Sigla**: Un campo de texto que permite al usuario ingresar la sigla de la categoría.
- **Tipo**: Un dropdown con opción de búsqueda para seleccionar el tipo de la categoría.
- **Nombre**: Un campo de texto para ingresar el nombre de la categoría.
- **Descripción**: Un campo de texto para ingresar una breve descripción.
- **Imagen**: Un campo para cargar una imagen con vista previa.
- **Botones de Acción**: Incluir botones para **Cancelar** y **Crear Nueva Categoría**.

## Dependencias
El componente `CreateCategoria` depende de los siguientes modelos y elementos:

- **CategoriaModel**: Modelo que maneja la información de las categorías en la base de datos.
- **TiposModel**: Modelo que almacena los diferentes tipos disponibles para una categoría.
- **UnidadModel**: Modelo que gestiona las unidades de medida relacionadas con los tipos de campo.
- **Livewire**: Utiliza Livewire para manejar la interacción con la vista de forma reactiva.
- **WithFileUploads**: Trait que permite la subida de archivos, utilizado para cargar la imagen de la categoría.
- **VerificacionTrait**: Trait utilizado para verificar y validar datos en el componente.

