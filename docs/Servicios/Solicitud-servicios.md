**Documentación del Componente SolicitudServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas](#columnas)
- [Filtros](#filtros)

## Descripción General
El componente **SolicitudServicios** es un componente de tabla de **Livewire** que muestra las solicitudes de servicios realizadas para activos. Este componente permite filtrar, buscar, y visualizar información detallada sobre cada solicitud, incluyendo información sobre el activo, el servicio, las empresas involucradas, y otros detalles relevantes.

## Dependencias
### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Proporciona la estructura base para construir tablas interactivas con Livewire.
- **RamonRietdijk\LivewireTables\Filters\SelectFilter**: Permite implementar filtros de selección.
- **RamonRietdijk\LivewireTables\Columns\ImageColumn**: Utilizado para mostrar columnas que contienen imágenes.
- **RamonRietdijk\LivewireTables\Columns\DateColumn**: Utilizado para mostrar columnas con formato de fecha.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir columnas genéricas en la tabla.
- **App\Models\SolicitudesServiciosModel**: Modelo que representa las solicitudes de servicios en la base de datos.
- **App\Models\ActivosModel**: Modelo para gestionar los activos.
- **App\Helpers\IdHelper**: Helper para obtener el identificador del usuario.

## Propiedades
- **\$model**: Define el modelo asociado a la tabla, en este caso, **SolicitudesServiciosModel**.
- **\$title**: Título del encabezado de la tabla. El valor es "SOLICITUDES DE SERVICIOS PARA ACTIVOS".
- **\$createForm**: Nombre del componente para la creación de un nuevo registro. Actualmente no se usa.
- **\$value**: Almacena el identificador del usuario o la empresa del titular del activo.
- **\$useSelection**: Indica si se permite la selección de filas. Está deshabilitado en este caso.

## Métodos
### `query()`
Este método genera la consulta principal que se ejecuta para recuperar los datos de la tabla. Filtra los registros de **SolicitudesServiciosModel** para incluir solo aquellos activos que pertenecen al usuario o empresa actual.

### `getActivos()`
Este método obtiene los identificadores de los activos que están vinculados al usuario o empresa actualmente autenticado. Primero intenta encontrar los activos mediante el campo **usuario_titular** y, si no encuentra ningún registro, realiza la búsqueda por **empresa_titular**.

### `getValue()`
Obtiene el identificador del usuario o empresa a través de **IdHelper** y lo asigna a la propiedad **\$value**.

### `getServicios()`
Este método obtiene todos los servicios asociados a las solicitudes de servicios existentes. Utiliza una relación para acceder al nombre y al identificador del servicio.

## Columnas
El componente define varias columnas que se muestran en la tabla:
- **Foto**: Muestra una imagen del activo relacionado con el servicio.
- **Servicio**: Nombre del servicio solicitado, con capacidad de búsqueda.
- **Tipo**: Tipo del activo.
- **Categoría**: Categoría del activo.
- **Subcategoría**: Subcategoría del activo.
- **Empresa Prestadora**: Razón social de la empresa que ofrece el servicio.
- **Empresa Solicitante**: Razón social de la empresa que solicita el servicio.
- **Solicitante**: Nombre del usuario que solicita el servicio.
- **Fecha**: Fecha de la solicitud, formateada en el formato **Y-m-d**.
- **Descripción**: Descripción del servicio solicitado.
- **Estado**: Estado actual de la solicitud.

## Filtros
El componente permite añadir ciertos filtros a la tabla para facilitar la búsqueda y navegación:
- **Filtro de Selección de Servicios**: Permite filtrar las solicitudes por el identificador del servicio. Utiliza el método **getServicios()** para obtener las opciones disponibles.
- **Filtro de Fecha**: Permite filtrar las solicitudes por la fecha de solicitud, usando el campo **fechaHora**.

