**Documentación del Middleware CheckUserEstado**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Método `handle`](#método-handle)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El middleware `CheckUserEstado` verifica el estado de la cuenta del usuario autenticado para determinar si tiene acceso permitido a la aplicación. Este middleware se asegura de que los usuarios cuya cuenta está desactivada o eliminada (estado igual a `2`) no puedan acceder al sistema y sean redirigidos al formulario de inicio de sesión con un mensaje de error.

### Dependencias
- **Request**: Utiliza `Illuminate\Http\Request` para acceder a la información de la solicitud actual.
- **Closure**: Utiliza `Closure` para pasar la solicitud al siguiente middleware o controlador si se cumplen las condiciones.
- **Response**: Utiliza `Symfony\Component\HttpFoundation\Response` para definir el tipo de respuesta devuelta.

### Método `handle`
El método `handle()` se encarga de procesar la solicitud entrante y verificar el estado del usuario autenticado.

- **Parámetros**:
  - `$request`: Instancia de `Illuminate\Http\Request` que contiene todos los datos de la solicitud.
  - `$next`: Closure que representa el siguiente paso en la cadena de middleware.
- **Lógica**:
  - Obtiene el usuario autenticado mediante `$request->user()`.
  - Verifica si el usuario tiene el estado igual a `2`, lo cual indica que la cuenta está desactivada o eliminada.
  - Si el estado del usuario es `2`:
    - Se invalida la sesión actual utilizando `$request->session()->invalidate()`.
    - Se redirige al formulario de inicio de sesión con un mensaje de error que indica que la cuenta ha sido eliminada.
  - Si el usuario tiene un estado diferente, la solicitud se pasa al siguiente middleware/controlador.

### Consideraciones de Uso
- **Estado del Usuario**: El middleware verifica si el usuario tiene un estado igual a `2`. Este estado se utiliza para indicar que la cuenta está eliminada o desactivada, impidiendo que el usuario continúe utilizando la aplicación.
- **Invalidación de la Sesión**: Si el usuario tiene estado `2`, el middleware invalida la sesión actual para evitar cualquier actividad adicional.
- **Redirección y Mensajes de Error**: El middleware redirige al usuario al inicio de sesión (`route('login')`) con un mensaje de error que explica que la cuenta ha sido eliminada y solicita la creación de una nueva cuenta.
- **Protección de Seguridad**: Este middleware es útil para proteger rutas que solo deben estar disponibles para usuarios activos. Al asegurarse de que los usuarios con cuentas desactivadas sean redirigidos fuera de la aplicación, se mantiene la integridad y seguridad del sistema.

