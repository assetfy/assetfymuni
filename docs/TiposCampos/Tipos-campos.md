**Documentación del Componente TiposCampos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `TiposCampos` se utiliza para mostrar una tabla interactiva de los diferentes tipos de campos en el sistema. Este componente permite al usuario visualizar, buscar y ordenar los tipos de campos a través de una tabla basada en Livewire y Laravel. La gestión se realiza mediante la librería `LivewireTables` de RamonRietdijk, proporcionando una interfaz amigable y eficiente para la manipulación de estos datos.

## Dependencias
### Modelos
- **TiposCamposModel**: Este modelo representa los tipos de campos en la base de datos.

### Librerías y Paquetes
- **RamonRietdijk\LivewireTables**: Librería utilizada para construir tablas interactivas con Livewire.
- **Livewire**: Utilizado para la gestión del estado y la interacción en tiempo real con el componente.

## Propiedades
- **\$model** (string): Define el modelo que será utilizado por el componente para cargar los datos, en este caso `TiposCamposModel`.
- **\$title** (string): El título del encabezado de la tabla, definido como "TIPOS CAMPOS".
- **\$createForm** (string): Nombre del componente de creación predeterminado. Actualmente está vacío, lo que indica que no se ha definido un formulario de creación asociado a este componente.

## Métodos
### columns(): array
Define las columnas que se mostrarán en la tabla de tipos de campos. La tabla tiene una columna principal:
- **Nombre**: Muestra el nombre de cada tipo de campo. Esta columna es tanto ordenable como buscable, lo que facilita la navegación y búsqueda de registros específicos.

## Vista del Componente
Este componente no tiene una vista explícita definida en el código presentado, ya que utiliza la infraestructura de `LivewireTables` para manejar la representación de la tabla de forma dinámica. La vista generada incluye:
- **Columna de Nombre**: Permite ver, ordenar y buscar el nombre de los tipos de campos.
- **Interacción de la Tabla**: Las tablas generadas con `LivewireTables` son interactivas, permitiendo al usuario ordenar y buscar directamente desde la interfaz.

El componente `TiposCampos` proporciona una solución simple para mostrar y gestionar los diferentes tipos de campos dentro del sistema, usando una tabla limpia y fácil de usar para el usuario final.

