# Documentación del Componente Livewire: EstadoActividad

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Filtros](#filtros)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EstadoActividad` permite visualizar y gestionar el estado de las actividades económicas. Proporciona una tabla con información relevante de cada actividad, incluyendo su código, nombre, imagen y estado. Este componente está completamente implementado y en funcionamiento.

## Propiedades

- **model**: Define el modelo asociado al componente, en este caso, `ActividadesEconomicasModel`.
- **useSelection**: Define si se permite la selección de registros en la tabla. En este caso, está establecido como **false**.
- **title**: Título del encabezado de la tabla, definido como **'Actividades'**.
- **createForm**: Nombre del componente de creación predeterminado, que en este caso está vacío ya que no se requiere.

## Métodos

### columns()
Este método define las columnas que se mostrarán en la tabla. Las columnas incluyen:

- **Imagen**: Muestra la imagen del logo asociado a la actividad con un tamaño de 75x75 píxeles.
- **Código de actividad**: Columna que muestra el código de la actividad (`COD_ACTIVIDAD`). Es **sortable** y **searchable**.
- **Nombre**: Columna que muestra el nombre de la actividad (`nombre`). Es **sortable** y **searchable**.
- **Acciones**: Columna que muestra un botón para editar el estado de la actividad. Utiliza el evento `EditarEstadoActividad` para abrir el modal de edición.

### filters()
Este método define los filtros disponibles para la tabla. Incluye un filtro de selección para el **estado** de la actividad, que permite filtrar entre **Activo** e **Inactivo**.

## Filtros

- **Estado**: Filtro de selección que permite filtrar las actividades según su estado (**Activo** o **Inactivo**).

## Vista y Controlador
El controlador principal del componente es la clase `EstadoActividad`, que maneja la lógica de negocio y se comunica con los modelos para realizar las operaciones en la base de datos. La vista asociada muestra una tabla con información relevante de las actividades económicas, permitiendo su visualización y edición mediante un botón de acción.

## Dependencias

El componente `EstadoActividad` depende de los siguientes elementos:

- **ActividadesEconomicasModel**: Modelo que maneja los datos de las actividades económicas.
- **RamonRietdijk\LivewireTables**: Paquete utilizado para crear tablas dinámicas con Livewire, incluyendo **LivewireTable**, **ImageColumn**, **Column**, y **SelectFilter**.

