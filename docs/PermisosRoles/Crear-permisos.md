**Documentación del Componente CrearPermisos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearPermisos` se encarga de la creación de permisos en el sistema de administración de roles y permisos. Este componente proporciona una interfaz donde los usuarios pueden registrar nuevos permisos asignados a una empresa o usuario específico. La validación de los campos se realiza antes de que la información sea almacenada en la base de datos.

## Dependencias
- **Modelos**:
  - `App\Models\TiposEmpresaModel`: Utilizado para obtener el tipo de empresa asociada al usuario.
  - `App\Models\PermisosModel`: Utilizado para registrar los nuevos permisos en la base de datos.
  - `App\Models\EmpresasModel`: Utilizado para recuperar la información de la empresa con base en el CUIT del usuario.
- **Traits**:
  - `App\Traits\VerificacionTrait`: Proporciona funcionalidades para verificar y crear registros de manera más eficiente.
- **Servicios**:
  - `App\Services\MiddlewareInvoker`: Utilizado para comprobar si el usuario tiene permiso para realizar la acción de creación de permisos.
- **Librerías de Laravel**:
  - `Illuminate\Support\Facades\Auth`: Para la autenticación del usuario y obtención de sus datos.
  - `Livewire\Component`: Componente base de Livewire.

## Propiedades
- `$nombre`: Almacena el nombre del permiso que se va a crear. Validado para garantizar que tenga un máximo de 50 caracteres.
- `$tipo_permisos`: Almacena el tipo de permisos según la empresa a la que pertenece el usuario.
- `$open`: Bandera booleana que indica si el modal está abierto o cerrado.

## Métodos

### `render()`
Devuelve la vista del componente `livewire.permisos-roles.crear-permisos`.

### `updated($propertyName)`
Este método se ejecuta cada vez que se actualiza una propiedad, realizando la validación correspondiente para verificar si cumple con las reglas establecidas.

### `save()`
Valida los campos ingresados por el usuario, obtiene el tipo de permisos, y realiza las siguientes acciones:
- Guarda la información en la base de datos utilizando el modelo `PermisosModel`.
- Emite un evento para refrescar la tabla de permisos (`refreshLivewireTable`).
- Llama al método `close()` para cerrar el modal y restablecer los valores de las propiedades.

### `close()`
Cierra el modal de creación de permisos y restablece los valores de las propiedades `$nombre` y `$tipo_permisos`. Cambia el valor de `$open` a `false`.

### `getTipoPermisos()`
Obtiene el tipo de permisos basado en el panel actual del usuario. Si el usuario está en el panel de empresa, se llama a `getEmpresaTipo()`.

### `getEmpresaTipo($cuit)`
Obtiene el tipo de empresa asociado al `cuit` proporcionado utilizando el modelo `EmpresasModel` y luego obtiene el nombre del tipo de empresa desde el modelo `TiposEmpresaModel`.

### `CrearPermisos()`
Este método escucha el evento `CrearPermisos`. Verifica si el usuario tiene permiso para realizar la acción de creación de permisos utilizando el servicio `MiddlewareInvoker`. Si el usuario no tiene permisos, se emite un evento `no-permission` con un mensaje de error; de lo contrario, se abre el modal estableciendo `$open` como `true`.

## Vista del Componente
La vista del componente `crear-permisos` contiene un modal que permite al usuario registrar un nuevo permiso. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Registro (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según su valor.

  - **Título del Modal**: Muestra el texto "Crear tus Permisos" para indicar la acción que se va a realizar.

  - **Contenido del Modal**:
    - **Campo para el Nombre (`x-input`)**: Permite al usuario ingresar el nombre del permiso, con validación en tiempo real para asegurarse de que se cumplen los requisitos.

  - **Footer del Modal**:
    - **Botón "Registrar Permiso" (`x-danger-button`)**: Ejecuta la acción de guardar el permiso, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar el permiso.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, llamando al método `close()`.

Esta estructura proporciona una interfaz clara y sencilla para que el usuario pueda crear permisos de forma efectiva y con la validación necesaria para garantizar la integridad de los datos ingresados.

