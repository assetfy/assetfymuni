# Documentación del Componente Livewire: ActividadEmpresas

El componente `ActividadEmpresas` permite la gestión y visualización de actividades asociadas a diferentes empresas. Utiliza el paquete **RamonRietdijk LivewireTables** para crear tablas interactivas y proporciona funcionalidades como la carga de nuevas actividades reguladoras.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActividadEmpresas` se encarga de mostrar una lista de actividades relacionadas con diferentes empresas. La tabla incluye información como la razón social de la empresa, el código de la actividad, el nombre de la actividad económica, el responsable de autorizar, y el estado de la actividad.

## Propiedades
- **model**: Especifica el modelo a utilizar, que en este caso es `EmpresasActividadesModel`.
- **title**: Título que aparece como encabezado de la tabla, en este caso es **"Actividades"**.
- **createForm**: Nombre del componente de creación que se utilizará por defecto, denominado **"CargarReguladora"**.
- **useSelection**: Propiedad que controla si se utilizará la selección de filas en la tabla, establecida en **false**.

## Métodos
### columns()
Define las columnas que serán visibles en la tabla de actividades de empresas. Las columnas definidas son:
- **Empresa**: Muestra la razón social de la empresa.
- **Código Actividad**: Campo **sortable** y **searchable**, permite ordenar y buscar por código de actividad.
- **Actividad Económica**: Nombre de la actividad económica asociada a la empresa.
- **Autoriza**: Indica quién es el responsable de autorizar la actividad.
- **Estado**: Estado de la actividad.

### CargarReguladora()
Este método se utiliza para abrir el componente de creación relacionado con la actividad reguladora. Se despacha el evento **CargarReguladora** y se dirige al componente correspondiente (`actividad.cargar-reguladora`).

## Vista y Controlador
El componente `ActividadEmpresas` se basa en la extensión **LivewireTable** del paquete de **RamonRietdijk**, lo que significa que el controlador y la vista interactúan para generar tablas que muestran datos dinámicos de las actividades empresariales. La vista permite al usuario realizar acciones como ordenar y buscar entre las actividades registradas.

## Dependencias
El componente `ActividadEmpresas` depende de los siguientes elementos:
- **EmpresasActividadesModel**: Modelo que proporciona los datos de actividades empresariales a la tabla.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Extiende la funcionalidad de **LivewireTable** para generar tablas interactivas.
- **Livewire**: Utiliza **Livewire** para crear componentes interactivos.

Este componente permite una gestión centralizada y clara de las actividades económicas asociadas a diferentes empresas, facilitando tanto su visualización como la creación de nuevas actividades mediante formularios específicos.