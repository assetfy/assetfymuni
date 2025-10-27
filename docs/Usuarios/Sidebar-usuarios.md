**Documentación del Componente SidebarUsuario**

### Tabla de Contenidos
1. [Descripción General](#descripción-general)
2. [Dependencias](#dependencias)
3. [Propiedades](#propiedades)
4. [Métodos](#métodos)
5. [Vista del Componente](#vista-del-componente)
6. [Consideraciones de Uso](#consideraciones-de-uso)
7. [Ejemplos de Uso](#ejemplos-de-uso)

### Descripción General
El componente `SidebarUsuario` es un componente de Livewire que se encarga de la gestión de la barra lateral de navegación para usuarios dentro de la aplicación. Esta barra lateral proporciona acceso a diferentes secciones y funcionalidades, como la visualización de activos, auditorías, ubicaciones, servicios, y parametrizaciones.

### Dependencias
Este componente depende de varios modelos para cargar la información necesaria para la barra lateral:

- **Modelos**:
  - `App\Models\ActivosModel`: Maneja los activos asociados al usuario.
  - `App\Models\AuditoriasModel`: Gestiona las auditorías relacionadas con el usuario.
  - `App\Models\UbicacionesModel`: Almacena las ubicaciones asociadas al usuario.

- **Facades**:
  - `auth()`: Utilizado para obtener al usuario actualmente autenticado.

### Propiedades
- **ubicaciones**: Lista de ubicaciones asociadas al usuario autenticado.
- **auditorias**: Lista de auditorías relacionadas con el usuario autenticado.
- **numeroActivo**: Contador del número de activos asociados al usuario.
- **openMenu**: Controla el menú que está abierto en la barra lateral (por defecto es `null`).
- **openParametrizaciones**: Controla el submenú de parametrizaciones abierto (por defecto es `null`).

### Métodos
- **mount()**: Inicializa el componente cargando la información necesaria del usuario autenticado.
- **render()**: Renderiza la vista del componente, mostrando la barra lateral con las secciones disponibles.
- **loadData($user)**: Carga toda la información relevante para la barra lateral, incluyendo ubicaciones, auditorías, y activos del usuario.
- **cantidadActivo($user)**: Calcula el número de activos asociados al usuario y los almacena en la propiedad `numeroActivo`.
- **loadAuditorias($user)**: Carga las auditorías del usuario autenticado y las asigna a la propiedad `auditorias`.
- **loadUbicaciones($user)**: Carga las ubicaciones del usuario autenticado y las asigna a la propiedad `ubicaciones`.
- **refreshData()**: Refresca todos los datos del usuario volviendo a ejecutar `loadData()`.
- **maybeRefreshData()**: Refresca los datos si no hay un menú abierto actualmente en la barra lateral.

### Vista del Componente
La vista del componente `SidebarUsuario` proporciona diferentes secciones accesibles a través de la barra lateral. Estas secciones incluyen:

- **Inicio**: Permite al usuario volver al tablero principal.
- **Activos**: Ofrece acceso a la lista de activos y tablas de activos.
- **Auditorías**: Permite al usuario ver el estado de las empresas en auditoría.
- **Registrar Empresa**: Opción para registrar nuevas empresas.
- **Servicios**: Acceso a los servicios ofrecidos por la empresa.
- **Ubicaciones**: Permite visualizar las ubicaciones asociadas al usuario.
- **Parametrizaciones**: Sección avanzada que permite configurar varios aspectos de la aplicación, como activos, permisos, atributos, usuarios, y otros parámetros.

### Consideraciones de Uso
- **Actualización de Datos**: La barra lateral incluye actualizaciones automáticas cada 120 segundos en secciones como activos y servicios, para asegurar que la información esté siempre actualizada.
- **Responsividad**: La barra lateral está diseñada para ser responsiva, ofreciendo una buena experiencia de usuario tanto en dispositivos móviles como en escritorios.
- **Estado del Menú**: Los menús pueden abrirse o cerrarse según la navegación del usuario, manteniendo así un flujo de navegación intuitivo.

### Ejemplos de Uso
El componente `SidebarUsuario` puede ser utilizado en cualquier parte de la aplicación donde se necesite una barra lateral de navegación para un usuario autenticado. Este componente permite un acceso rápido a diferentes funcionalidades, haciéndolo ideal para aplicaciones complejas donde el usuario necesita gestionar activos, empresas, y auditorías de manera eficiente.



