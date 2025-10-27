# Documentación del Componente Livewire: CreateEstadosAltas

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CrearEstadosAltas` permite crear nuevos estados de alta en el sistema. Proporciona un formulario para ingresar el nombre y la descripción del estado de alta. Este componente está implementado, pero su uso depende de permisos específicos para poder realizar la acción.

## Propiedades

- **open**: Define si el modal de creación está abierto o cerrado. Valor inicial: **false**.
- **nombre**: Almacena el nombre del estado de alta que se va a crear.
- **descripcion**: Almacena la descripción del estado de alta que se va a crear.

## Métodos

### CreateEstadoAltas()
Este método verifica si el usuario tiene permisos para realizar la acción mediante `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene permisos, abre el modal para crear un nuevo estado de alta estableciendo **open** en **true**.

### save()
Este método valida los campos de entrada y luego guarda un nuevo estado de alta en la base de datos usando el modelo `EstadosAltasModel`. Finalmente, cierra el modal y resetea los valores de **nombre** y **descripcion**.

### updated($propertyName)
Este método se ejecuta cuando se actualiza una propiedad y valida únicamente esa propiedad específica, ayudando a proporcionar retroalimentación inmediata al usuario.

### close()
Este método cierra el modal y resetea las propiedades **nombre** y **descripcion**.

### render()
Este método devuelve la vista asociada al componente para mostrar el formulario de creación del estado de alta.

## Vista y Controlador
El controlador principal del componente es la clase `CrearEstadosAltas`, que maneja la lógica de creación de los estados de alta, asegurándose de que el usuario tenga los permisos necesarios para realizar la acción. La vista asociada muestra un formulario con campos para el nombre y la descripción del estado de alta, y permite guardar los datos o cancelar la operación.

## Dependencias

El componente `CrearEstadosAltas` depende de los siguientes elementos:

- **EstadosAltasModel**: Modelo que maneja los datos de los estados de alta.
- **MiddlewareInvoker**: Servicio utilizado para verificar los permisos del usuario.
- **SortableTrait**: Trait utilizado para proporcionar capacidades de ordenamiento.
- **Livewire\Component**: Componente base de Livewire que se extiende para crear componentes dinámicos.

