# Documentación del Componente Livewire: ActivosControles

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActivosControles` muestra una lista general de los controles aplicados a los activos de la organización. Permite al usuario visualizar detalles relacionados con cada activo y los controles asociados, como fechas de inicio y finalización de los controles, categorizados por tipo, categoría y subcategoría del activo.

## Propiedades

- **model**: Especifica el modelo principal utilizado en la consulta. En este caso, se refiere a `ActivosControlesModel`.
- **title**: Contiene el título que se muestra en la interfaz de usuario. Valor inicial: **"LISTA GENERAL DE CONTROLES DE SUS ACTIVOS"**.
- **createForm**: Nombre del componente predeterminado de creación. Valor inicial: **""** (sin componentes de creacion).
- **useSelection**: Indica si la selección está habilitada. Valor inicial: **false**.
- **value**: Contiene el valor ID del activo asociado con los controles. Es obtenido usando el `IdHelper`.

## Métodos

### query(): Builder
Este método define la consulta a la base de datos que se realizará. Filtra los controles de los activos según los IDs obtenidos del método **getActivos()**.

### getActivos()
Este método obtiene los IDs de los activos asociados con un usuario particular, ya sea basado en el **usuario_titular** o en el **empresa_titular**. Esto permite realizar el filtrado adecuado para mostrar los controles de activos relevantes para el usuario actual.

### getValue()
Este método obtiene el valor del ID del usuario mediante la clase `IdHelper`, el cual es utilizado para determinar qué activos pertenecen al usuario.

### columns(): array
Define las columnas que serán mostradas en la tabla de resultados. Incluye columnas para mostrar el nombre del activo, el control del activo, la fecha de inicio y la fecha final del control.

### filters(): array
Define los filtros aplicables a la tabla de resultados. En este caso, permite aplicar filtros de fechas, como **Fecha Inicio** y **Fecha Final**.

## Vista y Controlador
El componente tiene una vista que muestra los controles de los activos. A continuación, se detalla cómo se estructura la vista:

- **Header**: Muestra el título del componente: "Controles".
- **Input de Búsqueda**: Campo de búsqueda para filtrar resultados de los activos o controles.
- **Listado de Controles**: La información de cada activo y sus respectivos controles se muestra en tarjetas (cards). Cada tarjeta contiene:
  - **Nombre del Activo**.
  - **Tipo de Activo**.
  - **Categoría y Subcategoría**.
  - **Lista de Controles**: Nombre del control, fecha de inicio y fecha de término.
- **Paginación**: Se muestra un componente de paginación si existen más páginas de controles para visualizar.

## Dependencias
El componente `ActivosControles` depende de los siguientes modelos y elementos:

- **ActivosControlesModel**: Modelo que maneja la información de los controles de los activos.
- **ActivosModel**: Modelo que maneja la información de los activos registrados.
- **IdHelper**: Helper utilizado para obtener el identificador del usuario, empleado en la consulta de activos.
- **Livewire**: Utiliza Livewire para manejar la interacción con la vista de forma reactiva.

Este componente se integra fácilmente con otros elementos del sistema, como el componente `CreateControlesActivos`, que facilita la creación de nuevos controles. El filtrado y la búsqueda de los activos se pueden ajustar según la necesidad del usuario, permitiendo una mejor gestión de los activos y sus controles.

