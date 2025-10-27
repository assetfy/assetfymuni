**Documentación del Middleware CheckTipoUserEmpresa**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Método `handle`](#método-handle)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El middleware `CheckTipoUserEmpresa` está diseñado para verificar el tipo de usuario que está accediendo a la aplicación, especialmente en el contexto de usuarios vinculados con empresas. Si el usuario tiene un tipo específico, se le permite el acceso; de lo contrario, se le redirige al dashboard principal.

### Dependencias
- **Auth**: Utiliza `Illuminate\Support\Facades\Auth` para obtener el usuario actualmente autenticado.
- **UsuariosEmpresasModel**: Utiliza el modelo `UsuariosEmpresasModel` para verificar el tipo de usuario asociado con la empresa actual.
- **Request y Closure**: Utiliza `$request` para acceder a los datos de la solicitud y `$next` para pasar la solicitud al siguiente middleware.

### Método `handle`
El método `handle()` gestiona la solicitud entrante y determina si el usuario tiene el tipo adecuado para acceder a ciertas partes de la aplicación.

- **Parámetros**:
  - `$request`: Instancia de `Illuminate\Http\Request` que contiene todos los datos de la solicitud.
  - `$next`: Closure que representa el siguiente paso en la cadena de middleware.
- **Lógica**:
  - Obtiene el usuario autenticado mediante `Auth::user()`.
  - Utiliza el `UsuariosEmpresasModel` para verificar si el usuario autenticado tiene el tipo `2` asociado a la empresa seleccionada.
  - Si el usuario tiene el tipo `2`, se le permite continuar con la solicitud.
  - Si el usuario no tiene el tipo `2`, se le redirige al dashboard principal mediante `redirect()->route('dashboard')`.

### Consideraciones de Uso
- **Tipos de Usuario**: Este middleware es útil para diferenciar entre diferentes tipos de usuarios dentro de una empresa. Actualmente está configurado para permitir el acceso solo a usuarios con el tipo `2`.
- **Redirección**: Si el usuario no cumple con la condición, se le redirige al dashboard principal, lo cual es útil para proteger ciertas rutas que solo deben estar disponibles para usuarios específicos.
- **Verificación en Base de Datos**: El tipo de usuario se obtiene desde la base de datos a través del modelo `UsuariosEmpresasModel`. Es importante asegurar que los datos estén correctamente sincronizados y que el tipo de usuario sea el correcto en la base de datos.
- **Aplicación del Middleware**: Asegúrate de registrar este middleware correctamente en el archivo de rutas o en el kernel de middleware para que se aplique a las rutas que necesitan verificación del tipo de usuario.

