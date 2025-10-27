**Documentación del archivo de rutas de Laravel**

Este documento proporciona una descripción del archivo de rutas configurado para una aplicación Laravel, donde se define la estructura de rutas para diferentes tipos de usuarios (administrador, empresa, usuario, estado). El archivo incluye rutas que utilizan middleware para garantizar la autenticación, la autorización y el control de permisos de acceso.

### Secciones del Archivo de Rutas

1. **Rutas de sesión**
   - Se definen rutas para la verificación de email, actualización de sesión y obtención de valores de la sesión.
   - Incluye rutas como `/email/verify` y `email/verify/{id}/{hash}`.

2. **Rutas generales**
   - Define la ruta principal (`/`) que devuelve la vista `welcome`.

3. **Rutas autenticadas con verificación del estado del usuario**
   - Utiliza el middleware `auth:sanctum`, `jetstream.auth_session`, `check.user.estado` y `verified` para garantizar que solo usuarios autenticados y verificados accedan a ciertas rutas.
   - Ejemplos de rutas incluyen `/usuarios/perfiles` y `/ubicaciones/vistas-aperturas`.

4. **Rutas autenticadas con verificación de permisos**
   - Estas rutas están protegidas con el middleware `check.permisos.roles` para controlar el acceso basado en permisos.
   - Ejemplos de rutas incluyen:
     - `/empresas/create-empresas` para crear empresas.
     - `/usuarios/dashboard-usuario` para acceder al dashboard del usuario.
     - `/afip/{cuit}` para verificar información en AFIP.

5. **Rutas para administradores**
   - Se aplica el middleware `check.user.tipo` para restringir el acceso solo a usuarios con privilegios de administrador.
   - Ejemplos de rutas incluyen:
     - `/atributos/create-nuevo-atributos-activos/{id_activo}` para crear nuevos atributos de activos.
     - `/usuarios/tipos-usuarios` para ver los diferentes tipos de usuarios.

6. **Rutas para empresas**
   - Utiliza el middleware `check.tipo.empresa` para garantizar que solo los usuarios que pertenecen a empresas puedan acceder a estas rutas.
   - Ejemplos de rutas incluyen:
     - `/empresas/dashboard-empresa` para acceder al dashboard de la empresa.
     - `/actividad/actividades-extra` para ver actividades adicionales.

7. **Rutas para estado**
   - Estas rutas están destinadas a usuarios con permisos relacionados con "Estado".
   - Incluyen rutas como:
     - `/estado/dashboard-estado` para acceder al dashboard de estado.
     - `/estado/tabla-estado` para ver la tabla de estado.

### Middleware Aplicados

- **auth:sanctum**: Asegura que el usuario esté autenticado mediante el sistema de autenticación de Laravel Sanctum.
- **jetstream.auth_session**: Mantiene la sesión autenticada mediante el paquete Jetstream.
- **check.user.estado**: Middleware que verifica el estado del usuario para garantizar que no esté dado de baja.
- **check.permisos.roles**: Middleware que verifica si el usuario tiene los permisos necesarios para acceder a una ruta en particular.
- **check.user.tipo**: Middleware que garantiza que solo los administradores tengan acceso a determinadas rutas.
- **check.tipo.empresa**: Middleware que restringe el acceso a las rutas específicas de empresa.

### Estructura de Rutas

- Cada ruta se define utilizando el método `Route::get()` y puede tener diferentes vistas asociadas, como `view('dashboard-usuario')`.
- Las rutas también se agrupan según el middleware requerido, lo cual mejora la organización y la seguridad.

### Rutas con Parámetros

- Algunas rutas aceptan parámetros como `{id_activo}`, `{cuit}` o `{solicitud}` para proporcionar información dinámica en las vistas correspondientes. Ejemplos:
  - `/servicios/solicitud-servicios/{id_activo}`: Proporciona información sobre los servicios solicitados para un activo específico.
  - `/afip/{cuit}`: Verifica la información de una empresa utilizando su CUIT.

