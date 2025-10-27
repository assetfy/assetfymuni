# Documentación del Componente Livewire: Atributos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `Atributos` permite visualizar y gestionar los atributos disponibles en el sistema, incluyendo el nombre, tipo de campo, unidad de medida y descripción de cada atributo. Proporciona funcionalidades de búsqueda, ordenación y edición para los atributos.

## Propiedades

- **title**: Título del componente. Valor inicial: **"Atributos"**.
- **createForm**: Componente Livewire asociado para la creación de un nuevo atributo. Valor inicial: **'crearAtributos'**.
- **useSelection**: Define si se permite la selección de filas en la tabla. Valor inicial: **false**.

## Métodos

### columns()
Este método define las columnas que se mostrarán en la tabla del componente. Las columnas disponibles incluyen:
- **Nombre**: Muestra el nombre del atributo, con opciones para ordenar y buscar.
- **Tipo Campo**: Muestra el tipo de campo del atributo, también con opciones para ordenar y buscar.
- **Unidad de Medida**: Indica la unidad de medida asociada al atributo.
- **Descripción**: Muestra la descripción del atributo.
- **Acciones**: Botón para editar un atributo existente.

### filters()
Define los filtros disponibles para la tabla del componente. Se incluyen dos filtros:
- **Tipo de Campo**: Filtra los atributos según el tipo de campo asociado.
- **Unidad**: Filtra los atributos según la unidad de medida.

### getTipos()
Obtiene los tipos de campo disponibles en el sistema y los devuelve como un array de opciones para el filtro de **Tipo de Campo**.

### getUnidad()
Obtiene las unidades de medida disponibles en el sistema y las devuelve como un array de opciones para el filtro de **Unidad**.

### crearAtributos()
Este método utiliza el evento **dispatch** para abrir el componente Livewire asociado a la creación de un nuevo atributo, dirigiendo a la vista **'Atributos.create-atributos'**.

## Vista y Controlador
El componente principal de `Atributos` extiende la clase `LivewireTable` y controla la visualización de los atributos a través de una tabla. La vista incluye:
- **Campos de Búsqueda y Filtros**: Filtros para buscar atributos por nombre, tipo de campo y unidad de medida.
- **Acciones**: Botón de edición para cada atributo, que activa el evento **openEditAtributos** para mostrar el formulario de edición.

## Dependencias
El componente `Atributos` depende de los siguientes elementos y modelos:

- **AtributosModel**: Modelo principal que contiene la información de los atributos.
- **LivewireTable**: Utiliza `LivewireTable` para manejar la presentación en forma de tabla y las operaciones asociadas, como ordenación, filtrado y búsqueda.
- **RamonRietdijk\LivewireTables**: Paquete utilizado para la creación de las tablas dinámicas y los filtros que permiten la gestión de los atributos de forma eficiente.