**Documentación del Componente EditarRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditarRoles` permite la edición de los roles definidos en el sistema. A través de este componente, el usuario puede modificar el nombre del rol y actualizar la información asociada. Se utiliza principalmente en contextos donde es necesario ajustar los permisos y roles dentro de una organización.

## Dependencias
Este componente depende de varios modelos y traits para su funcionamiento:

- **Modelos**:
  - `EmpresasModel`: Utilizado para obtener la información de las empresas asociadas al rol.
  - `RolesModel`: Utilizado para obtener y actualizar la información de los roles.

- **Traits**:
  - `VerificacionTrait`: Utilizado para verificar y realizar actualizaciones de registros de manera segura.

## Propiedades
- `$open`: Bandera booleana que indica si el modal para editar el rol está abierto o cerrado.
- `$upNombre`: Nombre actualizado del rol que se va a editar.
- `$id_rol`: Identificador del rol que se está editando.
- `$cuit`: CUIT de la empresa asociada al rol.
- `$roles`: Objeto que contiene la información del rol seleccionado.

## Métodos

### `openModal($value)`
Este método se encarga de abrir el modal para editar un rol específico. Recibe como parámetro el identificador del rol (`$value`) y busca la información correspondiente en la base de datos. Si el rol es encontrado, se inicializan las propiedades del componente con los datos del rol y se establece `$open` como `true` para mostrar el modal.

### `update()`
Este método se encarga de validar y actualizar el nombre del rol. Utiliza el `VerificacionTrait` para verificar los cambios y realizar la actualización de manera segura. Luego de actualizar, emite un evento para refrescar la tabla de roles (`refreshLivewireTable`).

### `render()`
Renderiza la vista asociada al componente (`livewire.roles.editar-roles`). Obtiene una lista de todas las empresas (`EmpresasModel`) y pasa los datos a la vista para su uso.

### `close()`
Cierra el modal de edición del rol y restablece las propiedades del componente a sus valores predeterminados.

## Vista del Componente
La vista del componente `editar-roles` contiene un modal que permite al usuario editar un rol específico. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Edición (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.

  - **Título del Modal**: Muestra el texto "Editar Rol" para indicar la acción que se va a realizar.

  - **Contenido del Modal**:
    - **Razon Social**: Muestra la razón social de la empresa asociada al CUIT del rol que se está editando.
    - **Campo para Nombre (`x-input`)**: Permite al usuario modificar el nombre del rol, con validación en tiempo real para asegurar que se cumplen los requisitos de longitud.

  - **Footer del Modal**:
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios.
    - **Botón "Actualizar" (`x-danger-button`)**: Ejecuta la acción de actualizar el rol, desencadenando el método `update()`. Muestra un indicador de "Cargando..." mientras se procesa la actualización.

