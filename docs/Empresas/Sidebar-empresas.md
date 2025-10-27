**Documentación del componente SidebarEmpresa**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `SidebarEmpresa` es un componente de Livewire que gestiona el menú lateral de navegación para las empresas. Permite a los usuarios acceder a diferentes secciones relacionadas con la empresa, como ubicaciones, servicios, usuarios, permisos y roles, y activos. 

### Propiedades

- **$panelActual**: Almacena el panel actual en el que se encuentra el usuario.
- **$estadoActual**: Almacena el estado actual de la empresa.
- **$empresas**: Contiene la lista de empresas vinculadas al usuario actual.
- **$estado**: Almacena el estado de las empresas.
- **$actividad**: Almacena información relacionada con las actividades de la empresa.
- **$ubicaciones**: Contiene las ubicaciones asociadas a la empresa seleccionada.
- **$tipo**: Almacena el tipo de usuario de la empresa (administrador, apoderado, etc.).

### Métodos

- **render()**: Renderiza la vista del componente `sidebar-empresa`, proporcionando la interfaz del menú lateral de navegación.

- **mount()**: Inicializa el componente al ser montado, cargando los datos del CUIT seleccionado, las ubicaciones y el tipo de usuario de la empresa.

- **datosActualizado()**: Obtiene y almacena el CUIT de la empresa seleccionada, ya sea desde la sesión o desde el usuario autenticado.

- **cargarUbicaciones()**: Carga las ubicaciones asociadas al CUIT de la empresa seleccionada para cargar el menu de ubicaciones en caso de que tenga ubicaciones registradas.

- **tipoUserEmpresa()**: Determina el tipo de usuario de la empresa (por ejemplo, administrador o apoderado) basado en el usuario autenticado, dependiendo de esto mostrara o no otras opciones el sidebar.

- **refreshUbicaciones()**: Refresca la lista de ubicaciones llamando al método `cargarUbicaciones()`.

### Vista del Componente

La vista del componente `SidebarEmpresa` proporciona la interfaz de navegación lateral para la empresa. Utiliza `Alpine.js` para gestionar el estado de los menús desplegables y controlar el flujo de navegación. La vista incluye:

- **Menú Principal**: El menú incluye secciones como Inicio, Ubicaciones, Servicios, Usuarios, Permisos y Roles, y Activos. Cada sección tiene un ícono y un nombre asociado, lo que facilita la navegación por el panel.

- **Submenús Desplegables**: Algunas secciones tienen submenús que se pueden desplegar, como Servicios y Usuarios, para acceder a opciones más específicas.

- **Actualización Dinámica**: Utiliza `wire:poll.30s` para actualizar las ubicaciones cada 30 segundos, asegurando que la información mostrada esté siempre actualizada.

- **Menú Responsive**: La vista incluye comportamiento responsive, lo cual permite a los usuarios ver y navegar por el menú lateral en dispositivos móviles o pantallas pequeñas de manera conveniente.

