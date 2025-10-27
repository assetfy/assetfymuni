**Documentación del Middleware CheckUserTipo**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Método `handle`](#método-handle)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El middleware `CheckUserTipo` verifica el tipo de usuario que realiza una solicitud para determinar si tiene los permisos necesarios para acceder a una sección específica de la aplicación. Este middleware permite diferenciar entre usuarios administradores y usuarios con menos privilegios, redirigiendo o bloqueando el acceso según el tipo de usuario.

### Dependencias
- **Request**: Utiliza `Illuminate\Http\Request` para acceder a la información de la solicitud actual.
- **Closure**: Utiliza `Closure` para pasar la solicitud al siguiente middleware o controlador si se cumplen las condiciones.

### Método `handle`
El método `handle()` se encarga de procesar la solicitud entrante y tomar decisiones basadas en el tipo de usuario.

- **Parámetros**:
  - `$request`: Instancia de `Illuminate\Http\Request` que contiene todos los datos de la solicitud.
  - `$next`: Closure que representa el siguiente paso en la cadena de middleware.
- **Lógica**:
  - Obtiene el usuario autenticado mediante `$request->user()`.
  - Si el usuario es del tipo `1` (Administrador), se permite continuar con la solicitud.
  - Si el usuario es del tipo `2`, se le redirige al dashboard de usuario con un mensaje de error que indica que no tiene permiso para acceder a la sección solicitada.
  - Si el usuario no cumple ninguna de las condiciones, se lanza un error `403` indicando que no tiene los permisos necesarios.

### Consideraciones de Uso
- **Tipos de Usuario**: El middleware está diseñado para distinguir entre dos tipos de usuarios:
  - **Administrador (tipo 1)**: Tiene acceso total y puede pasar al siguiente middleware/controlador.
  - **Usuario Regular (tipo 2)**: Se le restringe el acceso y se le redirige al dashboard de usuario.
- **Errores de Permisos**: Si un usuario no está autenticado o no cumple con los criterios necesarios, se lanza una respuesta de error `403` indicando que no tiene los permisos necesarios.
- **Mensajes de Error**: En caso de redirección, se pasa un mensaje de error a la sesión con la clave `'error'`, lo cual facilita la comunicación del motivo del bloqueo al usuario.

### Ejemplo de Aplicación
El middleware `CheckUserTipo` es útil para proteger rutas que solo deben ser accesibles para usuarios administradores. Asegúrate de registrar este middleware en el archivo de rutas o en el kernel de middleware para aplicarlo correctamente a las rutas que requieren estas verificaciones de permisos.