**Documentación del Componente CargarRutas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CargarRutas` se utiliza para gestionar la creación de rutas dentro de un sistema de permisos y roles. Permite registrar una nueva ruta con un nombre asociado, lo que facilita la administración de accesos en el sistema. La funcionalidad del componente está centrada en la validación de los campos requeridos y el registro de la ruta en la base de datos.

## Dependencias
Este componente depende de varios modelos, traits y otros servicios para su correcto funcionamiento:

- **Modelos**:
  - `RutasModel`: Para gestionar el registro de nuevas rutas en el sistema.

- **Traits**:
  - `VerificacionTrait`: Proporciona métodos para verificar y realizar acciones relacionadas con la creación y actualización de registros.

## Propiedades
- `$nombre`: Representa el nombre de la ruta que se va a registrar. Se valida para garantizar que no sea nulo y cumpla con los límites de longitud (mínimo 3, máximo 100 caracteres).
- `$ruta`: Contiene el valor de la ruta que se registrará en el sistema.
- `$open`: Es una bandera booleana que indica si el modal de registro de rutas está abierto o cerrado.
- `use VerificacionTrait`: Utiliza el `VerificacionTrait` para facilitar la verificación y creación de registros.

## Métodos

### `render()`
Devuelve la vista asociada al componente (`livewire.permisos-roles.cargar-rutas`).

### `permisosRolescargarRutas()`
Se ejecuta cuando se escucha el evento `permisosRolescargarRutas`, abriendo el modal para registrar una nueva ruta estableciendo `$open` como `true`.

### `updated($propertyName)`
Se ejecuta cada vez que se actualiza una propiedad. Valida la propiedad que se ha actualizado para verificar si cumple con las reglas establecidas.

### `save()`
Realiza la validación de los campos `nombre` y `ruta` para asegurar que la información ingresada sea correcta. Si se cumplen las validaciones:
- Guarda la información en la base de datos utilizando el modelo `RutasModel`.
- Emite un evento para refrescar la tabla de rutas (`refreshLivewireTable`).
- Cierra el modal restableciendo los valores de las propiedades `$nombre` y `$ruta`.

### `close()`
Cierra el modal de registro y restablece los valores de las propiedades `$nombre` y `$ruta`. Establece `$open` como `false`.

## Vista del Componente
La vista del componente `cargar-rutas` contiene un modal que permite al usuario registrar una nueva ruta. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Registra las rutas" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Campo para el Nombre (`x-input`)**: Permite al usuario ingresar el nombre de la ruta, con validación en tiempo real para asegurarse de que se cumplen los requisitos de longitud.
    - **Campo para la Ruta (`x-input`)**: Permite al usuario ingresar la ruta a registrar.
    
  - **Footer del Modal**:
    - **Botón "Registrar ruta" (`x-danger-button`)**: Ejecuta la acción de guardar la ruta, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar la ruta.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

