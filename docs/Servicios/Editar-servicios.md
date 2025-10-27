**Documentación del Componente EditarServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **EditarServicios** permite editar los registros existentes de servicios en la aplicación. Los usuarios pueden modificar el nombre y la descripción de un servicio mediante un formulario en un modal. Este componente se asegura de que se respeten las reglas de validación antes de guardar los cambios.

## Dependencias
### Namespaces y Paquetes Utilizados
- **App\Models\ServiciosModel**: Modelo utilizado para representar y manipular los datos de los servicios.
- **Livewire\Component**: Proporciona la funcionalidad necesaria para construir el componente interactivo de Livewire.
- **Livewire\Attributes\On**: Atributo usado para especificar acciones que deben ejecutarse al recibir ciertos eventos.

## Propiedades
- **$nombre**: Propiedad que representa el nombre del servicio.
- **$descripcion**: Propiedad que representa la descripción del servicio.
- **$servicio**: Almacena el modelo del servicio que está siendo editado.
- **$updatedNombre**: Almacena el nuevo valor del nombre del servicio.
- **$updatedDescripcion**: Almacena el nuevo valor de la descripción del servicio.
- **$open**: Define si el modal está abierto o cerrado.
- **$servicios**: Almacena el servicio que se busca editar.

## Métodos

### `mount(ServiciosModel $value)`
Inicializa el componente con el modelo del servicio que se va a editar y asigna los valores actuales del servicio a las propiedades **$updatedNombre** y **$updatedDescripcion**.

### `updated($propertyName)`
Valida una propiedad específica en cuanto se actualiza, utilizando las reglas de validación correspondientes.

### `openModal($data)`
Busca el servicio utilizando el identificador proporcionado. Si se encuentra el servicio, llama al método **mount()** para inicializar el componente y luego abre el modal.

### `guardarCambios()`
Llama al evento **check**, el cual realiza verificaciones antes de proceder a actualizar los datos.

### `actualizar()`
Este método se llama mediante un evento anotado con **#[On('guardado')]** y actualiza los valores del servicio con los nuevos datos proporcionados.

### `actualizarServicios()`
Valida y guarda los nuevos valores del servicio en la base de datos, luego cierra el modal.

### `close()`
Cierra el modal sin guardar cambios adicionales.

### `render()`
Renderiza la vista correspondiente al componente, ubicada en **livewire.servicios.editar-servicios**.

## Vista del Componente
El componente **EditarServicios** utiliza un modal para permitir a los usuarios actualizar los valores de un servicio ya existente. La vista contiene los siguientes elementos:

- **Campo de Nombre del Servicio**: Campo de texto para editar el nombre del servicio con validación.
- **Campo de Descripción**: Campo de texto para editar la descripción del servicio con validación.
- **Botones de Acción**:
  - **Cancelar**: Cierra el modal sin guardar los cambios.
  - **Actualizar**: Guarda los cambios realizados al servicio si la validación es exitosa.

El modal facilita la edición de los servicios y permite a los usuarios gestionar la información de manera eficiente.

