# Documentación del Componente Livewire: ActividadesExtra

El componente `ActividadesExtra` se utiliza para la gestión y visualización de actividades relacionadas con empresas, diferenciando entre las empresas que son reguladoras autorizantes y las que se encuentran en estado autorizante. Utiliza el paquete **RamonRietdijk LivewireTables** para crear tablas interactivas.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActividadesExtra` permite la visualización y gestión de actividades relacionadas con empresas según su tipo. Dependiendo del tipo de empresa, la vista y la información mostrada varían. Las empresas de tipo `3` se consideran como reguladoras autorizantes, mientras que otras empresas se gestionan con un estado autorizante.

## Propiedades
- **model**: Define el modelo a utilizar, en este caso es `EmpresasActividadesModel`.
- **title**: Título del encabezado de la tabla, definido como **"Actividades"**.
- **createForm**: Nombre del componente de creación predeterminado. Actualmente está vacío.
- **cuitEmpresaSeleccionado**: CUIT de la empresa seleccionada para la consulta.
- **empresa**: Tipo de empresa, obtenido de la tabla de empresas.
- **useSelection**: Propiedad booleana para determinar si se utilizará la selección de filas en la tabla, establecida como **false**.

## Métodos
### asignar()
Este método asigna el valor de **cuitEmpresaSeleccionado** a partir de la sesión actual o del usuario autenticado. Luego, obtiene el **tipo** de la empresa basada en el CUIT seleccionado.

### query()
Este método construye la consulta para la tabla dependiendo del tipo de empresa.
- Si la empresa es de tipo `3`, la consulta se filtra por la columna **empresa_reguladora_autorizante**.
- En caso contrario, la consulta se filtra por **estado_autorizante**.

### columns()
Define las columnas visibles en la tabla, diferenciando entre columnas comunes y columnas específicas según el tipo de empresa.
- **commonColumns()**: Define las columnas comunes a todas las empresas, como **Código Actividad**, **CUIT**, **Última Habilitación**, **Actividad Económica**, **Provincia**, **Localidad**, **Estado**, y **Acciones**.
- **specificColumnsEmpresa()**: Define columnas específicas para empresas de tipo `3`, como **Autorización** para la empresa reguladora.
- **specificColumnsEstado()**: Define columnas específicas para empresas en estado autorizante, como **Autorización** del estado.

## Vista y Controlador
El componente `ActividadesExtra` extiende de **LivewireTable** del paquete **RamonRietdijk**, permitiendo la creación de una tabla interactiva que varía según el tipo de empresa consultada. La vista del componente permite buscar y ordenar actividades, así como realizar acciones como editar actividades a través de modales.

## Dependencias
El componente `ActividadesExtra` depende de los siguientes elementos:
- **EmpresasActividadesModel**: Modelo utilizado para obtener los datos de actividades empresariales.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Extiende la funcionalidad de **LivewireTable** para generar tablas interactivas.
- **EmpresasModel**: Modelo utilizado para obtener información de la empresa seleccionada.
- **Auth**: Proporciona autenticación para determinar la entidad del usuario autenticado.

El componente `ActividadesExtra` permite gestionar actividades empresariales con base en la regulación o estado de la empresa, proporcionando funcionalidades diferenciadas según el tipo de empresa, facilitando así la visualización y edición de las actividades.

