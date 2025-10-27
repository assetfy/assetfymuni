**Documentación del componente ServiciosPrestadoraTabla**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Filtros](#filtros)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `ServiciosPrestadoraTabla` es un componente de Livewire que muestra los servicios realizados por una prestadora. Utiliza la clase `ServiciosActivosModel` para obtener los registros de servicios y mostrarlos en una tabla con columnas que incluyen la fecha del servicio, el nombre del servicio, los comentarios y la imagen asociada.

### Propiedades
- **$title**: Define el título del componente, en este caso "Servicios Realizados por la prestadora".
- **$createForm**: Indica el nombre del componente de creación predeterminado. En este caso, está vacío, lo que significa que este componente no tiene un formulario de creación.
- **$editForm**: Indica el nombre del componente de edición predeterminado. Al igual que con `$createForm`, también está vacío.
- **$tipoId**: Identificador del tipo asociado con el componente.
- **$idPrestadora**: Identificador de la prestadora cuyos servicios se mostrarán en la tabla.
- **$id_activo**: Identificador del activo relacionado con los servicios.

### Filtros
- **DateFilter**: Filtro que permite seleccionar la fecha de los servicios. El filtro se aplica a la columna de "Fecha" para facilitar la búsqueda de servicios realizados en un rango específico de tiempo.

### Métodos
- **asignar()**: Asigna el valor de `$idPrestadora` utilizando la sesión para determinar cuál es la prestadora seleccionada.

- **query()**: Devuelve la consulta de base de datos utilizada por el componente para obtener los servicios de la prestadora. Filtra los registros en función del identificador de la prestadora.

- **columns()**: Define las columnas que serán mostradas en la tabla. Incluye:
  - **Fecha**: Una columna de fecha que muestra cuándo se realizó el servicio. Además, esta columna es ordenable y tiene un formato de fecha personalizado.
  - **Servicio**: Columna que muestra el nombre del servicio realizado. Esta columna es tanto ordenable como buscable.
  - **Comentarios**: Columna que muestra comentarios adicionales sobre el servicio.
  - **Imagen**: Columna de imagen que muestra una foto asociada con el servicio.

- **filters()**: Define los filtros que se pueden aplicar en la tabla. En este caso, solo se cuenta con un filtro de fecha (`DateFilter`) que permite filtrar los servicios por su fecha de realización.

### Vista del Componente
La vista del componente `ServiciosPrestadoraTabla` mostrará una tabla que permite visualizar los servicios realizados por una prestadora. Esta tabla incluye las columnas previamente descritas (Fecha, Servicio, Comentarios e Imagen) y permite filtrar los registros por fecha para facilitar la búsqueda.

El componente no tiene un formulario de creación o edición asociado, lo cual se indica con las propiedades `$createForm` y `$editForm` que están vacías.

