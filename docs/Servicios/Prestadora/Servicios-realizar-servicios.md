**Documentación del Componente ServiciosRealizarServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **ServiciosRealizarServicios** permite a los prestadores de servicios registrar la ejecución de un servicio previamente solicitado. Los usuarios pueden completar la información del servicio realizado, adjuntar imágenes del trabajo y actualizar el estado de la solicitud para cerrar el servicio. Este componente está pensado para facilitar la gestión de los servicios ofrecidos a los activos.

## Dependencias

### Namespaces y Paquetes Utilizados
- **App\Models\ActivosModel**: Modelo para gestionar la información de los activos.
- **App\Models\EmpresasModel**: Modelo para gestionar la información de las empresas.
- **App\Models\FotosServicioModel**: Modelo para almacenar las fotos relacionadas con un servicio.
- **App\Models\ServiciosActividadesEconomicasModel**: Modelo para las actividades económicas de los servicios.
- **App\Models\ServiciosActivosModel**: Modelo para los servicios realizados en los activos.
- **App\Models\ServiciosModel**: Modelo para gestionar la información de los servicios.
- **App\Models\SolicitudesServiciosModel**: Modelo que representa las solicitudes de servicios.
- **App\Models\User**: Modelo para gestionar la información de los usuarios.
- **Illuminate\Support\Facades\Auth**: Facade para la autenticación de usuarios.
- **Illuminate\Support\Facades\Storage**: Facade para gestionar el almacenamiento de archivos.
- **Livewire\Component**: Clase base para los componentes de Livewire.
- **Livewire\WithFileUploads**: Trait para manejar la subida de archivos en Livewire.

## Propiedades

- **$servicioId**: ID del servicio que se está realizando.
- **$servicio**: Información del servicio solicitado.
- **$activoDelServicio**: Activo al cual se le está brindando el servicio.
- **$solicitante**: Solicitante del servicio.
- **$servicios**: Lista de servicios disponibles.
- **$activo**: Activo relacionado con la solicitud.
- **$fechaHora**: Fecha y hora de la realización del servicio.
- **$regulada**: Indicador de si el servicio está regulado.
- **$user**: Usuario actual.
- **$comentarios**: Comentarios sobre el servicio realizado.
- **$fotos**: Todas las fotos subidas y capturadas durante la realización del servicio.
- **$nuevasFotos**: Fotos nuevas subidas por el usuario.
- **$capturedPhotos**: Fotos capturadas desde la cámara.
- **$previousUrl**: URL previa para volver al listado anterior.

## Métodos

### `mount($servicio)`
Inicializa el componente cargando la información del servicio, activo, y solicitante. También formatea la fecha para ser utilizada en el formulario.

### `render()`
Renderiza la vista del componente con los datos necesarios.

### `obtenerSolicitante($servicio)`
Obtiene la información del solicitante del servicio, ya sea una empresa o un usuario.

### `updatedNuevasFotos()`
Maneja la actualización de las fotos nuevas subidas por el usuario y las agrega al array **$fotos**.

### `save()`
Valida los datos ingresados y guarda la información del servicio y las fotos.

### `guardarDatos()`
Guarda la información del servicio realizado y las fotos asociadas, y actualiza el estado de la solicitud de servicio.

### `guardarFotosSubidas($servicioActivoId)`
Guarda las fotos subidas por el usuario en el almacenamiento y registra sus rutas en la base de datos.

### `guardarFotosCapturadas($servicioActivoId)`
Guarda las fotos capturadas desde la cámara en el almacenamiento y registra sus rutas en la base de datos.

### `actualizarEstado()`
Actualiza el estado de la solicitud de servicio a "Servicio Realizado, Solicitud Cerrada".

### `tipoActividad()`
Determina si el servicio está regulado y obtiene información adicional sobre la actividad económica del servicio.

### `close()`
Reinicia los datos del componente y redirige al listado de servicios pendientes.

### `usuarios()`
Obtiene el usuario autenticado.

### `formatoFecha($fecha)`
Convierte una fecha en un formato adecuado para ser almacenado en la base de datos.

### `formatDatetimeForInput($fecha)`
Formatea la fecha para ser utilizada en el campo de tipo **datetime-local** del formulario.

### `saveCapturedPhoto($imageData)`
Guarda una foto capturada desde la cámara en el array **$capturedPhotos**.

### `removeFoto($index)`
Elimina una foto del array **$fotos**.

### `removeCapturedPhoto($index)`
Elimina una foto del array **$capturedPhotos**.

## Vista del Componente
La vista del componente **ServiciosRealizarServicios** muestra un formulario que permite al usuario completar la información del servicio realizado. El formulario incluye campos para la fecha y hora, comentarios sobre la tarea realizada, y la opción de adjuntar fotos o capturar nuevas imágenes utilizando la cámara.

### Características de la Vista
- **Botón Volver**: Permite regresar a la URL anterior.
- **Línea de Progreso**: Visualización gráfica del progreso del servicio.
- **Formulario**: Incluye los siguientes campos:
  - **Servicios**: Muestra los servicios relacionados con la solicitud.
  - **Descripción**: Campo para la descripción de la tarea realizada.
  - **Activo**: Muestra el activo al cual se le está prestando el servicio.
  - **Fecha y Hora**: Campo para seleccionar la fecha y hora del servicio.
  - **Descripción de la tarea realizada**: Campo de texto para describir el servicio realizado.
  - **Adjuntar Fotos**: Permite al usuario subir fotos o capturarlas directamente con la cámara.

### Interacciones con el Usuario
- **Botón para Iniciar Cámara**: Disponible solo en dispositivos móviles. Permite al usuario capturar fotos en tiempo real.
- **Botones para Capturar y Terminar Captura**: Permiten iniciar y finalizar la captura de imágenes utilizando la cámara del dispositivo.
- **Vistas Previas de Fotos**: Muestra las fotos subidas y capturadas, con la opción de eliminarlas.

### Estilos Personalizados
Se utilizan estilos personalizados para la línea de progreso, animaciones de progreso, y vistas previas de imágenes, proporcionando una experiencia visual atractiva y dinámica.

### Script de la Vista
- **startCamera(event)**: Inicia la cámara del dispositivo para capturar imágenes.
- **btnCapture y btnFinishCapture**: Manejan la captura de fotos y la finalización de la sesión de la cámara.
- **Manejo de Dispositivos Móviles**: Detecta si el dispositivo es móvil y adapta la interfaz de usuario en consecuencia.



