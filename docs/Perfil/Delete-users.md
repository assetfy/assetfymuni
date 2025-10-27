**Documentación del Componente DeleteUser**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `DeleteUser` es un componente de Livewire que permite al usuario eliminar su cuenta de manera permanente. Este proceso requiere la confirmación mediante el ingreso de la contraseña del usuario para asegurar la seguridad de la acción. El componente incluye un modal que solicita la contraseña antes de proceder con la eliminación de la cuenta.

### Propiedades
- **$confirmingUserDeletion**: Booleano que indica si se está confirmando la eliminación de la cuenta del usuario.
- **$password**: Cadena de texto que almacena la contraseña ingresada por el usuario para confirmar la eliminación de la cuenta.

### Métodos
- **confirmUserDeletion()**: Activa la variable `$confirmingUserDeletion` para mostrar el modal de confirmación de eliminación de cuenta.

- **deleteUser()**: Valida la contraseña ingresada por el usuario y verifica que sea correcta. Si la contraseña es válida, se procede a eliminar la cuenta del usuario, enviando previamente una notificación. El usuario es marcado como inactivo y se redirige al usuario a la página de inicio.

- **render()**: Renderiza la vista principal del componente, que incluye el botón de eliminación y el modal de confirmación.

### Vista del Componente
La vista asociada al componente `DeleteUser` proporciona una interfaz visual que incluye:

- **Encabezado y Descripción**: Muestra el título "Eliminar Cuenta" y una descripción clara sobre las implicaciones de eliminar la cuenta, indicando que todos los datos serán eliminados permanentemente.

- **Contenido de la Vista**:
  - **Botón de Eliminación**: Un botón principal para activar la confirmación de eliminación de cuenta.
  - **Modal de Confirmación**: Incluye un campo para que el usuario ingrese su contraseña y confirme la eliminación de su cuenta.

- **Modal de Confirmación de Eliminación de Usuario**: El modal contiene los siguientes elementos:
  - **Título**: "Eliminar Cuenta".
  - **Contenido**: Texto que solicita al usuario ingresar su contraseña para confirmar la acción. Incluye un campo de entrada para la contraseña, con la opción de mostrar u ocultar la contraseña.
  - **Botones del Footer**: Incluye un botón para cancelar la acción y un botón para confirmar la eliminación de la cuenta.

La vista también maneja errores de validación, como cuando la contraseña ingresada es incorrecta, mostrando mensajes de error apropiados.

