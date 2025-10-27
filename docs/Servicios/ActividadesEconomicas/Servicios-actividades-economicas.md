**Documentación del Componente ServiciosActividadesEconomicas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas](#columnas)

## Descripción General
El componente `ServiciosActividadesEconomicas` es parte del módulo de gestión de actividades económicas y permite visualizar, gestionar y filtrar los servicios relacionados con actividades económicas que las empresas registran. Este componente se utiliza para listar servicios, actividades y otros detalles específicos en función del tipo de empresa.

## Propiedades
- **$model**: Define el modelo que se utiliza para este componente (`ServiciosActividadesEconomicasModel`).
- **$title**: El título que se muestra en la parte superior del componente, en este caso "Servicios Actividades Economicas".
- **$createForm**: Nombre del componente utilizado para la creación de registros ("CrearServicioActividadEconomica").
- **$municipio**: Almacena el tipo de municipio obtenido del modelo `EmpresasModel`.
- **$cuit**: CUIT de la empresa asociada al servicio que se está gestionando.

## Métodos

### `asignar()`
Este método se encarga de obtener el `CUIT` de la empresa seleccionada en la sesión, o bien asignarlo a la entidad del usuario autenticado si no hay un valor seleccionado. Luego, determina el tipo de municipio de la empresa y lo asigna a la propiedad `$municipio`.

### `columns()`
Este método define las columnas a mostrar en la tabla. Llama al método `asignar()` para inicializar las propiedades relevantes y determina cuál de las configuraciones de columnas se debe mostrar según el tipo de municipio:

- Si `$municipio` es "4", llama a `columnsForMunicipio4()`.
- En otros casos, llama a `columnsForOtherMunicipios()`.

### `columnsForMunicipio4()`
Devuelve una configuración de columnas específica para los municipios de tipo "4". Incluye columnas adicionales como "Localidad", "Vencimiento", "Tiempo Vencimiento", "Tiempo Estimado" y "Act. Regulada".

### `commonColumns()`
Define las columnas comunes que se deben mostrar en la tabla, independientemente del tipo de municipio. Estas columnas son "Servicio" (nombre del servicio) y "Actividades" (nombre de la actividad económica).

### `columnsForOtherMunicipios()`
Devuelve la configuración de columnas para otros municipios, que corresponde a las columnas comunes devueltas por `commonColumns()`.

### `CrearServicioActividadEconomica()`
Este método despacha el evento `CrearServicioActividadEconomica` que activa el componente de creación de servicios para actividades económicas, llevando al componente `create-servicios-actividades-economicas`.

## Columnas
Las columnas se definen según el tipo de municipio asociado a la empresa:

### Columnas Comunes
- **Servicio**: Nombre del servicio asociado, es ordenable y se puede buscar.
- **Actividades**: Nombre de la actividad económica relacionada.

### Columnas para Municipio de Tipo "4"
- **Localidad**: Localidad donde se presta el servicio.
- **Vencimiento**: Indicador si el servicio tiene un vencimiento.
- **Tiempo Vencimiento**: Si aplica, especifica si el vencimiento es mensual o por días.
- **Tiempo Estimado**: Indica el número de días o meses para el vencimiento.
- **Act. Regulada**: Muestra si la actividad está regulada.

## Descripción del Comportamiento
El componente primero determina el contexto del usuario o empresa en la sesión para decidir qué columnas mostrar. Si la empresa asociada tiene el tipo de municipio "4", se muestran columnas adicionales que detallan información específica de vencimientos y regulación. En otros casos, sólo se muestran las columnas comunes de servicios y actividades.

El componente también permite la creación de nuevos registros de servicios y actividades económicas a través del método `CrearServicioActividadEconomica()`, que abre un formulario para introducir estos datos.

