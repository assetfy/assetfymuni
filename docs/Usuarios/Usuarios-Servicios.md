**Documentación del Componente UsuariosServicios**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Propiedades](#propiedades)
4. [Métodos](#métodos)
5. [Vista del Componente](#vista-del-componente)
6. [Consideraciones de Uso](#consideraciones-de-uso)
7. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El componente `UsuariosServicios` es un componente basado en `LivewireTable` que se encarga de gestionar la visualización y edición de servicios solicitados por los usuarios. Permite a los usuarios autenticados consultar y gestionar los servicios que han solicitado, así como acceder a presupuestos y editar la información relacionada con dichos servicios.

### Dependencias
El componente depende de varios modelos y bibliotecas para gestionar la información de los servicios solicitados y para proporcionar la interfaz interactiva:

- **Modelos**:
  - `App\Models\SolicitudesServiciosModel`: Maneja la información relacionada con los servicios solicitados por los usuarios.

- **Paquetes de terceros**:
  - `RamonRietdijk\LivewireTables\Livewire\LivewireTable`: Extiende la funcionalidad de tablas interactivas para Livewire.
  - `RamonRietdijk\LivewireTables\Columns\Column`: Utilizado para definir las columnas en la tabla.
  - `RamonRietdijk\LivewireTables\Columns\DateColumn`: Permite definir columnas con formatos de fecha.
  - `RamonRietdijk\LivewireTables\Columns\ImageColumn`: Utilizado para mostrar imágenes en la tabla.

- **Facades**:
  - `auth()`: Utilizado para obtener el usuario actualmente autenticado.

- **Helpers**:
  - `App\Helpers\IdHelper`: Utilizado para obtener el ID de la empresa mediante `idEmpresa()`.

### Propiedades
- **model**: Define el modelo que será utilizado por la tabla, en este caso `SolicitudesServiciosModel`.
- **title**: Título del componente, definido como 'SERVICIOS SOLICITADOS'.
- **createForm**: Nombre del componente utilizado para la creación (por defecto es una cadena vacía).
- **editForm**: Nombre del componente utilizado para la edición, definido como `'usuarios.usuarios-servicios-autorizacion'`.
- **cuit**: CUIT de la empresa, utilizado para realizar consultas relacionadas con los servicios solicitados.
- **municipio**: Variable utilizada para almacenar información del municipio.
- **useSelection**: Define si se permite la selección de filas en la tabla (fijado como `false` en este componente).

### Métodos
- **query()**: Define la consulta principal utilizada para obtener los servicios solicitados por el usuario autenticado, donde el estado del presupuesto es 'Esperando confirmación del Cliente'.
- **getServicios()**: Obtiene los servicios solicitados por una empresa prestadora utilizando la propiedad `cuit`.
- **getValue()**: Asigna el valor del `cuit` utilizando el helper `IdHelper`.
- **columns()**: Define las columnas de la tabla mostradas en la vista. Estas columnas incluyen:
  - **Servicio**: Nombre del servicio solicitado, con la opción de búsqueda.
  - **Empresa Prestadora**: Razon social de la empresa que ofrece el servicio.
  - **Fecha**: Fecha en la que se solicitó el servicio, formateada como 'Y-m-d'.
  - **Descripción**: Descripción del servicio solicitado.
  - **Estado**: Estado actual del servicio solicitado.
  - **Precio**: Precio del servicio solicitado.
  - **Estado Presupuesto**: Estado del presupuesto del servicio.
  - **Acciones**: Proporciona botones para ver el presupuesto del servicio y para editar la solicitud.

- **isSelectable($row)**: Define si una fila en particular es seleccionable (en este caso, siempre retorna `true`).

### Vista del Componente
La vista del componente `UsuariosServicios` permite a los usuarios autenticados consultar y gestionar sus servicios solicitados mediante una tabla interactiva. Las funcionalidades clave incluyen:

- **Visualización de Servicios**: Los usuarios pueden ver una lista de los servicios solicitados, incluyendo información detallada sobre el estado, la empresa prestadora, la fecha, y el presupuesto.
- **Acciones**: Para cada servicio, el usuario tiene opciones para ver el presupuesto asociado y editar la solicitud si es necesario.

### Consideraciones de Uso
- **Acceso Autenticado**: El componente está diseñado para ser utilizado por usuarios autenticados, ya que la información mostrada depende del usuario autenticado.
- **Estados de Presupuesto**: El componente filtra los servicios solicitados que tienen un presupuesto en estado de 'Esperando confirmación del Cliente', proporcionando al usuario información relevante sobre sus solicitudes.
- **Acciones Personalizadas**: Cada fila de la tabla tiene acciones personalizadas, como ver y editar la solicitud. Estas acciones se implementan con botones interactivos.

### Ejemplos de Uso
El componente `UsuariosServicios` se utiliza en aplicaciones donde los usuarios necesitan gestionar los servicios que han solicitado. Es ideal para entornos donde se desea proporcionar una experiencia interactiva, permitiendo que los usuarios vean los detalles de sus solicitudes y realicen acciones como ver presupuestos o editar información.
