**Documentación del Componente CrearTiposUbicaciones**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearTiposUbicaciones` se utiliza para crear nuevos tipos de ubicaciones en la aplicación. Proporciona una interfaz que permite al usuario agregar un nuevo tipo de ubicación con un nombre. El componente incluye validaciones y gestión de permisos para garantizar la integridad de los datos y que solo los usuarios autorizados puedan realizar esta acción.

## Dependencias
### Modelos
- **TiposUbicacionesModel**: Modelo que representa los tipos de ubicaciones en la base de datos.

### Servicios
- **MiddlewareInvoker**: Servicio utilizado para verificar los permisos del usuario antes de realizar ciertas acciones.

### Traits
- **VerificacionTrait**: Trait personalizado utilizado para verificar los cambios en los valores antes de realizar la creación de registros.
- **SortableTrait**: Trait que permite agregar funcionalidad de ordenación en los componentes.

## Propiedades
- **open** (bool): Indica si el modal de creación está abierto.
- **nombre** (string): Representa el nombre del nuevo tipo de ubicación que se desea crear.

## Métodos
### crearTipoUbicacion()
Este método se utiliza para abrir el modal de creación. Verifica los permisos del usuario antes de permitir la apertura del modal. Si el usuario no tiene permisos, se emite un evento de "no-permission".

### updated($propertyName)
Valida la propiedad que fue actualizada para asegurarse de que cumple con las reglas definidas.

### save()
Valida los datos proporcionados y luego llama al método **crearRegistro()** para crear un nuevo tipo de ubicación. Finalmente, cierra el modal.

### crearRegistro()
Este método crea un nuevo registro de tipo de ubicación utilizando el modelo `TiposUbicacionesModel`. Se aseguran los valores proporcionados mediante el trait `VerificacionTrait`.

### close()
Cierra el modal y restablece las propiedades, incluyendo el valor de **nombre**. Además, emite un evento para refrescar la tabla relacionada si es necesario.

### render()
Renderiza la vista `livewire.ubicaciones.crear-tipos-ubicaciones`, que contiene el modal con el formulario para agregar un nuevo tipo de ubicación.

## Vista del Componente
La vista del componente está representada por un modal que incluye los siguientes elementos:
- **Campo de Nombre**: Un campo de entrada de texto para ingresar el nombre del nuevo tipo de ubicación. Este campo es obligatorio y tiene una validación de máximo 30 caracteres.
- **Botones de Acción**: El modal incluye dos botones:
  - **Cancelar**: Cierra el modal sin guardar los cambios.
  - **Crear Nuevo Tipos de Ubicaciones**: Guarda el nuevo tipo de ubicación en la base de datos si los datos proporcionados son válidos.

El modal proporciona una interfaz sencilla y eficiente para crear nuevos tipos de ubicaciones, asegurando que los datos ingresados sean consistentes y se respeten las reglas de validación establecidas.

