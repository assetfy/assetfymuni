**Documentación del Componente EditTipos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditTipos` se utiliza para editar la información de un tipo existente en la aplicación, como la sigla, el nombre, la descripción y la imagen asociada. Este componente permite que los usuarios modifiquen los datos de manera interactiva y se actualice la base de datos mediante un modal proporcionado en la vista.

## Dependencias
Este componente depende de varias clases y funciones para funcionar correctamente. Las dependencias se dividen en las siguientes categorías:

### Modelos
- **TiposModel**: Se utiliza para manejar las operaciones CRUD sobre los tipos en la base de datos.

### Traits
- **VerificacionTrait**: Trait personalizado para verificar si se han realizado cambios en los valores y gestionar la actualización de los datos.
- **WithFileUploads**: Trait de Livewire que permite la carga de archivos, necesario para la subida de las imágenes asociadas al tipo.

## Propiedades
- **open** (bool): Indica si el modal de edición está abierto.
- **tipo**: Instancia del modelo `TiposModel` correspondiente al tipo que se va a editar.
- **updatedSigla**: Sigla actualizada del tipo.
- **updatedNombre**: Nombre actualizado del tipo.
- **updatedDescripcion**: Descripción actualizada del tipo.
- **tipos**: Almacena la información del tipo seleccionado.
- **rutaFoto**: Ruta de la nueva imagen cargada, si se ha actualizado.
- **updatedImagen**: Imagen nueva cargada por el usuario.
- **imagen**: Imagen actual asociada al tipo.

## Métodos
### mount(TiposModel $value)
Inicializa las propiedades del componente con los valores actuales del tipo que se va a editar.

### openEditTipo($data)
Busca el tipo correspondiente a los datos proporcionados y abre el modal de edición si el tipo existe.

### verificacion()
Este método se dispara mediante un evento Livewire para actualizar los datos del tipo.

### actualizar()
Valida los datos proporcionados por el usuario y realiza la actualización en la base de datos. Si se ha cargado una nueva imagen, gestiona la eliminación de la imagen anterior.

### close()
Cierra el modal de edición estableciendo la propiedad **open** a `false`.

## Vista del Componente
La vista del componente se presenta mediante un modal que incluye los siguientes elementos:
- **Sigla**: Campo de entrada para actualizar la sigla del tipo.
- **Nombre**: Campo de entrada para actualizar el nombre del tipo.
- **Descripción**: Campo de entrada para actualizar la descripción del tipo.
- **Imagen**: Permite al usuario cargar una nueva imagen. Se muestra una vista previa de la imagen actual o la nueva imagen cargada.

El modal incluye botones para cancelar la edición o guardar los cambios realizados. La interfaz está diseñada para proporcionar una experiencia de usuario clara y eficiente al editar los detalles de un tipo.

