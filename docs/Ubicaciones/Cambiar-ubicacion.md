**Documentación del Componente CambiarUbicacion**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CambiarUbicacion` permite cambiar la ubicación de un activo en la aplicación, registrando el movimiento del activo y actualizando los datos correspondientes. El usuario puede seleccionar una ubicación disponible para el activo seleccionado y confirmar la acción, todo desde un modal que se despliega en pantalla.

## Dependencias
### Modelos
- **AuditoriaUbicacionActivoModel**: Modelo utilizado para registrar la auditoría de cambios en las ubicaciones de los activos.
- **UbicacionesModel**: Modelo que gestiona las ubicaciones disponibles para el activo.
- **ActivosModel**: Modelo que representa al activo cuyo cambio de ubicación está siendo gestionado.

### Servicios
- **MiddlewareInvoker**: Servicio que verifica si el usuario tiene los permisos necesarios para realizar cambios en la ubicación del activo.

### Otros
- **IdHelper**: Helper que se utiliza para obtener el identificador del usuario actual.
- **Livewire**: Framework de Laravel para la gestión de componentes interactivos.
- **DB**: Facade de Laravel para el manejo de transacciones en la base de datos.
- **Exception**: Para manejar los errores que ocurren durante la transacción.

## Propiedades
- **open** (bool): Indica si el modal para cambiar la ubicación del activo está abierto.
- **id_ubicacion**: Identificador de la ubicación seleccionada para mover el activo.
- **activo**: Instancia del modelo `ActivosModel` del activo a ser cambiado de ubicación.
- **ubicacionActual**: Instancia del modelo `UbicacionesModel` que representa la ubicación actual del activo.
- **ubicacionesDisponibles**: Colección de ubicaciones disponibles donde se puede mover el activo.
- **userId**: Identificador del usuario actual.

## Métodos
### mount()
Inicializa las propiedades del componente, estableciendo el estado inicial del modal y las demás propiedades.

### openModalCambiarUbicacion($data)
Verifica si el usuario tiene los permisos necesarios antes de abrir el modal. Si tiene permisos, obtiene el ID del activo y llama al método `openModal()`.

### openModal($activoId)
Busca el activo por el ID proporcionado, asigna la ubicación actual y carga las ubicaciones disponibles para el movimiento. Luego, establece el modal como abierto.

### obtenerUbicacionesDisponibles($ubicacionActual, $userId)
Devuelve una lista de ubicaciones disponibles para el usuario, excluyendo la ubicación actual del activo.

### actualizar()
Gestiona la transacción para actualizar la ubicación del activo. Si ocurre un error durante la actualización, realiza un rollback y muestra un mensaje de advertencia.

### auditoria()
Registra la auditoría del cambio de ubicación del activo en la base de datos.

### render()
Renderiza la vista del componente `livewire.ubicaciones.cambiar-ubicacion`.

## Vista del Componente
La vista del componente se presenta a través de un modal que incluye los siguientes elementos:
- **Ubicación Actual**: Se muestra la ubicación actual del activo.
- **Propiedad**: Se muestra la información de la propiedad asociada a la ubicación actual del activo.
- **Ubicaciones Disponibles**: Un dropdown para seleccionar entre las ubicaciones disponibles donde se puede mover el activo.
- **Botones**: El modal incluye un botón para cancelar la operación y un botón para confirmar y actualizar la ubicación del activo.

El modal proporciona una interfaz clara y sencilla para que el usuario pueda cambiar la ubicación de un activo, mostrando tanto la ubicación actual como las opciones disponibles para el cambio.

