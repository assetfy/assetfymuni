# Documentación del Componente Livewire: EditarNuevosAtributos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EditarNuevosAtributos` permite a los usuarios editar los atributos de un activo específico en el sistema. Proporciona una interfaz para modificar los valores de los atributos seleccionados, incluyendo la actualización de los campos de texto y los valores numéricos asociados al atributo.

## Propiedades

- **open**: Estado del modal para abrir o cerrar la vista de edición de atributos. Valor inicial: **false**.
- **update_campo, update_numerico**: Valores que almacenan la información que será actualizada para el campo de texto y el valor numérico del atributo.
- **resultado**: Resultado de la búsqueda para determinar si un atributo puede ser editado o no.
- **atributo**: Objeto del modelo `ActivosAtributosModel` que representa el atributo que se va a editar.
- **id_atributo, id_activo, id_tipo_activo, id_categoria_activo, id_subcategoria_activo**: Identificadores utilizados para manejar la información del activo y del atributo durante la edición.
- **atributoActivo**: Identificador del atributo activo que se está editando.

## Métodos

### mount(ActivosAtributosModel $atributo)
Este método inicializa las propiedades del componente con la información del atributo que se va a editar. Establece los valores actuales de los campos que se podrán modificar, tales como `campo` y `campo_numerico`.

### actualizarNuevoAtributo()
Este método valida los datos ingresados por el usuario y luego actualiza la información del atributo específico. Utiliza el modelo `ActivosAtributosModel` para localizar el atributo asociado al activo y lo actualiza con los nuevos valores proporcionados por el usuario.

### cerrarModal()
Cierra el modal de edición de atributos, cambiando el valor de la propiedad **open** a `false`.

### render()
Renderiza la vista del componente y proporciona los datos necesarios, como los atributos, subcategorías y activos que serán utilizados en la interfaz del usuario.

## Vista y Controlador
El controlador principal del componente es la clase `EditarNuevosAtributos`, que maneja la lógica para la actualización de atributos asociados a un activo específico. La vista proporciona un modal con los siguientes elementos:

- **Campos para Edición**: Incluye campos de texto para modificar el valor del atributo y el valor numérico asociado.
- **Botones de Acción**: Se incluyen botones para actualizar los atributos o cancelar la operación.
- **Validación del Atributo**: Muestra un mensaje si el atributo no puede ser editado debido a restricciones específicas.

## Dependencias
El componente `EditarNuevosAtributos` depende de los siguientes modelos y elementos:

- **ActivosAtributosModel**: Modelo principal que maneja la información de los atributos asociados a los activos.
- **AtributosSubcategoriaModel**: Modelo que relaciona los atributos con las subcategorías y categorías.
- **AtributosModel**: Modelo que gestiona la información de los atributos disponibles en el sistema.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.

Este componente permite a los usuarios realizar cambios en los atributos previamente asociados a un activo, brindando flexibilidad para actualizar la información cuando sea necesario y asegurando que los cambios sean consistentes y validados antes de ser aplicados.

