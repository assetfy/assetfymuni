# Documentación del Componente Livewire: ActivosAtributos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `ActivosAtributos` permite visualizar y gestionar los atributos de los activos en el sistema. Este componente utiliza la tabla `activos_atributos` para mostrar información relacionada con atributos, subcategorías, categorías y tipos. Además, implementa filtros para categorías y subcategorías, lo que facilita la navegación y búsqueda de los atributos asociados a los activos.

## Propiedades

- **model**: Define el modelo que se usará para el componente. Valor inicial: **ActivosAtributosModel::class**.
- **title**: Almacena el título del componente. Valor inicial: **ATRIBUTOS**.
- **createForm**: Define el nombre del componente de creación predeterminado. Valor inicial: **''** (vacío) ya que no tiene componente de creacion en la tabla .
- **editForm**: Define el nombre del componente de edición predeterminado. Valor inicial: **''** (vacío) ya que no tiene componente de edicion en la tabla.
- **previousUrl**: Almacena la URL anterior para poder regresar a la vista previa.
- **useSelection**: Define si se permite la selección de filas. Valor inicial: **false**.

## Métodos

### mount()
Este método se ejecuta al inicializar el componente y llama al método `initializeActivo()` para cargar la información necesaria del activo. Además, almacena la URL previa usando `Session::get('previous_url')`.

### initializeActivo()
Este método obtiene el ID del activo de la ruta de la solicitud y lo almacena en la sesión. Esto permite filtrar los atributos relacionados con un activo específico.

### query(): Builder
Este método construye la consulta para obtener los atributos relacionados con el activo almacenado en la sesión. Filtra los atributos usando el valor de **activo** obtenido de la sesión.

### columns(): array
Este método define las columnas que se mostrarán en la tabla del componente, incluyendo atributos como **Atributo**, **Subcategoría**, **Categoría**, **Tipo**, **Campo**, y **Campo Numérico**.

### filters(): array
Este método define los filtros disponibles para la tabla, incluyendo filtros por **Categoría** y **Subcategoría**.

### getTipos(), getCategorias(), getSubcategoria()
Estos métodos permiten obtener las opciones para los filtros definidos anteriormente, utilizando las relaciones en el modelo `ActivosAtributosModel` para obtener los valores correspondientes.

### isSelectable($row): bool
Este método define si una fila es seleccionable. Actualmente, retorna **false** para deshabilitar la selección de filas.

## Vista y Controlador
El controlador principal del componente es la clase `ActivosAtributos`, que maneja la lógica de gestión y visualización de los atributos de los activos. La vista asociada muestra una tabla con los atributos filtrados por activo, y se proporcionan opciones para filtrar por categoría y subcategoría, mejorando así la navegación por la información.

## Dependencias

El componente `ActivosAtributos` depende de los siguientes elementos:

- **ActivosAtributosModel**: Modelo que maneja los datos de los atributos de los activos.
- **Session**: Utilizado para almacenar y recuperar información de sesión, como el ID del activo y la URL previa.
- **LivewireTable**: Clase base que se extiende para crear tablas dinámicas y filtrables.
- **RamonRietdijk\LivewireTables**: Librería utilizada para manejar la tabla y sus columnas de manera eficiente.

