# Documentación del Componente Livewire: ActividadExtraAutorizacion

## Propiedades

- **empresa**: Almacena los datos de la actividad específica que se va a editar, obtenidos del modelo `EmpresasActividadesModel`.
- **cuit**: CUIT de la empresa relacionada con la actividad.
- **estado**: Estado actual de la actividad.
- **selectedEstado**: Estado seleccionado por el usuario para actualizar la actividad.
- **cod**: Código de la actividad.
- **value**: Variable que almacena la actividad seleccionada.
- **open**: Controla si el modal de autorización de actividad está abierto o cerrado. Inicialmente está establecido como **false**.

## Métodos

### mount(EmpresasActividadesModel \$value)

Este método inicializa la propiedad **empresa** con la actividad correspondiente al **código de actividad** proporcionado. Se utiliza cuando se monta el componente para cargar la información de la actividad seleccionada.

### openModalActividadExtra(\$data)

Este método se activa cuando se recibe el evento **openModalActividadExtra**. Busca la actividad utilizando el ID proporcionado (\$data) y, si la encuentra, llama al método **mount** para inicializar las propiedades y establece **open** como **true** para mostrar el modal.

### setSelectedEstado(\$estado)

Este método establece el **estado** seleccionado por el usuario para actualizar la actividad.

### updateEstado()

Este método se encarga de actualizar el estado de la actividad en la base de datos. Utiliza el método **updateEstadoEmpresa** para realizar la actualización. Después de actualizar, envía dos eventos para actualizar la vista y cerrar el modal.

### updateEstadoEmpresa(\$cod, \$estado)

Este método busca la actividad por el **código de actividad** y actualiza su estado en la base de datos si la actividad es encontrada.

## Dependencias

El componente `ActividadExtraAutorizacion` depende de los siguientes elementos:

- **EmpresasActividadesModel**: Modelo utilizado para obtener los datos de actividades empresariales.
- **Livewire\Component**: Proporciona la funcionalidad base para crear componentes Livewire.

Este componente facilita la actualización del estado de las actividades empresariales mediante un modal interactivo, permitiendo a los usuarios gestionar de manera eficiente el estado de las actividades según sea necesario. Actualmente, el componente está en funcionamiento, pero aún no se ha definido completamente cómo se implementará en el flujo general de la aplicación.

