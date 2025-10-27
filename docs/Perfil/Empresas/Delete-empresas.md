**Documentación del componente DeleteEmpresa**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `DeleteEmpresa` es un componente de Livewire que permite a los usuarios eliminar su cuenta de empresa. Este proceso requiere una confirmación mediante la introducción de la contraseña del usuario para garantizar la seguridad de la acción. Una vez que se confirma la eliminación, la empresa se marca como "Baja" y se envía una notificación al usuario.

### Propiedades
- **$confirmingEmpresaDeletion**: Variable booleana que indica si el modal de confirmación de eliminación está activo.
- **$password**: Almacena la contraseña ingresada por el usuario para confirmar la eliminación.
- **$empresa**: Instancia del modelo `EmpresasModel` que representa la empresa a ser eliminada.

### Métodos
- **confirmEmpresaDeletion()**: Activa el modal de confirmación de eliminación de la empresa.

- **deleteEmpresa()**: Realiza la eliminación de la empresa tras validar la contraseña del usuario. Si la contraseña es incorrecta, se muestra un mensaje de error. Si es correcta, se marca la empresa como "Baja" y se envía una notificación de eliminación al usuario.

- **render()**: Renderiza la vista principal del componente, que incluye el formulario de confirmación de eliminación.

### Vista del Componente
La vista asociada al componente `DeleteEmpresa` proporciona una interfaz visual para que los usuarios puedan confirmar la eliminación de su empresa:

- **Sección de Acción**: Contiene un botón para iniciar el proceso de eliminación de la empresa. Al hacer clic en el botón, se activa el modal de confirmación.

- **Modal de Confirmación**: Un modal que solicita al usuario ingresar su contraseña para confirmar la eliminación de la empresa. También muestra información sobre las consecuencias de la eliminación de la cuenta, como la pérdida permanente de los datos.

- **Botones de Acción**: Dentro del modal, hay dos botones: "Cancelar" para cerrar el modal sin eliminar la empresa, y "Eliminar Empresa" para confirmar la eliminación si la contraseña es correcta.

