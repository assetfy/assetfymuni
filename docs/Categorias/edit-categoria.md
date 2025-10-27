# Documentación del Componente Livewire: EditCategoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EditCategoria` permite editar una categoría existente dentro del sistema de gestión de activos. Proporciona una interfaz para actualizar información detallada sobre la categoría, como su sigla, nombre, descripción e imagen. Este componente facilita la gestión eficiente de las categorías ya registradas mediante un formulario interactivo que permite realizar modificaciones de forma rápida y segura.

## Propiedades

- **open**: Indica si el modal de edición de la categoría está abierto o cerrado. Valor inicial: **false**.
- **categoria**: Almacena la instancia de la categoría que se está editando.
- **updateTipo**: Almacena el tipo de categoría seleccionado para actualizar.
- **updatedSigla**: Almacena la sigla de la categoría que se está editando.
- **updatedNombre**: Almacena el nombre actualizado de la categoría.
- **updatedDescripcion**: Almacena la descripción actualizada de la categoría.
- **updatedImagenCategoria**: Almacena la imagen subida para la categoría editada.
- **imagen**: Almacena la imagen seleccionada para la vista previa.
- **rutaFoto**: Ruta de la imagen que se cargará en la base de datos.

## Métodos

### mount(CategoriaModel $value)
Este método se utiliza para inicializar el componente con la categoría que se va a editar. Asigna los valores actuales de la categoría a las propiedades correspondientes del componente.

### openModalCategoria($data)
Este método abre el modal de edición con la categoría especificada por el identificador recibido. Inicializa los campos del formulario con los valores de la categoría seleccionada.

### actualizarCategoria()
Este método se activa después de guardar cambios en la categoría. Llama al método `actualizar()` para realizar las actualizaciones necesarias en la base de datos.

### actualizar()
Este método realiza la actualización de la categoría en la base de datos utilizando el modelo `CategoriaModel`. También se encarga de actualizar la imagen si se seleccionó una nueva. Si existe una imagen anterior, esta se elimina del almacenamiento.

### close()
Este método cierra el modal de edición de la categoría.

## Vista y Controlador
El componente tiene un formulario modal que se muestra cuando la propiedad **open** es verdadera. La vista incluye campos para:

- **Tipo**: Un campo de visualización que muestra el tipo actual de la categoría.
- **Sigla**: Un campo de texto para editar la sigla de la categoría.
- **Nombre**: Un campo de texto para editar el nombre de la categoría.
- **Descripción**: Un campo de texto para editar la descripción de la categoría.
- **Imagen**: Un campo para cargar una imagen con vista previa. Permite ver la imagen actual y cambiarla si es necesario.
- **Botones de Acción**: Incluir botones para **Cancelar** y **Actualizar** la categoría.

## Dependencias
El componente `EditCategoria` depende de los siguientes modelos y elementos:

- **CategoriaModel**: Modelo que maneja la información de las categorías en la base de datos.
- **TiposModel**: Modelo que almacena los diferentes tipos disponibles para una categoría.
- **Livewire**: Utiliza Livewire para manejar la interacción con la vista de forma reactiva.
- **WithFileUploads**: Trait que permite la subida de archivos, utilizado para cargar la imagen de la categoría.
- **VerificacionTrait**: Trait utilizado para verificar y validar datos en el componente.

