**Documentación del Componente AsignarEncargadoServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `AsignarEncargadoServicios` permite a las empresas prestadoras asignar un encargado para los servicios solicitados por sus clientes. Este encargado debe ser un representante técnico de la empresa. El componente muestra un modal que permite buscar al encargado mediante su CUIL, y luego asignarlo al servicio correspondiente.

## Dependencias
Este componente depende de los siguientes modelos y helpers:
- **SolicitudesServiciosModel**: Modelo que gestiona las solicitudes de servicios realizadas por las empresas.
- **UsuariosEmpresasModel**: Modelo que relaciona los usuarios con las empresas y verifica si un usuario es representante técnico.
- **User**: Modelo que representa a los usuarios del sistema.
- **IdHelper**: Helper utilizado para obtener identificadores como el CUIT de la empresa prestadora.

## Propiedades
- `$open`: Bandera booleana que indica si el modal para asignar el encargado está abierto o cerrado.
- `$servicios`: Colección de servicios disponibles que han sido solicitados para la empresa prestadora.
- `$usuarios`: Usuario representado por el CUIL ingresado, si es un representante técnico válido.
- `$representante`: Indica si el representante asignado es único o no.
- `$prestadora`: CUIT de la empresa prestadora que se obtiene utilizando el `IdHelper`.
- `$cuil`: CUIL del encargado que se está buscando para asignar al servicio.

## Métodos

### `mount()`
Inicializa el componente, estableciendo el CUIT de la empresa prestadora y cargando los servicios disponibles para asignar un encargado. Solo se muestran los servicios que tienen el estado "Confirmado por Cliente y esperando visita".

### `buscarUsuario()`
Busca un usuario en la base de datos que tenga el CUIL proporcionado. Si se encuentra un usuario, verifica si es un representante técnico de la empresa prestadora actual. Si es válido, asigna el usuario a la propiedad `$usuarios`. Si no es válido, se muestra un mensaje de error.

### `render()`
Devuelve la vista asociada al componente (`livewire.servicios.prestadora.asignar-encargado-servicios`).

### `close()`
Cierra el modal y reinicia las propiedades relevantes del componente.

## Vista del Componente
La vista del componente `asignar-encargado-servicios` contiene un botón que permite abrir un modal para asignar un encargado de servicio. A continuación se describe cada parte:

### Botón para Abrir el Modal
- **Botón "Asignar Encargado"**: Este botón, al ser presionado, cambia el valor de `$open` a `true`, lo cual abre el modal para asignar el encargado.

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal enlazado a la propiedad `$open` para abrir o cerrar según su valor.

  - **Título del Modal**: Muestra el texto "Encargado" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Selección de Servicios**: Permite al usuario seleccionar un servicio de la lista de servicios disponibles. Si no hay servicios disponibles, no se muestra el select.
    - **Selección de Representante**: Permite al usuario indicar si el representante es único o no, mediante un selector con opciones "Si" o "No".
    - **CUIL del Representante**: Permite ingresar el CUIL del representante que se desea buscar. El campo admite solo números y el botón "Buscar" permite verificar si el CUIL pertenece a un representante técnico de la empresa prestadora.
    - **Nombre del Usuario**: Si se encuentra un usuario válido, se muestra su nombre.
    - **Mensaje de Error**: Si el usuario ingresado no es un representante válido, se muestra un mensaje de error correspondiente.

  - **Footer del Modal**:
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios.
    - **Botón "Solicitar Servicio" (`x-danger-button`)**: Ejecuta la acción de guardar la asignación del encargado si se cumplen las validaciones correspondientes.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar la solicitud.

