**Documentación del componente Estado**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `Estado` es un componente de Livewire que se utiliza para mostrar y gestionar la auditoría de empresas asociadas a un usuario específico. Se basa en el modelo `AuditoriasModel` y permite visualizar información clave sobre el estado de las auditorías, como el CUIT, la razón social de la empresa, el estado actual y la fecha de creación. Este componente permite ver el detalles de la auditoria a través del componente de edición `empresas.detalle-estado`.

### Propiedades

- **$model**: Define el modelo base (`AuditoriasModel`) que se utilizará para las consultas de datos.
- **$userId**: Propiedad que almacena el ID del usuario autenticado. Está marcada como `#[Locked]` para asegurar que no pueda ser alterada desde el exterior del componente.
- **$title**: Nombre del encabezado del componente, en este caso, "Auditoria".
- **$createForm**: Nombre del componente de creación predeterminado (vacío por defecto).
- **$editForm**: Nombre del componente de edición predeterminado, que se utiliza para editar el estado de la auditoría (`empresas.detalle-estado`).
- **$useSelection**: Define si se utiliza la selección de filas; en este caso, está deshabilitado (`false`).

### Listeners

Este componente no define listeners explícitos para eventos externos.

### Métodos

- **asignar()**: Este método asigna el ID del usuario autenticado a la propiedad `$userId`. Es utilizado internamente para asegurar que solo se muestren auditorías relacionadas con el usuario actual.

- **query()**: Método protegido que devuelve un `Builder` para construir la consulta que se utilizará para obtener los datos de auditoría. Filtra los registros de auditoría para que solo se muestren aquellos asociados al ID del usuario autenticado.

- **columns()**: Define las columnas que se mostrarán en la tabla de auditorías:
  - **Cuit**: Columna para el CUIT de la empresa, que es ordenable y buscable.
  - **Razon Social**: Columna para la razón social de la empresa, también ordenable y buscable.
  - **Estado**: Columna que muestra el estado actual de la auditoría.
  - **Fecha**: Columna que muestra la fecha de creación de la auditoría, formateada como `d m Y`.

### Vista del Componente

La vista del componente `Estado` muestra una tabla de auditorías de empresas, donde se incluyen columnas para el CUIT, la razón social, el estado y la fecha de creación. Además, permite realizar búsquedas y ordenar la información según las necesidades del usuario. La edición de los detalles se realiza a través del componente de edición predeterminado, que está asociado al componente `empresas.detalle-estado`.

