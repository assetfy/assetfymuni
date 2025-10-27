# Documentación del Componente Livewire: EliminarActivos

El componente `EliminarActivos` permite la eliminación de un activo dentro de la aplicación. Utiliza **Laravel Livewire 3.0** para gestionar la operación de eliminación mediante un formulario modal.

> **Nota:** Actualmente, este componente está en desuso, pero el controlador sigue disponible por si se necesita implementar en el futuro.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EliminarActivos` facilita la eliminación de activos existentes en la aplicación. Los usuarios pueden eliminar un activo de la base de datos con solo hacer clic en un botón, confirmando así su decisión. La acción de eliminación desencadena un evento que actualiza la vista, mostrando el cambio al usuario.

## Propiedades
- **activo**: Almacena el ID del activo que se desea eliminar.
- **open**: Controla si el modal está abierto o cerrado.

## Métodos
### mount($activoId)
Inicializa el componente con el ID del activo a eliminar, asegurando que se tiene la referencia correcta al activo en cuestión.

### deleteRecord()
Este método se encarga de eliminar el activo de la base de datos. Utiliza el ID almacenado en la propiedad `activo` para encontrar el registro correspondiente y eliminarlo. Luego de la eliminación, se emiten eventos para actualizar la vista y mostrar una alerta de confirmación. Finalmente, se cierra el modal.

## Eventos Livewire
- **render**: Emitido luego de la eliminación del activo para actualizar la vista.
- **alert**: Emitido para mostrar un mensaje de confirmación indicando que el activo ha sido eliminado exitosamente.

## Vista y Controlador

### Interacción entre Vista y Controlador
El componente `EliminarActivos` se comunica con la vista mediante eventos y el enlace de propiedades (`wire:model`). Cuando el usuario hace clic en el botón para eliminar un activo, el componente ejecuta el método `deleteRecord`, que elimina el activo de la base de datos y actualiza la vista.

### Manejo del Modal
El modal se abre haciendo clic en un botón con el evento `wire:click`. Una vez abierto, el usuario puede confirmar la eliminación del activo. Después de eliminar el activo, el modal se cierra y se muestra un mensaje de éxito.

## Dependencias
El componente `EliminarActivos` depende del siguiente modelo:
- **ActivosModel**: Utilizado para buscar y eliminar el registro del activo correspondiente en la base de datos. Este modelo gestiona toda la información de los activos y sus relaciones con otras entidades, como subcategorías, tipos, estados, etc.

