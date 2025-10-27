**Documentación del Componente PrestadoraServiciosSolicitudesEditarEstado**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `PrestadoraServiciosSolicitudesEditarEstado` permite a las empresas prestadoras gestionar el estado de las solicitudes de servicio realizadas por los clientes. Este componente se utiliza para aceptar o rechazar las solicitudes de servicio y adjuntar documentos de presupuesto cuando el servicio es aceptado. El componente muestra un modal con los detalles del activo y permite la actualización del estado de la solicitud.

## Dependencias
Este componente depende de los siguientes elementos y paquetes:

- **Livewire Component**: Para crear el componente dinámico.
- **WithFileUploads**: Trait de Livewire utilizado para manejar la carga de archivos.
- **SolicitudesServiciosModel**: Modelo que representa la solicitud de servicio.
- **Illuminate\Database\Eloquent\Collection**: Para manejar colecciones de modelos Eloquent.
- **Carbon**: Para manejar y formatear fechas y horas.
- **error_log()**: Función de PHP para registrar errores en el log del sistema.

## Propiedades
- `$open`: Bandera booleana que indica si el modal para editar el estado de la solicitud está abierto o cerrado.
- `$servicios`: Instancia del modelo `SolicitudesServiciosModel` que representa la solicitud de servicio actual.
- `$presupuesto`: Archivo cargado por el usuario que representa el documento de presupuesto para el servicio aceptado.
- `$precio`: Precio del servicio que se debe ingresar al aceptar la solicitud.
- `$activo`: Información del activo asociado a la solicitud.
- `$ubicacion`: Información de la ubicación del activo.
- `$servicio`: Información del servicio solicitado.
- `$aceptacion`: Valor que indica si el servicio ha sido aceptado o rechazado.
- `$rutaDocumento`: Ruta donde se almacena el documento de presupuesto.
- `$fechaHora`: Fecha y hora del servicio, formateada para ser utilizada en el formulario.

## Métodos

### `rules()`
Define las reglas de validación para los campos del formulario. Las reglas varían según el valor de `$aceptacion`.

### `actualizar()`
Valida los datos ingresados y llama al método `guardarDatos()` para actualizar el estado de la solicitud. Luego cierra el modal y redirige al dashboard de la empresa.

### `guardarDatos()`
Guarda la información de la solicitud de servicio. Si el servicio es aceptado, se guarda el presupuesto y el precio, y se actualiza el estado a "Esperando confirmación del Cliente". Si el servicio es rechazado, se guarda el motivo de cancelación y el estado se actualiza a "Rechazado".

### `modificacionFecha($fechaHoraFormateada)`
Verifica si la fecha y hora proporcionada coinciden con la fecha y hora actuales de la solicitud.

### `formatoFecha($fecha)`
Convierte una fecha al formato `Y-m-d H:i:s`.

### `close()`
Reinicia las propiedades relevantes del componente y cierra el modal. Luego redirige al dashboard de la empresa.

### `render()`
Devuelve la vista asociada al componente (`livewire.servicios.prestadora.prestadora-servicios-solicitudes-editar-estado`).

### `openModal($data)`
Abre el modal para editar el estado de una solicitud de servicio específica. Carga los detalles del activo, la ubicación, el servicio y la fecha y hora para ser mostrados en el formulario.

## Vista del Componente
La vista del componente `prestadora-servicios-solicitudes-editar-estado` contiene un modal que permite a la empresa prestadora aceptar o rechazar una solicitud de servicio. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal enlazado a la propiedad `$open` para abrir o cerrar según su valor.

  - **Título del Modal**: Muestra el texto "Solicitud" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Detalles del Activo**: Muestra el nombre del activo al que se le solicitó el servicio.
    - **Detalles del Servicio**: Muestra el nombre del servicio solicitado.
    - **Fecha y Hora del Servicio**: Campo para seleccionar o modificar la fecha y hora del servicio.
    - **Información de la Ubicación**: Tabla con detalles de la ubicación del activo, como nombre, país, provincia, ciudad, código postal, calle, altura, piso y departamento.
    - **Aceptación del Servicio**: Selector para indicar si el servicio es aceptado o rechazado.
    - **Documentos de Presupuesto**: Campo para adjuntar un archivo de presupuesto cuando se acepta el servicio.
    - **Precio del Servicio**: Campo para ingresar el precio del servicio cuando es aceptado.
    - **Mensaje de Rechazo**: Mensaje que indica que el servicio será rechazado y se informará al cliente.

  - **Footer del Modal**:
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios.
    - **Botón "Actualizar" (`x-danger-button`)**: Ejecuta la acción de guardar los cambios si se cumplen las validaciones correspondientes.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar los cambios.

