# Documentación del Componente Livewire: ControlDetalle

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ControlDetalle` permite visualizar los detalles de un control específico aplicado a un activo. Muestra información relevante sobre el activo y el control, incluyendo las fechas de inicio y finalización del control. Este componente facilita la gestión y revisión de los controles aplicados a activos individuales.

## Propiedades

- **model**: Especifica el modelo principal utilizado en la consulta. En este caso, se refiere a `ActivosControlesModel`.
- **title**: Contiene el título que se muestra en la interfaz de usuario. Valor inicial: **"CONTROLES DEL ACTIVO"**.
- **createForm**: Nombre del componente predeterminado de creación. Valor inicial: **""** (sin componente de creacion).
- **editForm**: Nombre del componente predeterminado de edición. Valor inicial: **""** (sin componente de edicion).
- **control**: Almacena el ID del control seleccionado.
- **activo**: Almacena el ID del activo asociado al control.

## Métodos

### asignar()
Este método asigna los valores de **control** e **id_activo** utilizando los valores almacenados en la sesión. Se utiliza para determinar qué control y activo se van a mostrar en la vista de detalles.

### query(): Builder
Este método define la consulta a la base de datos que se realizará. Filtra los registros de la tabla `activos_control` según el **control** y **activo** especificados, proporcionando los datos específicos del control aplicado al activo.

### columns(): array
Define las columnas que serán mostradas en la tabla de resultados. Incluye columnas para mostrar:
- **Activo**: Nombre del activo asociado al control.
- **Controles**: Nombre del control aplicado al activo.
- **Fecha Inicio**: Fecha de inicio del control, con formato **Y-m-d**.
- **Fecha Final**: Fecha final del control, con formato **Y-m-d**.

### isSelectable($row): bool
Este método determina si una fila puede ser seleccionada. En este caso, devuelve **false** ya que la selección de filas no está habilitada para este componente.

## Vista y Controlador
La vista asociada al componente `ControlDetalle` muestra los detalles de los controles aplicados a un activo específico. A continuación, se describe la estructura de la vista:

- **Botón de Volver**: Proporciona un enlace para regresar a la página anterior.
- **Controles Obligatorios de Carga Inicial**: Si existen controles obligatorios que aún no se han aplicado, se muestra una alerta informativa con sus nombres.
- **Listado de Controles**: Los controles asociados al activo se muestran en tarjetas (cards). Cada tarjeta contiene:
  - **Nombre del Control**: Se muestra en el encabezado de la tarjeta.
  - **Detalles del Control**: Un enlace que lleva a una vista detallada del control.

Si no existen controles para mostrar, se presenta un mensaje indicando que no hay registros disponibles.

## Dependencias
El componente `ControlDetalle` depende de los siguientes modelos y elementos:

- **ActivosControlesModel**: Modelo que maneja la información de los controles de los activos.
- **Livewire**: Utiliza Livewire para manejar la interacción con la vista de forma reactiva.
- **Session**: Se utiliza para almacenar y recuperar los valores del **control** y **activo** seleccionados.

Este componente está diseñado para integrarse con otros elementos del sistema, facilitando la revisión detallada de los controles aplicados a cada activo. Proporciona una interfaz clara y sencilla para explorar los controles, permitiendo a los usuarios acceder rápidamente a la información relevante.

