**Documentación del Componente TiposUsuarios**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Propiedades](#propiedades)
4. [Métodos](#métodos)
5. [Vista del Componente](#vista-del-componente)
6. [Consideraciones de Uso](#consideraciones-de-uso)
7. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El componente `TiposUsuarios` es un componente basado en `LivewireTable`, que se encarga de gestionar la lista de usuarios y permite cambiar su tipo y estado dentro del sistema. A través de una interfaz interactiva, los administradores pueden actualizar el rol de los usuarios (por ejemplo, Administrador o Común) y habilitar o deshabilitar a los usuarios según sea necesario.

### Dependencias
El componente depende de varios modelos y bibliotecas para gestionar la información y mostrarla correctamente:

- **Modelos**:
  - `App\Models\User`: Maneja la información de los usuarios en la aplicación.

- **Paquetes de terceros**:
  - `RamonRietdijk\LivewireTables\Livewire\LivewireTable`: Extiende la funcionalidad de tablas interactivas para Livewire.
  - `RamonRietdijk\LivewireTables\Columns\Column`: Utilizado para definir las columnas en la tabla.

- **Facades**:
  - `auth()`: Utilizado para obtener al usuario actualmente autenticado.

### Propiedades
- **model**: Define el modelo que será utilizado por la tabla, en este caso el modelo `User`.
- **title**: Título del componente, definido como 'Tipos de Usuarios'.
- **createForm**: Propiedad para la configuración del formulario de creación (puede usarse en la vista).
- **useSelection**: Define si se permite la selección de filas (fijado como `false` en este componente).
- **listeners**: Define los eventos a los que el componente escucha (ej. `updateEstadoUsuario` y `updateTipoUsuario`).
- **loadingEstados**: Arreglo que maneja el estado de carga de los usuarios (utilizado para deshabilitar acciones mientras se procesan).

### Métodos
- **columns()**: Define las columnas de la tabla que serán mostradas. Estas columnas incluyen:
  - **Nombre**: Columna que muestra el nombre del usuario. Es searchable.
  - **Correo**: Columna que muestra el correo electrónico del usuario. Es sortable y searchable.
  - **Tipo**: Columna que muestra un select para cambiar el tipo de usuario (Administrador o Común). Incluye una verificación del estado de carga para deshabilitar el select si es necesario.
  - **Estado**: Columna que muestra un botón para cambiar el estado del usuario (Habilitado o Deshabilitado). Incluye verificación del estado de carga para deshabilitar el botón si es necesario.

- **updateTipoUsuario($id, $tipo)**: Actualiza el tipo de un usuario específico en la base de datos según el ID y el tipo proporcionados.
- **updateEstadoUsuario($id, $estado)**: Actualiza el estado de un usuario específico en la base de datos según el ID y el estado proporcionados.

### Vista del Componente
La vista del componente `TiposUsuarios` permite a los administradores interactuar con la lista de usuarios de una manera intuitiva, proporcionando las siguientes funcionalidades:

- **Cambio de Tipo de Usuario**: Un select desplegable para cada usuario que permite cambiar entre los diferentes tipos de usuarios definidos (Administrador o Común). El select está deshabilitado mientras se está procesando el cambio.
- **Cambio de Estado de Usuario**: Un botón que permite habilitar o deshabilitar a cada usuario, mostrando el estado actual (Habilitado o Deshabilitado). El botón también se deshabilita mientras el estado está siendo procesado.

### Consideraciones de Uso
- **Sin Selección de Filas**: El componente no permite la selección de filas de manera predeterminada, ya que la funcionalidad de selección no es relevante en este contexto.
- **Estado de Carga**: Para evitar conflictos y asegurar la consistencia de los datos, los cambios en el tipo o estado de un usuario deshabilitan temporalmente la acción mientras se procesan. Esto se maneja utilizando la propiedad `loadingEstados`.
- **Actualización en Tiempo Real**: El componente escucha eventos para actualizar el tipo y estado de los usuarios de manera dinámica, garantizando que la información mostrada siempre esté al día.

### Ejemplos de Uso
El componente `TiposUsuarios` es útil cuando se desea gestionar los roles y estados de los usuarios dentro de la aplicación de manera eficiente. Es ideal para paneles de administración donde se requiere un control detallado de los permisos y accesos de los usuarios.
