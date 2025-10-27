# Documentación del Componente Livewire: CargarReguladora

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CargarReguladora` permite registrar una nueva actividad económica regulada para una empresa. Aunque actualmente está funcionando, aún no se ha implementado por completo, ya que depende el tipo de empresa determina si puede ser reguladora de una determinada actividad.

## Propiedades

- **empresas**: Lista de empresas que cumplen con los criterios de la actividad seleccionada.
- **actividades**: Lista de actividades económicas disponibles.
- **value**: Valor seleccionado durante la operación.
- **cuit**: CUIT de la empresa seleccionada.
- **selectedProvincia**: Provincia seleccionada.
- **selectedActividad**: Actividad seleccionada.
- **renovacion**: Período de renovación en días.
- **open**: Controla si el modal de registro está abierto o cerrado. Inicialmente está establecido como **false**.
- **search**: Cadena de búsqueda para filtrar empresas.
- **noEmpresas**: Indicador de si no existen empresas para la actividad seleccionada.
- **allFieldsComplete**: Indica si todos los campos requeridos están completos.

## Métodos

### updated($propertyName)
Este método se ejecuta cada vez que una propiedad es actualizada. Realiza la validación de la propiedad que ha cambiado mediante `validateOnly()` y verifica si todos los campos requeridos están completos. Si la propiedad actualizada es **selectedActividad**, se cargan las empresas correspondientes.

### mount()
Este método inicializa la lista de actividades económicas disponibles, filtrando solo aquellas cuyo estado sea **1** (activas).

### loadEmpresas()
Este método carga las empresas que están asociadas con la actividad seleccionada y que cumplen con ciertos criterios, como estado **Aceptado** y tipo **3** (reguladora).

### selectEmpresa($razon_social, $cuit)
Este método permite seleccionar una empresa, estableciendo el valor de **search** con la razón social y asignando el **cuit** de la empresa seleccionada.

### checkAllFieldsComplete()
Este método verifica si todos los campos requeridos están completos y actualiza **allFieldsComplete** en consecuencia.

### render()
Retorna la vista asociada al componente `livewire.actividad.cargar-reguladora`.

### save()
Método que se encarga de guardar la actividad regulada. Realiza la validación de los campos y luego crea un nuevo registro en el modelo **EstadoActividadesEconomicasModel**. Si no existen empresas para la actividad seleccionada, muestra un mensaje de error. Al finalizar, cierra el modal y dispara el evento **refreshLivewireTable** para actualizar la tabla.

### registro()
Método privado que realiza el registro de la actividad regulada en la base de datos.

### close()
Restablece los valores de las propiedades del componente y cierra el modal estableciendo **open** a **false**.

### CargarReguladora()
Abre el modal estableciendo **open** a **true**.

## Vista y Controlador
La vista asociada a este componente se encuentra en el archivo `livewire.actividad.cargar-reguladora`. Proporciona un formulario para seleccionar la provincia, la actividad económica y la empresa reguladora, así como para especificar el período de renovación. El controlador principal del componente es la clase `CargarReguladora`, que maneja la lógica de negocio y se comunica con los modelos para realizar operaciones en la base de datos.

## Dependencias

El componente `CargarReguladora` depende de los siguientes elementos:

- **EstadoActividadesEconomicasModel**: Modelo para manejar los datos de los estados de las actividades económicas reguladas.
- **ActividadesEconomicasModel**: Modelo para manejar los datos de las actividades económicas.
- **EmpresasModel**: Modelo para manejar los datos de las empresas.
- **SortableTrait**: Trait utilizado para funcionalidades de ordenamiento.
- **VerificacionTrait**: Trait utilizado para verificar la existencia de registros y validar los datos antes de guardar.

