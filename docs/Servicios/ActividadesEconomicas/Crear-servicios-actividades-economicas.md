**Documentación del Componente CreateServiciosActividadesEconomicas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateServiciosActividadesEconomicas` se utiliza para gestionar la creación de servicios asociados a actividades económicas dentro de un sistema. Permite seleccionar un servicio y una actividad económica, y dependiendo del tipo de panel, realizar ajustes adicionales como especificar si tiene vencimiento y el tipo de vencimiento. La información es validada y luego almacenada en la base de datos.

## Dependencias
Este componente depende de varios modelos, traits y servicios:

- **Modelos**:
  - `ServiciosActividadesEconomicasModel`: Modelo para crear registros de servicios asociados a actividades económicas.
  - `EstadoActividadesEconomicasModel`: Para verificar el estado de las actividades económicas.
  - `ActividadesEconomicasModel`: Para manejar las actividades económicas disponibles.
  - `ServiciosModel`: Para obtener los servicios disponibles.

- **Servicios**:
  - `MiddlewareInvoker`: Para verificar si el usuario tiene permisos para realizar ciertas acciones.

- **Traits**:
  - `VerificacionTrait`: Proporciona métodos para verificar y realizar acciones relacionadas con la creación y actualización de registros.

## Propiedades
- `$municipio`, `$localidad`: Utilizados para almacenar información de la localidad y municipio asociada a una actividad económica.
- `$tiene_vencimiento`, `$mensual_o_x_dias`, `$cantidad_dias_o_meses`, `$es_regulada`: Propiedades que se utilizan para definir si el servicio tiene vencimiento, su tipo y la duración.
- `$servicios`, `$actividades`: Contienen la lista de servicios y actividades económicas disponibles para ser seleccionadas.
- `$id_servicio`, `$id_actividad`: Identificadores del servicio y actividad económica seleccionados.
- `$panel`: Determina si el usuario tiene acceso a funcionalidades adicionales (como vencimientos).
- `$open`: Bandera booleana que indica si el modal de creación de servicios está abierto o cerrado.

## Métodos

### `mount()`
Inicializa las listas de servicios y actividades económicas disponibles. También llama al método `panel()` para definir el acceso del usuario.

### `panel()`
Define el acceso del usuario basado en su panel actual. Si el usuario pertenece al panel de "Estado", se le otorgan opciones adicionales.

### `rules()`
Define las reglas de validación para los campos del formulario. Si el usuario pertenece al panel de "Estado", se agregan reglas adicionales para los campos de vencimiento.

### `updated($propertyName)`
Valida la propiedad que se ha actualizado para verificar si cumple con las reglas establecidas.

### `save()`
Valida todos los campos del formulario y luego llama a los métodos `datos()` y `crearRegistro()` para procesar y guardar la información.

### `datos()`
Define si el servicio está regulado, basado en el panel del usuario. Si el usuario pertenece al panel "Estado", se llama a `verificaRegulacion()`.

### `verificaRegulacion()`
Verifica si la actividad económica seleccionada está regulada y define el municipio y localidad asociados.

### `crearRegistro()`
Crea un nuevo registro de servicio asociado a la actividad económica utilizando el modelo `ServiciosActividadesEconomicasModel`.

### `close()`
Cierra el modal y restablece las propiedades del componente a sus valores predeterminados.

### `render()`
Devuelve la vista asociada al componente (`livewire.servicios.actividadeseconomicas.create-servicios-actividades-economicas`).

### `CrearServicioActividadEconomica()`
Escucha el evento `CrearServicioActividadEconomica` y abre el modal llamando al método `openModal()`.

### `openModal()`
Verifica los permisos del usuario antes de abrir el modal de creación.

## Vista del Componente
La vista del componente `create-servicios-actividades-economicas` contiene un modal que permite al usuario crear un servicio asociado a una actividad económica. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.

  - **Título del Modal**: Muestra el texto "Crear Servicios Actividades" para indicar la acción que se va a realizar.

  - **Contenido del Modal**:
    - **Select para Servicio**: Permite al usuario seleccionar un servicio de la lista de servicios disponibles.
    - **Select para Actividades Económicas**: Permite al usuario seleccionar una actividad económica de la lista de actividades disponibles.
    - **Campos de Vencimiento** (solo si el usuario pertenece al panel "Estado"):
      - **Tiene Vencimiento**: Permite al usuario especificar si el servicio tiene vencimiento.
      - **Tipo de Vencimiento**: Permite seleccionar si el vencimiento es por meses o días.
      - **Cantidad**: Permite ingresar la cantidad de días o meses para el vencimiento.

  - **Footer del Modal**:
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios.
    - **Botón "Crear Servicios Actividades" (`x-danger-button`)**: Ejecuta la acción de guardar el servicio asociado a la actividad económica, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar.

