**Documentación del Componente CreateServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **CreateServicios** permite a los usuarios crear nuevos registros de servicios en la aplicación. Mediante un formulario modal, el usuario puede ingresar un nombre y una descripción para el servicio, validar estos campos, y luego guardarlos en la base de datos.

## Dependencias
### Namespaces y Paquetes Utilizados
- **App\Services\MiddlewareInvoker**: Utilizado para verificar los permisos antes de abrir el modal de creación.
- **App\Traits\VerificacionTrait**: Trait que permite reutilizar la funcionalidad de verificación de permisos.
- **App\Models\ServiciosModel**: Modelo utilizado para representar y almacenar los datos de los servicios.
- **Livewire\Component**: Proporciona la funcionalidad de Livewire para construir componentes interactivos en tiempo real.

## Propiedades
- **$listeners**: Define los eventos que el componente escucha. En este caso, el evento **crearServicios**.
- **$servicios**: Almacena los datos de los servicios, aunque no se utiliza directamente en este componente.
- **$nombre**: Propiedad que representa el nombre del servicio que se está creando.
- **$descripcion**: Propiedad que representa la descripción del servicio que se está creando.
- **$open**: Define si el modal está abierto o cerrado.

## Métodos

### `updated($propertyName)`
Valida una propiedad específica en cuanto se actualiza, utilizando la regla de validación correspondiente.

### `save()`
Valida los campos del formulario y luego llama al método **crearRegistro()** para guardar el servicio. Finalmente, cierra el modal con el método **close()**.

### `crearRegistro()`
Crea un nuevo registro en la base de datos utilizando los valores de **$nombre** y **$descripcion**. Se usa el modelo **ServiciosModel** y los campos se crean con los valores nuevos proporcionados.

### `close()`
Cierra el modal y restablece los valores de **$nombre** y **$descripcion**. Además, emite un evento para refrescar cualquier tabla de Livewire en la vista.

### `render()`
Renderiza la vista correspondiente a este componente, ubicada en **livewire.servicios.create-servicios**.

### `crearServicios()`
Llama al método **openModal()** para abrir el modal de creación.

### `openModal()`
Verifica si el usuario tiene los permisos necesarios para crear un servicio mediante **MiddlewareInvoker::checkPermisosRoles()**. Si tiene los permisos, abre el modal.

## Vista del Componente
El componente utiliza un modal de **Livewire** para recolectar los datos del servicio. La vista incluye los siguientes elementos:

- **Campo de Nombre del Servicio**: Campo de texto para ingresar el nombre del servicio, con validación en tiempo real.
- **Campo de Descripción**: Campo de texto para ingresar la descripción del servicio, también con validación en tiempo real.
- **Botones de Acción**:
  - **Cancelar**: Cierra el modal sin guardar los cambios.
  - **Crear Servicio**: Guarda los datos del servicio si la validación es exitosa.
- **Indicador de Carga**: Se muestra mientras se está guardando el servicio.

Este modal se utiliza para capturar los detalles del nuevo servicio y brindarle al usuario la capacidad de crear servicios fácilmente desde la interfaz de usuario.

