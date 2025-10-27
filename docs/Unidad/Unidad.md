**Documentación del Componente UnidadMedida**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `UnidadMedida` se utiliza para gestionar las unidades de medida en la aplicación. Permite listar, buscar y ordenar las unidades de medida registradas, así como crear nuevas unidades a través de un componente de creación específico.

## Dependencias
Este componente depende de las siguientes clases y servicios:

- **UnidadModel**: Modelo de Eloquent que representa la tabla de unidades de medida en la base de datos.
- **LivewireTable**: Clase base de Livewire que proporciona funcionalidades para crear tablas interactivas.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizada para definir las columnas de la tabla, incluyendo opciones de ordenación y búsqueda.

## Propiedades
- **\$model** (string): Especifica el modelo asociado a la tabla, en este caso `UnidadModel`.
- **\$title** (string): Título del encabezado de la tabla. En este caso, "Unidad de Medida".
- **\$createForm** (string): Nombre del componente de creación predeterminado que se utiliza para agregar nuevas unidades de medida.
- **\$useSelection** (bool): Indica si se habilita la selección de filas en la tabla. En este componente, está deshabilitado.

## Métodos
### crearUnidadMedida()
Este método se utiliza para disparar un evento que abre el componente de creación de una nueva unidad de medida. Utiliza la función `$this->dispatch()` para enviar el evento `crearUnidadMedida` y mostrar el formulario correspondiente.

### columns(): array
Define las columnas que se mostrarán en la tabla de unidades de medida. Las columnas incluyen:
- **Nombre**: Esta columna muestra el nombre de la unidad de medida y tiene opciones de ordenación y búsqueda habilitadas.

## Vista del Componente
El componente se representa como una tabla que incluye las siguientes funcionalidades:
- **Ordenación**: La columna "Nombre" es ordenable, lo cual permite al usuario organizar las unidades de medida según su preferencia.
- **Búsqueda**: La columna "Nombre" es searchable, lo que permite a los usuarios realizar búsquedas rápidas de unidades de medida.

Además, existe la opción de crear nuevas unidades de medida mediante el formulario asociado, que se activa con el método `crearUnidadMedida()`. Este formulario permite agregar información relevante sobre la nueva unidad de medida y actualizar la lista de manera dinámica.

