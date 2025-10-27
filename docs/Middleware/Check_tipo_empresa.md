**Documentación del Middleware CheckTipoEmpresa**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Método `handle`](#método-handle)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El middleware `CheckTipoEmpresa` se encarga de verificar el tipo de empresa o panel al que está asociado el usuario autenticado, con el objetivo de permitir o restringir el acceso a ciertas secciones de la aplicación. Solo se permite el acceso a los usuarios que tienen un panel activo de tipo `Empresa`, `Estado`, `Controladora`, o `Prestadora` y que pertenecen a una empresa verificada.

### Dependencias
- **Auth**: Utiliza `Illuminate\Support\Facades\Auth` para obtener el usuario actualmente autenticado.
- **EmpresasModel**: Utiliza el modelo `EmpresasModel` para obtener los detalles de la empresa asociada al usuario autenticado.
- **Session**: Utiliza `Illuminate\Support\Facades\Session` para manejar la sesión del usuario.
- **IdHelper**: Utiliza el helper `IdHelper` para obtener el identificador de la empresa seleccionada.

### Método `handle`
El método `handle()` gestiona la solicitud entrante y verifica si el usuario tiene acceso permitido basándose en el tipo de panel asociado.

- **Parámetros**:
  - `$request`: Instancia de `Illuminate\Http\Request` que contiene todos los datos de la solicitud.
  - `$next`: Closure que representa el siguiente paso en la cadena de middleware.
- **Lógica**:
  - Obtiene el usuario autenticado mediante `Auth::user()`.
  - Verifica si el panel del usuario es uno de los siguientes: `Empresa`, `Estado`, `Controladora` o `Prestadora`.
  - Utiliza el helper `IdHelper` para obtener el identificador de la empresa seleccionada y busca la empresa en la base de datos que tenga ese CUIT y que su tipo sea `1`, `2`, `3` o `4`.
  - Si la empresa existe y está verificada, permite que la solicitud continúe.
  - Si la empresa no existe o no está verificada, redirige al formulario de inicio de sesión con un mensaje de error indicando que la cuenta está suspendida.
  - Si el usuario no pertenece a ninguno de los paneles mencionados, se le redirige al dashboard del usuario (`dashboard-usuario`).

### Consideraciones de Uso
- **Tipos de Panel**: Este middleware verifica que el usuario tenga uno de los siguientes tipos de paneles: `Empresa`, `Estado`, `Controladora`, o `Prestadora`. Solo estos usuarios podrán continuar con la solicitud.
- **Verificación de Empresa**: La empresa del usuario debe estar verificada y debe tener un tipo válido (`1`, `2`, `3`, o `4`) para que se le permita acceder.
- **Redirección por Error**: Si la empresa está suspendida o no existe, el middleware redirige al usuario al inicio de sesión (`route('login')`) con un mensaje de error que explica que la cuenta está suspendida.
- **Aplicación del Middleware**: Este middleware debe ser registrado en el archivo de rutas o en el kernel de middleware para proteger rutas específicas que solo deben estar disponibles para usuarios con empresas verificadas.

### Ejemplo de Aplicación
El middleware `CheckTipoEmpresa` es útil para proteger secciones de la aplicación que solo deberían estar disponibles para usuarios que estén asociados a empresas verificadas y activas. Puede ser utilizado en rutas que requieran un nivel de seguridad adicional para garantizar que solo usuarios con cuentas y empresas válidas tengan acceso.

