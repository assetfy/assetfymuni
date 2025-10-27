**Documentación del Componente PrestadoraSolicitudServicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General

El componente **PrestadoraSolicitudServicios** se encarga de gestionar la visualización y edición de las solicitudes de servicio asignadas a una empresa prestadora. Permite al usuario ver las solicitudes y seleccionar registros específicos para su edición a través de un modal.

## Dependencias

### Namespaces y Paquetes Utilizados

- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Proporciona la base para la tabla interactiva utilizando Livewire.
- **RamonRietdijk\LivewireTables\Columns\ImageColumn**: Utilizado para definir columnas que muestran una imagen.
- **RamonRietdijk\LivewireTables\Columns\DateColumn**: Utilizado para definir columnas que muestran una fecha con un formato específico.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir columnas genéricas que muestran diferentes datos.
- **App\Models\SolicitudesServiciosModel**: Modelo que representa las solicitudes de servicios.
- **Illuminate\Database\Eloquent\Builder**: Builder de Eloquent para construir consultas complejas.
- **App\Helpers\IdHelper**: Helper utilizado para obtener identificadores relacionados con la empresa.

## Propiedades

- **\$model**: Define el modelo utilizado por la tabla, en este caso, **SolicitudesServiciosModel**.
- **\$title**: Título que se muestra en la parte superior de la tabla ("SERVICIOS SOLICITADOS").
- **\$createForm**: Nombre del componente de creación (vacío ya que no se utiliza).
- **\$editForm**: Nombre del componente de edición predeterminado, que se utiliza para abrir el modal de edición.
- **\$cuit**: Identificador de la empresa prestadora.
- **\$municipio**: Almacena el municipio asociado con la empresa.

## Métodos

### `update($value)`
Llama al método **dispatch()** para abrir el modal de edición de un registro seleccionado. Envía el valor del ID de la solicitud al modal a través del evento **openModaltabla**.

### `query()`
Construye y devuelve la consulta para filtrar las solicitudes de servicio que deben mostrarse en la tabla. Utiliza el helper **getServicios()** para determinar las solicitudes asociadas con la empresa prestadora.

### `getServicios()`
Obtiene las solicitudes de servicio que están asociadas a la empresa prestadora utilizando su CUIT. Devuelve los IDs de estas solicitudes.

### `getValue()`
Obtiene el CUIT de la empresa utilizando el helper **IdHelper::idEmpresa()** y lo almacena en la propiedad **\$cuit**.

### `columns()`
Define las columnas que se mostrarán en la tabla:

- **Foto**: Muestra una columna con la imagen asociada al servicio.
- **Servicio, Tipo, Categoria, Subcategoria**: Columnas que muestran información sobre el servicio solicitado.
- **Empresa Solicitante, Empresa Prestadora**: Columnas que indican las empresas involucradas.
- **Solicitante**: Usuario que realizó la solicitud.
- **Fecha, Descripción, Estado, Precio, Presupuesto, Estado Presupuesto**: Información adicional relacionada con la solicitud.

### `canSelect()`
Retorna un valor booleano indicando si la selección de filas está permitida en la tabla. Solo se permite si la tabla está en modo de selección y no está en modo de reordenamiento.

### `isSelectable($row)`
Retorna un valor booleano para determinar si una fila específica es seleccionable. Solo las filas que no tienen un precio definido son seleccionables.

## Vista del Componente
Este componente no define una vista personalizada para crear nuevas solicitudes (el campo `\$createForm` está vacío). La edición de las solicitudes se gestiona mediante el componente `servicios.prestadora.prestadora-servicios-solicitudes-editar-estado`. Esto permite a la empresa prestadora abrir un modal para revisar y actualizar los detalles de las solicitudes de servicio.

## Resumen
El componente **PrestadoraSolicitudServicios** permite a las empresas prestadoras visualizar las solicitudes de servicio asignadas y gestionar su estado. Este componente proporciona la capacidad de editar solicitudes a través de un modal, permitiendo al usuario aceptar o rechazar solicitudes, adjuntar presupuestos y actualizar los detalles relevantes.

