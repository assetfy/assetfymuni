# Documentación del Componente Livewire: CambiarEstadoActividad

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CambiarEstadoActividad` permite modificar el estado de una actividad económica y actualizar su logo asociado. Actualmente, este componente está funcionando e implementado en su forma final.

## Propiedades

- **estado**: Estado actual de la actividad económica.
- **actividad**: Almacena los datos de la actividad económica que se está editando.
- **updateEstado**: Almacena el nuevo estado de la actividad económica a ser actualizado.
- **updatedNombre**: Almacena el nombre actualizado de la actividad.
- **updatedDescripcion**: Almacena la descripción actualizada de la actividad.
- **estadoActividad**: Objeto que contiene la información de la actividad a editar.
- **logo**: Imagen del logo que se debe actualizar para la actividad.
- **open**: Controla si el modal de edición está abierto o cerrado. Inicialmente está establecido como **false**.

## Métodos

### EditarEstadoActividad($data)
Este método se activa al recibir el evento **EditarEstadoActividad**. Verifica los permisos del usuario utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si el usuario tiene los permisos adecuados, procede a abrir el modal de edición de la actividad.

### openModal($data)
Abre el modal de edición y carga los datos de la actividad económica seleccionada. Utiliza el modelo **ActividadesEconomicasModel** para obtener la actividad.

### mount(ActividadesEconomicasModel $value)
Inicializa las propiedades del componente con los datos de la actividad económica proporcionada.

### guardarCambios()
Dispara el evento **check** para proceder con la actualización de los datos.

### actualizar()
Este método está asociado al evento **guardado** mediante el atributo `#[On('guardado')]`. Llama al método **actualizarEstado** para realizar la actualización de la actividad.

### actualizarEstado()
Este método valida los datos del formulario, guarda el nuevo logo, actualiza el estado de la actividad económica y guarda los cambios en la base de datos. Luego, dispara el evento **refreshLivewireTable** para actualizar la tabla y cierra el modal.

## Eventos Livewire

- **EditarEstadoActividad**: Activa el método **EditarEstadoActividad** para iniciar la edición de una actividad.
- **check**: Disparado por el método **guardarCambios** para validar y proceder con la actualización.
- **guardado**: Disparado para llamar al método **actualizar**.
- **refreshLivewireTable**: Actualiza la tabla de actividades después de realizar cambios.

## Vista y Controlador
La vista asociada a este componente se encuentra en el archivo `livewire.actividad.cambiar-estado-actividad`. Proporciona un formulario para editar el estado de una actividad económica, incluyendo campos para actualizar el nombre, la descripción, el estado y el logo de la actividad.

El controlador principal del componente es la clase `CambiarEstadoActividad`, que maneja la lógica de negocio y se comunica con los modelos para realizar operaciones en la base de datos.

## Dependencias

El componente `CambiarEstadoActividad` depende de los siguientes elementos:

- **ActividadesEconomicasModel**: Modelo para manejar los datos de las actividades económicas.
- **MiddlewareInvoker**: Servicio para verificar los permisos del usuario antes de permitir cambios.
- **SortableTrait**: Trait que se usa para funcionalidades de ordenamiento.
- **WithFileUploads**: Trait de Livewire que permite manejar las cargas de archivos.

