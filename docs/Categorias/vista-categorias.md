# Documentación del Componente Livewire: Categoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `Categoria` permite gestionar las categorías dentro del sistema de gestión de activos. Proporciona una interfaz de tabla para listar, filtrar, crear y editar categorías, facilitando la administración de las mismas.

## Propiedades

- **model**: Define el modelo principal utilizado en el componente, en este caso `CategoriaModel`, que gestiona la información de las categorías.
- **title**: Título del encabezado de la tabla. Valor: **"Categoria"**.
- **createForm**: Nombre del componente de creación utilizado para añadir nuevas categorías. Valor: **"crearCategoria"**.
- **useSelection**: Indica si se utiliza la selección de filas en la tabla. Valor inicial: **false**.

## Métodos

### crearCategoria()
Este método se activa cuando el usuario desea crear una nueva categoría. Envía un evento llamado `crearCategoria` que abre el formulario para agregar una nueva categoría mediante el componente correspondiente.

### columns()
Este método define las columnas que se muestran en la tabla de categorías. Las columnas incluyen:
- **Imagen**: Columna que muestra una imagen de la categoría.
- **Sigla**: Muestra la sigla de la categoría. Es **sortable** y **searchable**.
- **Nombre**: Muestra el nombre de la categoría. Es **sortable** y **searchable**.
- **Descripción**: Muestra la descripción de la categoría.
- **Acciones**: Proporciona un botón para editar la categoría, disparando el evento `openModalCategoria`.

### filters()
Este método define los filtros disponibles para la tabla. Actualmente, se incluye un filtro para seleccionar el tipo de categoría.

### getTipos()
Este método obtiene los diferentes tipos de categorías disponibles en el sistema, utilizando el modelo `CategoriaModel` y verificando la relación con `data`. Devuelve una lista de opciones con los tipos disponibles para los filtros.

## Vista y Controlador
El controlador principal del componente es la clase `Categoria`, que maneja la lógica para la gestión de categorías dentro del sistema. La vista de la tabla muestra la lista de categorías con sus respectivas columnas y opciones de acción, como editar.

- **Tabla de Categorías**: Incluye columnas para la imagen, sigla, nombre, descripción y las acciones disponibles.
- **Botones de Acción**: Los usuarios pueden crear una nueva categoría o editar una categoría existente mediante los botones correspondientes.
- **Filtros**: La vista permite aplicar filtros para mostrar categorías según el tipo.

## Dependencias
El componente `Categoria` depende de los siguientes modelos y elementos:

- **CategoriaModel**: Modelo principal que maneja la información de las categorías en el sistema.
- **LivewireTable**: Utiliza `LivewireTable` de RamonRietdijk para mostrar la tabla de categorías y gestionar la lógica de filtrado, ordenamiento y paginación.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.

Este componente proporciona una solución eficiente para la administración de categorías, permitiendo crear, editar, filtrar y visualizar categorías en una sola interfaz.