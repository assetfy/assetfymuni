**Documentación del Componente Tipos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `Tipos` es un componente de Livewire que se utiliza para administrar los tipos disponibles en la aplicación. Permite listar, visualizar y editar los registros de tipos mediante una interfaz de tabla interactiva. Además, el componente proporciona acceso a una funcionalidad para crear nuevos tipos a través de un modal.

## Dependencias
### Modelos
- **TiposModel**: Proporciona acceso a la información almacenada en la base de datos sobre los tipos.

### Librerías de Terceros
- **RamonRietdijk\LivewireTables**: Utiliza las clases de LivewireTables para crear una tabla interactiva que soporta filtros, búsquedas y acciones.

## Propiedades
- **\$model** (string): El modelo asociado al componente, en este caso, `TiposModel`.
- **\$title** (string): Título del componente que se muestra en la interfaz, definido como `'Tipos'`.
- **\$createForm** (string): Nombre del evento que se utiliza para disparar la creación de un nuevo tipo, definido como `'crearTipos'`.
- **\$useSelection** (bool): Define si la selección de registros está habilitada o no, en este caso, se establece como `false`.

## Métodos
### columns(): array
Define las columnas de la tabla que se muestran en la vista del componente. Estas columnas incluyen:
- **Imagen**: Muestra una imagen asociada al tipo.
- **Sigla**: Columna que se puede ordenar para mostrar la sigla del tipo.
- **Nombre**: Columna ordenable y con funcionalidad de búsqueda para mostrar el nombre del tipo.
- **Descripción**: Columna ordenable que muestra la descripción del tipo.
- **Acciones**: Columna que incluye un botón para editar el tipo.

### crearTipos()
Método que dispara el evento `crearTipo` para abrir el modal de creación de un nuevo tipo. Utiliza `\$this->dispatch('crearTipo')->to('tipos.create-tipos')` para navegar al componente adecuado para crear un nuevo registro.

## Vista del Componente
El componente `Tipos` se muestra en forma de una tabla interactiva que permite al usuario:
- **Ver la lista de tipos** con sus respectivos detalles como imagen, sigla, nombre y descripción.
- **Buscar y ordenar** los tipos utilizando las columnas disponibles.
- **Editar** un registro existente mediante un botón "Editar" en la columna de acciones.
- **Crear nuevos tipos** mediante la funcionalidad de creación que se activa a través del método `crearTipos()`.

La tabla está diseñada para proporcionar una vista clara y ordenada de todos los tipos almacenados en la base de datos, facilitando tanto la gestión como la edición de cada uno de ellos.