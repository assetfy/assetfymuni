# Documentación del Componente Livewire: Controles

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Columnas de la Tabla](#columnas-de-la-tabla)

## Descripción General
El componente `Controles` es un componente de Livewire que muestra una tabla interactiva de los controles disponibles en el sistema. Este componente permite listar, buscar y ordenar los controles, así como realizar acciones específicas, como crear controles.

## Propiedades
- **model**: Define el modelo asociado al componente, que en este caso es `ControlesModel`.
- **title**: Título de la vista, definido como 'Controles'.
- **createForm**: Nombre del componente de creación predeterminado, especificado como `CreateControles`.
- **useSelection**: Propiedad que indica si se permite la selección de filas en la tabla. En este caso, está desactivado (false).

## Métodos

### CreateControles
El método `CreateControles` se encarga de disparar el evento que abre el formulario para crear un nuevo control.

## Columnas de la Tabla
El componente `Controles` define las columnas de la tabla que se muestra en la interfaz. Estas columnas incluyen:

1. **Nombre**: Columna que muestra el nombre del control. Es ordenable y permite realizar búsquedas.
2. **Descripción**: Columna que muestra la descripción del control.
3. **Acciones**: Columna que muestra un botón para editar cada control. Este botón emite un evento llamado `EditarControles` que abre el modal para editar el control correspondiente.

## Flujo del Componente
- El componente se encarga de listar todos los controles definidos en el modelo `ControlesModel`.
- Cada fila de la tabla tiene un botón para editar el control correspondiente.
- El componente también tiene un método para abrir el formulario de creación de nuevos controles, lo que permite a los usuarios agregar nuevos controles al sistema.

