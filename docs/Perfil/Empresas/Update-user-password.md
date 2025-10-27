**Documentación del Componente UpdateUserPassword**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `UpdateUserPassword` es un componente de Livewire en Laravel que permite a los usuarios cambiar su contraseña actual. Este componente garantiza que la contraseña actual del usuario se valide correctamente antes de permitir el cambio y se asegura de que las nuevas contraseñas cumplan con los requisitos de seguridad establecidos.

## Propiedades

- `$state` (array): Esta propiedad contiene los valores que se utilizan para almacenar la contraseña actual, la nueva contraseña y la confirmación de la nueva contraseña.
  - `current_password`: Contraseña actual del usuario.
  - `password`: Nueva contraseña que se desea establecer.
  - `password_confirmation`: Confirmación de la nueva contraseña.

## Métodos

### `updatePassword()`
Este método se encarga de actualizar la contraseña del usuario. Su funcionamiento se divide en varias etapas:

1. **Validación de los datos de entrada**:
   - Se verifica que la contraseña actual, la nueva contraseña y la confirmación de la nueva contraseña estén presentes y cumplan con los requisitos establecidos (longitud mínima de 8 caracteres y que la nueva contraseña coincida con su confirmación).

2. **Verificación de la contraseña actual**:
   - Se comprueba si la contraseña actual ingresada por el usuario coincide con la almacenada en la base de datos. Si no coincide, se lanza una excepción de validación.

3. **Actualización de la contraseña**:
   - Si la contraseña actual es correcta, se procede a almacenar la nueva contraseña en la base de datos, utilizando `Hash::make()` para encriptarla.

4. **Redirección y Mensaje de Éxito**:
   - Se emite un evento de éxito (`exitos`) y se redirige al usuario a la página de inicio de sesión.

### `render()`
Este método se encarga de devolver la vista del componente. La vista asociada es `livewire.perfil.update-user-password`.

## Vista del Componente

La vista del componente `UpdateUserPassword` proporciona una interfaz interactiva para que los usuarios cambien su contraseña. Utiliza TailwindCSS y Alpine.js para la gestión de eventos y el diseño de la interfaz.

### Estructura de la Vista

- **Formulario de Modificación de Contraseña**:
  La vista incluye un formulario de sección (`<x-form-section>`), que permite a los usuarios introducir la contraseña actual, la nueva contraseña y su confirmación. Cada campo tiene un input que permite mostrar/ocultar la contraseña usando iconos SVG.

- **Campos del Formulario**:
  1. **Contraseña Actual**: Campo para que el usuario introduzca su contraseña actual. Incluye validación en el lado del cliente.
  2. **Nueva Contraseña**: Campo para la nueva contraseña, con un requisito de mínimo 8 caracteres.
  3. **Confirmar Contraseña**: Campo para confirmar la nueva contraseña ingresada.

- **Acciones**:
  - **Guardar**: Un botón para enviar el formulario y actualizar la contraseña.
  - **Mensajes de Éxito**: Al guardar exitosamente, se muestra un mensaje de confirmación.

### Estilos Personalizados

- Se define un estilo `.input-error` para mostrar mensajes de error de validación de forma clara y visible debajo de cada campo del formulario.

