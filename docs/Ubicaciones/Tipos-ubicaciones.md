**Documentación del Componente TiposUbicaciones**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `TiposUbicaciones` se utiliza para gestionar los diferentes tipos de ubicaciones en una aplicación basada en Laravel Livewire. Proporciona una interfaz de tabla que permite a los usuarios visualizar, buscar y ordenar los tipos de ubicaciones existentes. Además, incluye la funcionalidad para crear nuevos tipos de ubicaciones mediante un componente asociado.

## Dependencias
Este componente depende de las siguientes librerías y servicios:

- **Modelos**:
  - `TiposUbicacionesModel`: Modelo que representa los tipos de ubicaciones en la base de datos.

- **Servicios**:
  - **RamonRietdijk\LivewireTables**: Utilizado para la generación y configuración de las tablas en Livewire.

## Propiedades
- **model** (string): Define el modelo asociado a la tabla, en este caso `TiposUbicacionesModel`.
- **title** (string): Título de la tabla que se muestra en la interfaz. En este componente, se define como "Tipos de Ubicaciones".
- **createForm** (string): Nombre del componente de creación predeterminado, en este caso, `crearubicacion`.
- **useSelection** (bool): Define si se habilita la selección de filas en la tabla. Está desactivado en este componente (`false`).

## Métodos
### columns(): array
Este método define las columnas que se mostrarán en la tabla de tipos de ubicaciones. Las columnas definidas son:
- **Nombre**: Columna que muestra el nombre del tipo de ubicación. Es sortable y searchable, permitiendo ordenar y buscar por nombre.

### crearubicacion()
Este método permite abrir el componente de creación de un nuevo tipo de ubicación. Utiliza `dispatch` para emitir el evento `crearTipoUbicacion` que abre el componente `ubicaciones.crear-tipos-ubicaciones`.

## Vista del Componente
Este componente se representa mediante una tabla que permite a los usuarios interactuar con los tipos de ubicaciones disponibles. La vista de la tabla incluye:
- **Columna de Nombre**: Muestra el nombre de cada tipo de ubicación registrado.
- **Funcionalidades**: Los usuarios pueden ordenar y buscar tipos de ubicaciones mediante la interfaz de la tabla.
- **Creación de Nuevas Ubicaciones**: La opción de crear un nuevo tipo de ubicación se activa mediante el método `crearubicacion`, el cual abre un modal o formulario que permite introducir los detalles del nuevo tipo de ubicación.

