**Documentación del Middleware CheckPermisosRoles**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Método `handle`](#método-handle)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El middleware `CheckPermisosRoles` tiene como objetivo verificar si el usuario tiene los permisos y roles necesarios para acceder a una determinada ruta. Este middleware se asegura de que solo los usuarios autorizados puedan acceder a ciertas partes de la aplicación, basándose en sus roles asignados y permisos configurados.

### Dependencias
- **Auth**: Utiliza `auth()->user()` para obtener el usuario actualmente autenticado.
- **IdHelper**: Utiliza el helper `IdHelper` para obtener el identificador de la empresa seleccionada.
- **Modelos**:
  - `UsuariosEmpresasModel`: Se usa para obtener la empresa asociada con el usuario autenticado.
  - `AsignacionesRolesModel`: Se usa para obtener los roles asignados al usuario dentro de la empresa.
  - `PermisoPorEmpresaModel`: Se usa para verificar los permisos asignados a las rutas.
  - `PermisosRolesModel`: Se usa para verificar los permisos asignados a los roles del usuario.
  - `RutasModel`: Se usa para obtener información sobre la ruta actual.

### Método `handle`
El método `handle()` gestiona la solicitud entrante y verifica si el usuario tiene acceso permitido a la ruta solicitada.

- **Parámetros**:
  - `$request`: Instancia de `Illuminate\Http\Request` que contiene todos los datos de la solicitud.
  - `$next`: Closure que representa el siguiente paso en la cadena de middleware.
- **Lógica**:
  1. Obtiene el usuario autenticado.
  2. Si el usuario tiene el panel `Usuario`, se le permite continuar sin restricciones.
  3. Para otros tipos de paneles, obtiene el identificador de la empresa y los roles asignados al usuario para esa empresa.
  4. Si no tiene roles asignados, se le permite continuar.
  5. Verifica si la ruta actual está registrada en la base de datos.
  6. Si la ruta no está registrada o no tiene permisos asignados, se le permite continuar.
  7. Si la ruta tiene permisos asignados, verifica si el usuario tiene los permisos requeridos.
  8. Si el usuario tiene los permisos, se le permite continuar; de lo contrario, se le redirige al dashboard de la empresa y se muestra un mensaje de error indicando que no tiene permiso para acceder.

### Consideraciones de Uso
- **Panel del Usuario**: Este middleware verifica si el usuario tiene el panel `Usuario`, permitiéndole acceso directo. Para otros tipos de paneles, se procede a verificar los roles y permisos.
- **Verificación de Roles y Permisos**: Los roles se obtienen a través del modelo `AsignacionesRolesModel`, y se verifica si dichos roles tienen los permisos necesarios para acceder a la ruta actual.
- **Redirección por Error**: Si el usuario no tiene el permiso requerido, se le redirige al `dashboard-empresa` con un mensaje de error indicando la falta de permisos.
- **Aplicación del Middleware**: Este middleware es ideal para proteger rutas específicas en las que solo usuarios con ciertos roles y permisos deben tener acceso. Es importante registrarlo adecuadamente en el kernel de middleware o en las rutas correspondientes para garantizar una correcta protección de las funcionalidades de la aplicación.

