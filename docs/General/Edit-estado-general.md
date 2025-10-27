**Documentación del componente EditEstadoGeneral**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `EditEstadoGeneral` es un componente de Livewire utilizado para editar el estado general de un elemento existente. Permite al usuario modificar las propiedades de un estado general, como su nombre y descripción, mediante una interfaz modal. Este componente está diseñado para facilitar la actualización de la información de los estados generales existentes.

### Propiedades
- **$open**: Indica si el modal está abierto o cerrado.
- **$estado**: Instancia del modelo `EstadoGeneralModel` que se está editando.
- **$updatedNombre**: Almacena el valor actualizado del nombre del estado general.
- **$updatedDescripcion**: Almacena el valor actualizado de la descripción del estado general.
- **$estados**: Contiene la instancia del modelo que se encuentra en edición.

### Listeners
- **EditarEstadoGeneral**: Escucha el evento `EditarEstadoGeneral` para iniciar el proceso de edición.

### Métodos
- **mount(EstadoGeneralModel $value)**: Inicializa las propiedades del componente con los datos del estado general que se va a editar.

- **EditarEstadoGeneral($value)**: Valida los permisos del usuario y, si tiene permisos, llama al método `openModal($value)`.

- **openModal($value)**: Busca el estado general correspondiente al valor proporcionado y lo asigna al componente. Abre el modal de edición si el estado es encontrado.

- **guardarCambios()**: Despacha el evento `check` para realizar verificaciones adicionales antes de guardar los cambios.

- **actualizar()**: Valida los datos ingresados y guarda los cambios en el estado general, despachando el evento `lucky` para indicar que la actualización fue exitosa.

- **actualizarGeneral()**: Valida los datos del formulario y guarda los cambios en el modelo `EstadoGeneralModel`. Luego, cierra el modal y actualiza la tabla de Livewire.

- **render()**: Renderiza la vista del componente `edit-estado-general`.

### Vista del Componente
La vista asociada al componente `EditEstadoGeneral` incluye un modal para editar los estados generales. El modal contiene los siguientes elementos:

- **Nombre**: Un campo desplegable que permite seleccionar el nombre del estado general. Las opciones incluyen "Baja", "Service", y "Normal/Funcionando".

- **Descripción**: Un campo de texto para ingresar o modificar la descripción del estado general.

- **Botones de Acción**:
  - **Cancelar**: Cierra el modal sin realizar cambios.
  - **Actualizar**: Guarda los cambios realizados y actualiza el estado general.
  - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesan los cambios.

