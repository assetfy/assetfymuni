**Documentación del Trait SortableTrait**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Métodos](#métodos)
4. [Consideraciones de Uso](#consideraciones-de-uso)
5. [Estado de Obsolescencia](#estado-de-obsolescencia)

### Descripción General
El trait `SortableTrait` proporciona funcionalidades básicas para la ordenación de datos en componentes, principalmente utilizando propiedades como `$sort` y `$direction`. Este trait permite gestionar el campo por el cual se ordenan los datos y la dirección de la ordenación (ascendente o descendente). Además, incluye métodos para manejar eventos de renderización y actualización de componentes.

### Dependencias
Este trait se espera que funcione en un contexto de un componente Livewire, donde las propiedades `$sort` y `$direction` están definidas en la clase que lo usa. También utiliza el método `dispatch()` para lanzar eventos.

### Métodos

#### order($sort)
Este método cambia el campo de ordenación y la dirección de la misma. Si el campo de ordenación actual es igual al nuevo campo recibido, se alterna la dirección; de lo contrario, se establece el nuevo campo de ordenación con dirección descendente.

- **Parámetro**:
  - `$sort`: Campo sobre el cual se realizará la ordenación.

#### sortIcon($field)
Devuelve la clase CSS del icono que indica la dirección de la ordenación. Si el campo proporcionado es el que se está ordenando actualmente, se devuelve el icono correspondiente a la dirección (ascendente o descendente). Si no, se devuelve el icono por defecto.

- **Parámetro**:
  - `$field`: Campo sobre el cual se quiere mostrar el icono de ordenación.

#### eventos()
Lanza varios eventos utilizando `dispatch()` para realizar tareas adicionales como la actualización o renderización de componentes. También cierra cualquier ventana modal estableciendo la propiedad `$open` en `false`.

### Consideraciones de Uso
- **Uso del Trait**: El trait `SortableTrait` está diseñado para ser utilizado en componentes Livewire que necesitan implementar una funcionalidad básica de ordenación. Se espera que las propiedades `$sort` y `$direction` existan en la clase que implemente el trait.
- **Eventos Livewire**: El método `eventos()` lanza varios eventos que pueden ser útiles para refrescar tablas y actualizar la interfaz. Sin embargo, este comportamiento puede ser innecesario en muchos casos y podría sobrecargar la interfaz.

### Estado de Obsolescencia
Este trait se encuentra actualmente en **desuso**.