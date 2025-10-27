# Documentación del Componente Livewire: ActividadEmpresaVista

El componente `ActividadEmpresaVista` permite la gestión y visualización de actividades empresariales desde la perspectiva de una empresa específica. Utiliza el paquete **RamonRietdijk LivewireTables** para crear una tabla interactiva que muestra la información relevante de las actividades asociadas a una empresa.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActividadEmpresaVista` está diseñado para mostrar una lista de actividades relacionadas con una empresa específica. La tabla generada por este componente incluye información sobre el estado de la actividad, el código de actividad, el responsable de autorizar, y el nombre de la actividad económica, así como acciones disponibles para cada registro.

## Propiedades
- **model**: Especifica el modelo a utilizar, que en este caso es `EmpresasActividadesModel`.
- **title**: Título que aparece como encabezado de la tabla, definido como **"Actividades"**.
- **createForm**: Nombre del componente de creación que se utilizará por defecto, denominado **"CrearNuevaActividadEmpresa"**.
- **userId**: ID del usuario actual que se asigna para realizar consultas específicas.
- **useSelection**: Controla si se permitirá la selección de filas en la tabla, en este caso está establecido como **false**.

## Métodos
### columns()
Define las columnas que serán visibles en la tabla. Las columnas definidas son:
- **Estado**: Estado actual de la actividad.
- **Código Actividad**: Campo **sortable** y **searchable**, permite ordenar y buscar por código de actividad.
- **Autoriza**: Indica el responsable de autorizar la actividad de la empresa.
- **Actividad Económica**: Nombre de la actividad económica asociada a la empresa.
- **Acciones**: Incluye un botón que permite al usuario editar la actividad. Este botón despacha el evento **EditarNuevaActividadEmpresa** al componente correspondiente.

### asignar()
Este método asigna el **userId** utilizando el método `identificador()` del **IdHelper**. Se utiliza para establecer el identificador del usuario que interactúa con el componente.

### query()
Este método sobreescribe el método `query()` predeterminado para definir la consulta personalizada. Filtra las actividades según el **cuit** de la entidad del usuario autenticado, lo cual permite mostrar únicamente las actividades relacionadas con la empresa correspondiente.

### CrearNuevaActividadEmpresa()
Este método se utiliza para abrir el componente de creación relacionado con una nueva actividad de empresa. Se despacha el evento **CrearNuevaActividadEmpresa** y se dirige al componente correspondiente (`actividad.actividad-nueva-actividad-empresa`).

## Dependencias
El componente `ActividadEmpresaVista` depende de los siguientes elementos:
- **EmpresasActividadesModel**: Modelo que proporciona los datos de actividades empresariales a la tabla.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Extiende la funcionalidad de **LivewireTable** para generar tablas interactivas.
- **Auth**: Proporciona la funcionalidad de autenticación para identificar al usuario actual.
- **IdHelper**: Utilizado para obtener el identificador del usuario actual.

Este componente facilita la gestión de actividades específicas de una empresa, permitiendo visualizar, editar y crear actividades de manera centralizada y organizada.

