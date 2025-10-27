**Documentación del Componente CreateRoles**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateRoles` se utiliza para gestionar la creación de roles dentro de un sistema de gestión de usuarios. Este componente permite registrar un nuevo rol, asignándole un nombre y vinculándolo a una entidad específica (empresa). La validación de datos está integrada para garantizar que se cumplan los requisitos establecidos.

## Dependencias
Este componente utiliza los siguientes modelos, traits y servicios:

- **Modelos**:
  - `RolesModel`: Se utiliza para gestionar los roles disponibles en el sistema y para crear nuevos roles.
  - `EmpresasModel`: Se utiliza para mostrar información de la empresa asociada al rol que se está creando.

- **Servicios**:
  - `MiddlewareInvoker`: Verifica si el usuario tiene los permisos necesarios para realizar la acción de creación del rol.

- **Traits**:
  - `SortableTrait`: Proporciona funcionalidades para ordenar y manejar datos del componente.

## Propiedades
- `$open`: Bandera booleana que indica si el modal para crear roles está abierto o cerrado.
- `$nombre`: Representa el nombre del rol que se desea crear. Se valida para garantizar que no sea nulo y cumpla con los límites de longitud (mínimo 3, máximo 100 caracteres).
- `$cuit`: Contiene el CUIT de la empresa asociada, que se utiliza para crear el rol vinculado a esa empresa.

## Métodos

### `mount()`
Inicializa la propiedad `$cuit` usando el helper `IdHelper::identificador()`. Si no se encuentra un identificador, se toma la entidad del usuario autenticado.

### `updated($propertyName)`
Valida la propiedad especificada cada vez que se actualiza para garantizar que cumple con las reglas establecidas.

### `save()`
Realiza la validación del campo `nombre` y verifica si el rol ya existe para la empresa especificada. Si no existe, crea un nuevo registro en la base de datos usando `RolesModel`. Posteriormente, emite eventos para manejar el estado del componente y cerrar el modal.

### `render()`
Devuelve la vista del componente (`livewire.roles.create-roles`) junto con los datos necesarios, incluyendo la información de las empresas disponibles.

### `close()`
Cierra el modal y restablece las propiedades del componente a sus valores predeterminados.

### `CrearRoles()`
Se ejecuta cuando se escucha el evento `CrearRoles`. Verifica si el usuario tiene permisos para realizar la acción utilizando `MiddlewareInvoker::checkPermisosRoles()`. Si tiene permiso, establece `$open` como `true` para abrir el modal.

## Vista del Componente
La vista del componente `create-roles` contiene un modal que permite al usuario crear un nuevo rol. A continuación se describe cada parte:

### Estructura del Modal
- **Modal de Creación (`x-dialog-modal`)**: Define un modal que está enlazado a la propiedad `$open` para abrir o cerrar según el valor de esta.
  
  - **Título del Modal**: Muestra el texto "Crear Rol" para indicar la acción que se va a realizar.
  
  - **Contenido del Modal**:
    - **Campo "Razón Social"**: Muestra la razón social de la empresa vinculada al rol que se está creando.
    - **Campo "Nombre" (`x-input`)**: Permite al usuario ingresar el nombre del nuevo rol, con validación en tiempo real para asegurar que se cumplen los requisitos de longitud.
  
  - **Footer del Modal**:
    - **Botón "Registrar Rol" (`x-danger-button`)**: Ejecuta la acción de guardar el nuevo rol, desencadenando el método `save()`.
    - **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesa la acción de guardar el rol.
    - **Botón "Cancelar" (`x-secondary-button`)**: Permite al usuario cerrar el modal sin guardar los cambios, estableciendo `$open` como `false`.

