# Documentación del Componente Livewire: ActividadAutorizacion

El componente `ActividadAutorizacion` permite la gestión del estado de las actividades empresariales, facilitando la autorización, el rechazo y la generación de notificaciones relacionadas. Utiliza **Laravel Livewire 3.0** para interactuar de manera dinámica con la vista y proporciona un modal que permite actualizar el estado de una empresa específica.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActividadAutorizacion` se encarga de actualizar el estado de una empresa según sea necesario: **Aceptado**, **En Revisión**, o **Rechazado**. También permite registrar un apoderado en caso de rechazo y enviar notificaciones al apoderado. Este componente facilita el proceso de autorización de empresas, permitiendo establecer un flujo de trabajo flexible y controlado para cada situación.

Este componente actualmente está en funcionamiento, pero el proceso completo de cómo debe trabajar aún no está definido.

## Propiedades
- **empresa**: Instancia de la empresa que se está evaluando.
- **cuit**: CUIT de la empresa en cuestión.
- **estado**: Estado de la empresa (Aceptado, En Revisión, Rechazado).
- **selectedEstado**: Estado seleccionado para actualizar la empresa.
- **descripcionRechazo**: Comentario proporcionado para el rechazo de la empresa.
- **apoderado**: Apoderado asociado a la empresa.
- **open**: Controla la apertura del modal para editar el estado.

## Métodos
### mount(EmpresasModel $value)
Inicializa la propiedad **empresa** buscando una empresa que coincida con el CUIT proporcionado en **$value**. Además, inicializa **selectedEstado** con el estado actual de la empresa.

### setSelectedEstado($estado)
Establece el estado seleccionado de la empresa.

### updateEstado()
Actualiza el estado de la empresa en la base de datos. Si el estado es **Rechazado**, llama a **cargaApoderado()** para buscar al apoderado y notificarlo, y también llama a **panel()** para modificar el panel del usuario si es necesario. Después de actualizar el estado, cierra el modal.

### panel($cuit)
Actualiza el **panel_actual** de los usuarios asociados a la empresa, configurándolo como **Usuario** si el estado de la empresa fue rechazado.

### cargaApoderado($cuit)
Busca al apoderado de la empresa y llama al método **notificacion()** para crear una notificación.

### notificacion($cuit, $apoderado)
Crea una notificación para el apoderado de la empresa cuando el estado de la empresa se establece en **Rechazado**.

### updateEstadoEmpresa($cuit, $estado)
Actualiza el estado de la empresa dependiendo del **panel_actual** del usuario. Llama a **AutorizaEstado()** o **AutorizacionEmpresa()** según sea necesario.

### AutorizaEstado($empresa, $estado, $cuit)
Actualiza el campo **autorizacion_estado** de la empresa y, si el estado es **Aceptado**, llama al método **actualizarEstadoUsuarioEmpresa()**.

### AutorizacionEmpresa($empresa, $estado, $cuit)
Actualiza el campo **autorizacion_empresa_reg** de la empresa y, si el estado es **Aceptado**, llama al método **actualizarEstadoUsuarioEmpresa()**.

### actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
Actualiza el estado del usuario asociado a la empresa, si su cargo es **Apoderado**, estableciendo su estado como **Aceptado**.

### render()
Renderiza la vista del componente **actividad-autorizacion**.

## Eventos Livewire
- **dataUpdated**: Emitido cuando se actualizan los datos, para indicar la necesidad de actualizar la vista.
- **refreshLivewireTable**: Utilizado para refrescar la tabla de empresas luego de actualizar el estado.

## Vista y Controlador

### Interacción entre Vista y Controlador
El componente `ActividadAutorizacion` interactúa con la vista a través del modal para actualizar el estado de la empresa. Al seleccionar un nuevo estado, se habilitan diferentes opciones, como la especificación de una **descripción de rechazo** en caso de ser necesario. Además, las actualizaciones del estado desencadenan acciones adicionales, como notificar al apoderado de la empresa o actualizar el panel de los usuarios.

### Estructura de la Vista
La vista incluye un botón que abre el modal para editar el estado de la empresa. Dentro del modal se muestran las opciones para cambiar el estado de la empresa, así como el CUIT y el código de actividad. Si el estado seleccionado es **Rechazado**, se habilita un campo adicional para ingresar la descripción del rechazo.

El modal se presenta utilizando un componente de **Blade** (`x-dialog-modal`) y la interacción con el componente Livewire se logra a través de eventos como `wire:model` y `wire:click`.

## Dependencias
El componente `ActividadAutorizacion` depende de varios modelos y clases auxiliares:
- **Illuminate\Support\Facades\Session**: Utilizado para almacenar y recuperar información de la sesión del usuario.
- **Illuminate\Support\Facades\Auth**: Utilizado para obtener el usuario autenticado y su información.
- **App\Models\UsuariosEmpresasModel**: Utilizado para obtener información sobre las relaciones entre usuarios y empresas.
- **App\Models\NotificacionesModel**: Utilizado para crear notificaciones cuando el estado de una empresa es rechazado.
- **App\Models\EmpresasModel**: Utilizado para buscar y actualizar información de una empresa específica.
- **App\Models\User**: Utilizado para actualizar información de usuarios, como el panel al que tienen acceso.
- **Livewire\Component**: Base para crear componentes en Livewire.

