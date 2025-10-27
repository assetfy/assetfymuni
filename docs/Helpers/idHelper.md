**Documentación del Helper IdHelper**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Métodos](#métodos)
4. [Consideraciones de Uso](#consideraciones-de-uso)

### Descripción General
El `IdHelper` es una clase auxiliar ubicada en `App\Helpers` que proporciona métodos para obtener identificadores relacionados con la empresa seleccionada o el usuario autenticado. Facilita la gestión de identificadores como el CUIT de la empresa o el CUIL del usuario en diversas partes de la aplicación, asegurando un acceso consistente a estos valores.

### Dependencias
Este helper utiliza las siguientes dependencias:

- **Facades**:
  - `Illuminate\Support\Facades\Auth`: Para obtener información del usuario autenticado.
  - `Illuminate\Support\Facades\Session`: Para gestionar la sesión del usuario y acceder al identificador de la empresa seleccionada.

### Métodos

#### identificador()
Este método devuelve el identificador de la empresa seleccionada, si está disponible. Si no hay una empresa seleccionada, devuelve el CUIL del usuario autenticado.

- **Devuelve**: El identificador (CUIT) de la empresa seleccionada o el CUIL del usuario.
- **Lógica**: Primero revisa la sesión para determinar si existe un CUIT de empresa seleccionado. Si no, obtiene el CUIL del usuario autenticado.

#### identificadorParcial()
Este método devuelve un array con el CUIT de la empresa seleccionada o el CUIL del usuario, dependiendo de lo que esté disponible.

- **Devuelve**: Un array con las claves `cuit` y `user`.
  - `cuit`: CUIT de la empresa seleccionada, si existe.
  - `user`: CUIL del usuario, si no hay empresa seleccionada.
- **Lógica**: Revisa si hay un CUIT de empresa seleccionado en la sesión. Si lo hay, devuelve el `cuit` y establece `user` como `null`. Si no, devuelve el CUIL del usuario.

#### idEmpresa()
Este método devuelve el CUIT de la empresa seleccionada. Si no existe, devuelve la entidad del usuario autenticado, si el usuario está autenticado.

- **Devuelve**: El CUIT de la empresa seleccionada o la entidad del usuario autenticado.
- **Lógica**: Utiliza `Session::get()` para obtener el CUIT de la empresa seleccionada. Si no existe, verifica si el usuario está autenticado y devuelve su entidad.

#### identificadoresCompletos()
Este método devuelve un array con los identificadores completos de la empresa y del usuario.

- **Devuelve**: Un array con las claves `cuit` y `user`.
  - `cuit`: CUIT de la empresa seleccionada, si existe.
  - `user`: Instancia del usuario autenticado, si no hay empresa seleccionada.
- **Lógica**: Revisa si hay un CUIT de empresa seleccionado en la sesión. Si lo hay, devuelve `cuit` y establece `user` como `null`. Si no, devuelve al usuario autenticado.

### Consideraciones de Uso
- **Gestor de Identificadores**: El `IdHelper` está diseñado para facilitar la obtención de identificadores relacionados con la empresa y el usuario. Esto es particularmente útil en aplicaciones donde se permite al usuario cambiar entre varias empresas o manejar diferentes perfiles.
- **Manejo de Sesiones**: Los métodos dependen de la sesión (`Session`) para determinar si hay un CUIT de empresa seleccionado. Asegúrate de que el valor de `cuitEmpresaSeleccionado` esté configurado correctamente para evitar problemas.
- **Autenticación**: En todos los métodos, se asume que hay un usuario autenticado. Si se usa en contextos donde el usuario no está autenticado, es posible que ocurra un error. Por lo tanto, se recomienda usar estos métodos solo cuando se sepa que el usuario ha iniciado sesión.
- **Manejo de Valores Nulos**: Se ha implementado una verificación para evitar problemas cuando no existen valores en la sesión o el usuario no está autenticado. Es importante considerar estos escenarios al utilizar los métodos del `IdHelper`.

