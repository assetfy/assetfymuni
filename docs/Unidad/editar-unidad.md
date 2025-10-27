**Documentación del Componente EditUnidad**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditUnidad` se utiliza para editar los detalles de una unidad de medida existente. Permite al usuario modificar el nombre de la unidad mediante una interfaz de modal que aparece cuando se llama al evento correspondiente. El componente utiliza Livewire para gestionar el estado en tiempo real y facilitar la interacción sin recargar la página.

## Dependencias
Este componente depende de las siguientes librerías y servicios:
- **Livewire**: Para la interacción en tiempo real y la gestión del estado del componente.
- **VerificacionTrait**: Trait personalizado para verificar los cambios realizados en los valores del modelo.
- **UnidadModel**: Modelo que representa la entidad de Unidad de Medida en la base de datos.

## Propiedades
- **open** (bool): Indica si el modal de edición está abierto o cerrado.
- **unidad**: Instancia del modelo `UnidadModel` que se va a editar.
- **unidades**: Variable temporal que almacena la unidad a editar antes de asignarla a la propiedad `unidad`.
- **updatedNombre**: Contiene el nuevo nombre de la unidad que se va a actualizar.

## Métodos
### mount(UnidadModel \$value)
Inicializa las propiedades del componente con los valores de la unidad proporcionada.

### openModalUnidad(\$value)
Busca la unidad especificada por el ID y, si la encuentra, la asigna a la propiedad `unidad`. Luego, llama al método `actualizar()` y abre el modal.

### actualizarUnidad()
Método disparado mediante un evento de Livewire para actualizar los datos de la unidad.

### actualizar()
Valida y guarda los cambios realizados en la unidad. Utiliza el `VerificacionTrait` para verificar si se han hecho cambios y luego actualiza el registro en la base de datos.

### close()
Cierra el modal de edición y restablece la propiedad **open** a `false`.

### render()
Renderiza la vista `livewire.unidad.edit-unidad` para mostrar el modal y la información del componente.

## Vista del Componente
La vista del componente se representa a través de un modal que incluye los siguientes elementos:
- **Campo de Edición del Nombre**: Permite al usuario modificar el nombre de la unidad de medida. El campo está enlazado mediante `wire:model.defer` para actualizar su valor solo cuando el usuario lo confirme.
- **Botones**: El modal incluye botones para cancelar la edición o para guardar los cambios realizados.

El modal proporciona una interfaz sencilla y clara para editar la unidad de medida, asegurando que el usuario pueda ver el estado actual de los datos y realizar cambios de manera eficiente.

