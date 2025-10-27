# Documentación del Componente Livewire: EditarEstadosAltas

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EditarEstadosAltas` permite editar los estados de alta existentes en el sistema. Proporciona un formulario para modificar el nombre y la descripción del estado de alta seleccionado. Este componente está implementado y requiere permisos específicos para realizar la acción de edición.

## Propiedades

- **open**: Define si el modal de edición está abierto o cerrado. Valor inicial: **false**.
- **altas**: Almacena la información del estado de alta que se va a editar.
- **updatedNombre**: Almacena el nuevo nombre del estado de alta.
- **updatedDescripcion**: Almacena la nueva descripción del estado de alta.
- **value**: Almacena el valor del estado de alta seleccionado para editar.

## Métodos

### editarEstadoAlta($data)
Este método verifica si el usuario tiene permisos para realizar la acción mediante `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene permisos, llama al método `openModal($data)` para abrir el modal de edición.

### openModal($value)
Este método busca el estado de alta mediante el valor proporcionado y, si se encuentra, lo asigna a la propiedad **altas** y establece **open** en **true** para abrir el modal.

### mount(EstadosAltasModel $value)
Este método asigna los valores del estado de alta a las propiedades **updatedNombre** y **updatedDescripcion** para que se muestren en el formulario de edición.

### actualizarAlta()
Este método valida los datos ingresados, actualiza el nombre y la descripción del estado de alta en la base de datos, y luego cierra el modal.

### cerrarModal()
Este método cierra el modal estableciendo **open** en **false**.

### render()
Este método devuelve la vista asociada al componente para mostrar el formulario de edición del estado de alta.

## Vista y Controlador
El controlador principal del componente es la clase `EditarEstadosAltas`, que maneja la lógica de edición de los estados de alta, asegurándose de que el usuario tenga los permisos necesarios para realizar la acción. La vista asociada muestra un formulario con campos para el nombre y la descripción del estado de alta, y permite guardar los cambios o cancelar la operación.

## Dependencias

El componente `EditarEstadosAltas` depende de los siguientes elementos:

- **EstadosAltasModel**: Modelo que maneja los datos de los estados de alta.
- **MiddlewareInvoker**: Servicio utilizado para verificar los permisos del usuario.
- **SortableTrait**: Trait utilizado para proporcionar capacidades de ordenamiento.
- **Livewire\Component**: Componente base de Livewire que se extiende para crear componentes dinámicos.

