# Documentación del Componente Livewire: EditarAtributos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EditarAtributos` permite editar atributos existentes en el sistema de gestión de activos. El usuario puede modificar el nombre del atributo, el tipo de campo, la unidad de medida asociada y la descripción del atributo mediante un modal que se muestra cuando se edita un atributo específico.

## Propiedades

- **open**: Estado del modal para abrir o cerrar la vista de edición de atributos. Valor inicial: **false**.
- **atributo**: Objeto del modelo `AtributosModel` que representa el atributo a editar.
- **updateTipo**: Tipo de campo actualizado para el atributo (Ej: numérico, texto).
- **updatedMedida**: Unidad de medida actualizada asociada al atributo.
- **updatedNombre**: Nombre actualizado del atributo.
- **updatedDescripcion**: Descripción actualizada del atributo.
- **unidad2**: Lista de unidades de medida disponibles según el tipo de campo seleccionado.
- **atributos**: Almacena los datos del atributo que se está editando.

## Métodos

### mount(AtributosModel $value)
Este método se utiliza para inicializar las propiedades del componente con la información del atributo que se va a editar. Se establecen los valores actuales para el tipo de campo, el nombre, la descripción y la unidad de medida del atributo.

### openEditAtributos($data)
Este método se activa mediante un listener para abrir el modal de edición. Antes de abrir el modal, verifica si el usuario tiene permisos para editar el atributo utilizando `MiddlewareInvoker`.

### openModal($value)
Este método busca el atributo en la base de datos y, si lo encuentra, inicializa las propiedades mediante el método `mount()` y abre el modal.

### actualizar()
Método anotado con `#[On('guardado')]` que se encarga de actualizar el atributo luego de que se han guardado los cambios.

### actualizarAtributo()
Este método valida los datos ingresados por el usuario y luego actualiza el atributo correspondiente en la base de datos utilizando el modelo `AtributosModel`. Posteriormente, se dispara el evento `refreshLivewireTable` para actualizar la tabla.

### TipoId($value)
Este método carga las unidades de medida disponibles para un tipo de campo seleccionado. Si el tipo de campo es igual a `1`, se cargan las unidades de medida desde el modelo `UnidadModel`.

### close()
Este método reinicia las propiedades del componente y cierra el modal de edición de atributos.

### render()
Renderiza la vista del componente y proporciona los datos necesarios, como los tipos de campos disponibles, los atributos y las unidades de medida, para la interfaz del usuario.

## Vista y Controlador
El controlador principal del componente es la clase `EditarAtributos`, que maneja la lógica para la edición de atributos en el sistema. La vista proporciona un modal con los siguientes elementos:

- **Campos para Edición**: Incluye campos de texto para editar el nombre y la descripción del atributo, así como selectores para actualizar el tipo de campo y la unidad de medida.
- **Botones de Acción**: Se incluyen botones para actualizar el atributo o cancelar la operación.
- **Validación Dinámica**: Los campos se validan dinámicamente a medida que el usuario los modifica.

## Dependencias
El componente `EditarAtributos` depende de los siguientes modelos y elementos:

- **AtributosModel**: Modelo principal que maneja la información de los atributos en el sistema.
- **TiposCamposModel**: Modelo que proporciona los diferentes tipos de campos que puede tener un atributo.
- **UnidadModel**: Modelo que maneja las unidades de medida asociadas a los atributos.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.
- **MiddlewareInvoker**: Servicio utilizado para verificar los permisos del usuario antes de permitir la edición de un atributo.

Este componente es esencial para gestionar la actualización de los atributos, permitiendo modificar y adaptar la información de los activos según los cambios requeridos por los usuarios y asegurando la consistencia de los datos.