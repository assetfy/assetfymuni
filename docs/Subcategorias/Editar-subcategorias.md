**Documentación del Componente EditSubcategoria**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditSubcategoria` se utiliza para editar los detalles de una subcategoría existente, incluyendo la imagen, nombre, descripción y otras propiedades. Este componente ofrece una interfaz para que el usuario pueda actualizar la información de la subcategoría de manera interactiva, a través de un modal en una aplicación basada en Livewire y Laravel.

## Dependencias
### Modelos
- **SubcategoriaModel**: Modelo que representa la subcategoría a editar.
- **CategoriaModel**: Modelo que representa la categoría asociada a la subcategoría.
- **TiposModel**: Modelo que representa el tipo asociado a la subcategoría.

### Servicios
- **MiddlewareInvoker**: Servicio que verifica si el usuario tiene permisos para realizar acciones sobre la subcategoría.

### Traits
- **WithFileUploads**: Trait de Livewire para permitir la carga de archivos.
- **VerificacionTrait**: Trait personalizado para la verificación de cambios en los valores.

## Propiedades
- **open** (bool): Indica si el modal de edición está abierto.
- **categoria**: Datos de la categoría asociada a la subcategoría.
- **id_categoria**, **id_tipo**: Identificadores de la categoría y el tipo relacionados con la subcategoría.
- **updatedSigla**, **updatedNombre**, **updatedDescripcion**: Propiedades que representan los valores editados de la subcategoría.
- **updatedMovilofijo**, **updatedRelacion**: Valores editados para los campos "Móvil o Fijo" y "Se Relaciona".
- **categorias**, **tipoPruebas**: Categorías y tipos disponibles para la selección.
- **tipos**, **rutaFoto**, **updatedImagen**: Almacenan información relacionada con los tipos y la imagen actualizada.
- **subcategoria**: Instancia del modelo `SubcategoriaModel` que se va a editar.

## Métodos
### openModalSubcategoria($data)
Verifica los permisos del usuario antes de abrir el modal de edición de la subcategoría.

### openModal($data)
Carga los datos de la subcategoría a editar y abre el modal.

### actualizarSubcategoria()
Método disparado mediante un evento de Livewire para actualizar los datos de la subcategoría.

### actualizar()
Valida y guarda los cambios realizados en la subcategoría. Si se actualiza la imagen, también gestiona la eliminación de la imagen anterior.

### close()
Cierra el modal de edición y restablece la propiedad **open** a `false`.

### render()
Renderiza la vista `livewire.subcategoria.edit-subcategoria` para mostrar el modal y la información del componente.

## Vista del Componente
La vista del componente se representa a través de un modal que incluye los siguientes elementos:
- **Tipo y Categoría**: Se muestran los datos del tipo y la categoría asociados con la subcategoría.
- **Campos de Edición**: Campos como el nombre, sigla, descripción, "Móvil o Fijo" y "Se Relaciona", permiten al usuario modificar los valores actuales.
- **Imagen**: El usuario puede cargar una nueva imagen, y se muestra una vista previa de la imagen actual o de la nueva imagen seleccionada.
- **Botones**: El modal incluye botones para cancelar la edición o para guardar los cambios realizados.

El modal proporciona una interfaz clara para editar todos los aspectos relevantes de la subcategoría, asegurando que el usuario pueda ver el estado actual de los datos y realizar cambios de manera eficiente.

