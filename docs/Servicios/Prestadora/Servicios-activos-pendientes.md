**Documentación del Componente ServiciosActivosPendientes**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Detalles de las Columnas](#detalles-de-las-columnas)

## Descripción General
El componente **ServiciosActivosPendientes** es un componente de tabla que muestra los servicios pendientes de realización para los activos asociados a una empresa prestadora. Utiliza el modelo **SolicitudesServiciosModel** para obtener la información de los servicios y proporciona un enlace para realizar dichos servicios.

## Dependencias
### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Base para la implementación de tablas utilizando Livewire.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir columnas que muestran diferentes datos en la tabla.
- **Illuminate\Database\Eloquent\Builder**: Builder de Eloquent para construir consultas complejas.
- **App\Models\SolicitudesServiciosModel**: Modelo que representa las solicitudes de servicios.
- **Illuminate\Database\Eloquent\Model**: Base para los modelos de Eloquent.
- **App\Helpers\IdHelper**: Helper utilizado para obtener identificadores relacionados con la empresa.

## Propiedades
- **$model**: Define el modelo utilizado por la tabla, en este caso, **SolicitudesServiciosModel**.
- **$title**: Título que se muestra en la parte superior de la tabla ("Servicios pendientes para activos").
- **$createForm**: Nombre del componente de creación (vacío ya que no se utiliza).
- **$editForm**: Nombre del componente de edición (vacío ya que no se utiliza).
- **$empresa**: Almacena el identificador de la empresa prestadora.
- **$datos**: Propiedad para almacenar datos adicionales.

## Métodos

### `query()`
Construye y devuelve la consulta para filtrar los servicios pendientes asociados a la empresa prestadora. La consulta se realiza sobre el modelo **SolicitudesServiciosModel** y filtra las solicitudes con estado **Confirmado por Cliente y esperando visita**.

### `asignar()`
Obtiene el identificador de la empresa prestadora utilizando el helper **IdHelper::idEmpresa()** y lo asigna a la propiedad **$empresa**.

### `columns()`
Define las columnas que se mostrarán en la tabla:
- Servicio, Activo, Tipo, Categoría, Subcategoría, Empresa Prestadora, Empresa Solicitante, Solicitante, Descripción de solicitud, Estado, Precio y Actions.

### `action($model)`
Devuelve la URL para realizar un servicio asociado a un activo. Utiliza la ruta **servicios-realizar-servicios** y pasa el modelo correspondiente como parámetro.

## Detalles de las Columnas
1. **Column::make('Servicio')**: Muestra el nombre del servicio solicitado. Es una columna searchable, lo que permite filtrar por este campo.

2. **Column::make('Activo')**: Muestra el nombre del activo asociado al servicio.

3. **Column::make('Tipo')**: Muestra el tipo de servicio relacionado.

4. **Column::make('Categoría')**: Muestra la categoría del servicio.

5. **Column::make('Subcategoría')**: Muestra la subcategoría del servicio.

6. **Column::make('Empresa Prestadora')**: Muestra la razón social de la empresa prestadora.

7. **Column::make('Empresa Solicitante')**: Muestra la razón social de la empresa solicitante del servicio.

8. **Column::make('Solicitante')**: Muestra el nombre del usuario que realizó la solicitud del servicio.

9. **Column::make('Descripción de solicitud')**: Muestra la descripción de la solicitud de servicio.

10. **Column::make('Estado')**: Muestra el estado actual del servicio.

11. **Column::make('Precio')**: Muestra el precio asociado al servicio.

12. **Column::make('Actions')**: Proporciona un enlace que permite realizar el servicio. El enlace incluye un ícono para indicar la acción a realizar.

