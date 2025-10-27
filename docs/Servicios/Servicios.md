**Documentación del Componente Servicios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Interacción con la Vista](#interacción-con-la-vista)

## Descripción General
El componente **Servicios** es un componente de tabla interactiva creado con Livewire que permite visualizar una lista de servicios en la aplicación. Los servicios se muestran en columnas con opciones de búsqueda y ordenamiento.

## Dependencias
### Namespaces y Paquetes Utilizados
- **RamonRietdijk\LivewireTables\Livewire\LivewireTable**: Utilizado para generar tablas interactivas con Livewire.
- **RamonRietdijk\LivewireTables\Columns\Column**: Utilizado para definir las columnas de la tabla.
- **App\Models\ServiciosModel**: Modelo de datos que representa los servicios que se muestran en la tabla.

## Propiedades
- **\$model**: Especifica el modelo asociado a la tabla, en este caso **ServiciosModel**.
- **\$title**: Título de la tabla, que se muestra en la vista como "Servicios".
- **\$createForm**: Nombre del componente de creación predeterminado, en este caso **crearServicio**.
- **\$useSelection**: Define si se habilita la selección de filas en la tabla. En este caso, está desactivado con valor **false**.

## Métodos

### `update($value)`
Este método emite un evento para abrir el modal de edición de un servicio. Utiliza **dispatch** para enviar el evento **openModal** y proporciona el **ID del servicio** que se desea editar.

### `columns()`
Este método define las columnas de la tabla:
- **Nombre**: Columna que muestra el nombre del servicio. Esta columna es ordenable y se puede buscar en ella.
- **Descripción**: Columna que muestra la descripción del servicio.

### `crearServicio()`
Este método emite un evento para abrir el modal de creación de un nuevo servicio. Utiliza **dispatch** para enviar el evento **crearServicios** al componente de creación correspondiente.

## Interacción con la Vista
El componente se presenta como una tabla interactiva con columnas que permiten buscar y ordenar servicios por **nombre** y ver sus **descripciones**. Además, hay un flujo para editar y crear nuevos servicios, lo que hace que el componente sea flexible para la gestión de servicios dentro de la aplicación.