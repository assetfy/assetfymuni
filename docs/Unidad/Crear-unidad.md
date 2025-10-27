**Documentación del Componente CreateUnidad**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateUnidad` permite la creación de una nueva unidad de medida en la aplicación. Este componente ofrece una interfaz para que el usuario pueda registrar una unidad de manera interactiva a través de un modal.

## Dependencias
### Modelos
- **UnidadModel**: Modelo que representa la unidad de medida que se está creando.

### Servicios
- **MiddlewareInvoker**: Servicio que se utiliza para verificar si el usuario tiene permisos para realizar la acción de creación.

### Traits
- **VerificacionTrait**: Trait utilizado para verificar y validar los cambios en los valores del componente.

## Propiedades
- **nombre** (string): Almacena el nombre de la nueva unidad de medida.
- **unidad** (UnidadModel): Modelo que representa la unidad de medida que se está creando.
- **search** (string): Almacena el valor de búsqueda, si es necesario.
- **open** (bool): Indica si el modal de creación está abierto o cerrado.

## Métodos
### updated($propertyName)
Valida solo la propiedad que ha sido actualizada. Se usa para verificar los datos a medida que el usuario los ingresa.

### save()
Valida el formulario completo y registra la nueva unidad de medida llamando al método `registro()`.

### registro()
Crea una nueva instancia de `UnidadModel` con los datos proporcionados y emite un evento para actualizar la tabla de unidades de medida. También utiliza el `VerificacionTrait` para realizar la creación.

### close()
Cierra el modal de creación y restablece los valores de las propiedades a su estado inicial.

### crearUnidadMedida()
Verifica si el usuario tiene los permisos necesarios antes de abrir el modal de creación. Si el usuario no tiene permisos, emite un evento de "sin permiso" para notificar al usuario.

## Vista del Componente
La vista del componente se representa mediante un modal que incluye los siguientes elementos:

- **Campo Nombre**: Un campo de entrada para el nombre de la unidad de medida.
- **Botones del Modal**: El modal incluye botones para cancelar la creación o para guardar los datos ingresados.

La estructura del modal es clara y sencilla, permitiendo al usuario ingresar el nombre de la unidad de medida y confirmar la creación de la misma. Además, el modal muestra mensajes de error de validación cuando sea necesario para guiar al usuario durante el proceso de creación.

