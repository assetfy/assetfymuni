# Documentación del Componente Livewire: EstadosAltas

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EstadosAltas` permite visualizar y gestionar los estados de altas en el sistema. Proporciona una tabla con información relevante de cada estado de alta, incluyendo su nombre y descripción, así como acciones para editar cada estado. Este componente está completamente implementado y en funcionamiento.

## Propiedades

- **model**: Define el modelo asociado al componente, en este caso, `EstadosAltasModel`.
- **useSelection**: Define si se permite la selección de registros en la tabla. En este caso, está establecido como **false**.
- **title**: Título del encabezado de la tabla, definido como **'Estados Alta'**.
- **createForm**: Nombre del componente de creación predeterminado, definido como **'CreateEstadoAltas'**.

## Métodos

### columns()
Este método define las columnas que se mostrarán en la tabla. Las columnas incluyen:

- **Nombre**: Columna que muestra el nombre del estado de alta (`nombre`). Es **sortable**.
- **Descripción**: Columna que muestra la descripción del estado de alta (`descripcion`).
- **Acciones**: Columna que muestra un botón para editar el estado de alta. Utiliza el evento `editarEstadoAlta` para abrir el modal de edición.

### CreateEstadoAltas()
Este método dispara el evento `CreateEstadoAltas` para abrir el componente de creación de estados de alta (`altas.create-estados-altas`).

## Vista y Controlador
El controlador principal del componente es la clase `EstadosAltas`, que maneja la lógica de negocio y se comunica con los modelos para realizar las operaciones en la base de datos. La vista asociada muestra una tabla con información relevante de los estados de alta, permitiendo su visualización y edición mediante un botón de acción.

## Dependencias

El componente `EstadosAltas` depende de los siguientes elementos:

- **EstadosAltasModel**: Modelo que maneja los datos de los estados de alta.
- **RamonRietdijk\LivewireTables**: Paquete utilizado para crear tablas dinámicas con Livewire, incluyendo **LivewireTable** y **Column**.

