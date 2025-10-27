**Documentación del componente TablaEstado**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `TablaEstado` es un componente de Livewire que permite visualizar una lista de empresas con información relevante, como el estado de autorización y los detalles asociados. Este componente también ofrece la posibilidad de filtrar las empresas según su estado de autorización, así como visualizar los detalles de cada solicitud. Se utiliza principalmente para mostrar y gestionar solicitudes de empresas dentro del panel del usuario que tiene permisos de "Estado" o "Empresa Reguladora".

### Propiedades
- **$model**: Define el modelo de la base de datos que se utilizará para generar las filas de la tabla, en este caso `EmpresasModel`.
- **$createForm**: No tiene un componente de creación predeterminado asignado.
- **$title**: Título del encabezado de la tabla.
- **$useSelection**: Indica si se pueden seleccionar varias filas de la tabla. En este caso está deshabilitado (`false`).

### Métodos
- **query()**: Define la consulta principal utilizada para obtener los datos que se mostrarán en la tabla. Según el panel actual del usuario ("Estado" o "Empresa Reguladora"), se filtran las empresas que el usuario tiene derecho a ver. Además, si existe un filtro de estado, se aplica al resultado de la consulta.

- **columns()**: Define las columnas que se mostrarán en la tabla:
  - **Logo**: Muestra el logo de la empresa, con un tamaño de 75x75 px.
  - **Nombre**: Nombre de la empresa (razón social), con capacidad de ordenación y búsqueda.
  - **Autoriza**: Muestra la entidad que autoriza a la empresa.
  - **Estado**: Muestra el estado de la solicitud.
  - **Detalles**: Enlace a los detalles de la solicitud, utilizando un ícono de "ojo" y un enlace sin subrayado.

- **filters()**: Define los filtros que se pueden aplicar a los resultados de la tabla:
  - **Estado**: Filtro para seleccionar el estado de las empresas. Las opciones son "Todos", "En Revisión" y "Aceptado".

### Vista del Componente
La vista del componente `TablaEstado` muestra una tabla interactiva de las empresas, permitiendo ordenar, buscar y filtrar los resultados. Además, cada fila tiene una columna que permite acceder a los detalles de la solicitud correspondiente mediante un enlace. Los filtros están disponibles para facilitar la navegación entre los distintos estados de las empresas.

