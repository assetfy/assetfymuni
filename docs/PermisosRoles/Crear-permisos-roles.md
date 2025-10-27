**Documentación del Componente CrearPermisosRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearPermisosRoles` se utiliza para gestionar la asignación y eliminación de permisos a roles dentro de un sistema de gestión de permisos y roles. Permite seleccionar un rol y asignar o eliminar permisos específicos, actualizando la relación en la base de datos de forma dinámica.

## Dependencias
Este componente depende de varios modelos, servicios, traits y helpers para funcionar correctamente:

- **Modelos**:
  - `UsuariosEmpresasModel`: Para obtener la relación entre usuarios y empresas.
  - `PermisosRolesModel`: Para gestionar la relación entre permisos y roles.
  - `PermisosModel`: Para manejar los permisos disponibles.
  - `RolesModel`: Para obtener la lista de roles disponibles.

- **Servicios**:
  - `MiddlewareInvoker`: Utilizado para verificar si el usuario tiene los permisos necesarios para realizar la acción.

- **Traits**:
  - `VerificacionTrait`: Proporciona métodos para verificar y realizar acciones relacionadas con la creación y actualización de registros.

- **Helpers**:
  - `IdHelper`: Utilizado para obtener el ID de la empresa asociada al usuario.

## Propiedades
- `$nombre`: Representa el nombre del permiso o rol.
- `$id_rol`: Almacena el ID del rol seleccionado al cual se le van a asignar permisos.
- `$id_permisos`: Array que contiene los IDs de los permisos asignados al rol.
- `$cuit`: Representa el CUIT asociado al usuario.
- `$usuario_empresa`: ID del usuario relacionado con una empresa.
- `$id_relacion_empresa`: ID de la relación entre la empresa y el usuario.
- `$datosEmpresa`: Contiene la información de la empresa del usuario.
- `$datos`: Almacena los datos del usuario autenticado.
- `$roles`: Lista de roles disponibles que se pueden seleccionar.
- `$open`: Booleano que indica si el modal para la creación de permisos y roles está abierto o cerrado.
- `$buscarAsignados`: Criterio de búsqueda para los permisos ya asignados.
- `$buscarDisponibles`: Criterio de búsqueda para los permisos disponibles.
- `$permisosAsignados`: Lista de permisos ya asignados al rol seleccionado.
- `$permisosDisponibles`: Lista de permisos que se pueden asignar al rol.

## Métodos

### `CrearPermisosRoles()`
Valida si el usuario tiene permisos para realizar la acción. Si no tiene permisos, emite un evento con un mensaje de error. Si tiene permisos, abre el modal de creación de permisos y roles.

### `mount()`
Inicializa el componente llamando al método `obtenerDatos()` para cargar los datos iniciales del usuario y de los roles.

### `obtenerDatos()`
Obtiene la información del usuario autenticado y, si el usuario está asociado a una empresa, carga los datos de la empresa, incluyendo CUIT, ID de usuario, y ID de la relación empresa-usuario.

### `seleccionarRol($idRol)`
Permite seleccionar un rol y actualiza los permisos asignados y disponibles para ese rol. También resetea los campos de búsqueda.

### `actualizarPermisos()`
Actualiza la lista de permisos asignados y disponibles para el rol seleccionado. Filtra los permisos en función de los criterios de búsqueda.

### `updatedBuscarAsignados()`
Se ejecuta cuando se actualiza el valor de `$buscarAsignados` y llama a `actualizarPermisos()` para reflejar los cambios en la búsqueda de permisos asignados.

### `updatedBuscarDisponibles()`
Se ejecuta cuando se actualiza el valor de `$buscarDisponibles` y llama a `actualizarPermisos()` para reflejar los cambios en la búsqueda de permisos disponibles.

### `save()`
Valida los datos ingresados, compara los permisos actuales del rol con los nuevos permisos seleccionados, y realiza las acciones necesarias para agregar o eliminar permisos. Emite un evento de éxito y cierra el modal.

### `close()`
Cierra el modal y restablece todas las propiedades del componente a sus valores iniciales.

### `render()`
Renderiza la vista del componente (`livewire.permisos-roles.crear-permisos-roles`). Llama a `actualizarPermisos()` para mantener los permisos actualizados en la vista.

## Vista del Componente
La vista del componente `crear-permisos-roles` contiene un modal que permite al usuario gestionar la asignación de permisos a roles. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Relación (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según su valor.

  - **Título del Modal**: Muestra el texto "Relación permisos y roles" para indicar la acción que se va a realizar.

  - **Contenido del Modal**:
    - **Roles Disponibles (`select`)**: Desplegable que permite seleccionar un rol de entre los disponibles. Cuando se selecciona un rol, se ejecuta el método `seleccionarRol()`.
    - **Permisos Asignados**:
      - Campo de búsqueda para permisos ya asignados al rol.
      - Lista de permisos asignados al rol, que permite deseleccionar permisos ya asignados.
    - **Permisos Disponibles**:
      - Campo de búsqueda para permisos que se pueden asignar al rol.
      - Lista de permisos disponibles que se pueden asignar al rol seleccionado.
  
  - **Footer del Modal**:
    - **Botón "Registrar Relación" (`x-danger-button`)**: Ejecuta la acción de guardar la relación entre permisos y roles, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje "Cargando..." mientras se procesa la acción de guardar la relación.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, llamando al método `close()`.

