**Documentación del componente EmpresasVista**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `EmpresasVista` es un componente de Livewire basado en `LivewireTable` de la librería RamonRietdijk. Su propósito es presentar una tabla de empresas con información relevante sobre las solicitudes de alta. El componente incluye funcionalidades de búsqueda, ordenamiento, y actualización de estados para gestionar la aprobación o rechazo de dichas solicitudes. Los estados pueden ser modificados directamente desde la tabla utilizando controles select.

### Propiedades

- **\$model**: Define el modelo subyacente que se utilizará en la tabla, en este caso, `EmpresasModel`.
- **\$title**: Define el título de la vista, que en este componente es "Solicitudes de alta".
- **\$createForm**: Utilizada para manejar el evento que crea un formulario (aunque no está detallado en el código proporcionado).
- **\$useSelection**: Controla si se permite la selección de filas en la tabla. En este componente, está deshabilitado (valor `false`).
- **\$listeners**: Define los eventos que escucha el componente. En este caso, `updateEstado` para manejar cambios de estado de una empresa.
- **\$loadingEstados**: Propiedad utilizada para manejar el estado de carga de cada empresa durante la actualización de estado.

### Listeners

- **updateEstado**: Este listener se activa cuando el usuario cambia el estado de una empresa mediante un control `select`. Permite actualizar la información de la empresa de manera dinámica sin necesidad de recargar la página.

### Métodos

- **query()**: Define la consulta que se utiliza para generar los datos de la tabla. En este caso, se buscan empresas que tengan la autorización `admin` y se cargan sus relaciones con los usuarios apoderados.

- **columns()**: Define las columnas que se mostrarán en la tabla. Las columnas son:
  - **Nombre**: Muestra el nombre del usuario apoderado relacionado con la empresa.
  - **Empresa**: Muestra la razón social de la empresa.
  - **Cuit**: Muestra el CUIT de la empresa.
  - **Estado**: Permite al usuario modificar el estado de la empresa mediante un control `select` cuando la empresa está en "En Revisión", o muestra un botón indicando el estado actual si ya se ha decidido.
  - **Acciones**: Muestra un botón que permite descargar la constancia de AFIP de la empresa.

- **updateEstado(\$estado, \$id)**: Actualiza el estado de una empresa específica. Además de cambiar el estado, llama a los métodos auxiliares `panel()` y `actualizarEstadoUsuarioEmpresa()` para realizar actualizaciones adicionales según el nuevo estado.

- **panel(\$cuit, \$estado)**: Si el estado se cambia a "Rechazado", este método actualiza el panel de los usuarios de la empresa a "Usuario".

- **actualizarEstadoUsuarioEmpresa(\$nuevoEstado, \$cuit)**: Actualiza el estado del usuario apoderado relacionado con la empresa si está asignado como tal.

### Vista del Componente

El componente `EmpresasVista` genera una tabla que incluye los siguientes elementos:

- **Nombre del Usuario Apoderado**: Muestra el nombre del usuario que tiene el rol de apoderado para la empresa.
- **Razón Social de la Empresa**: Presenta el nombre de la empresa correspondiente a la solicitud de alta.
- **CUIT**: Muestra el CUIT de la empresa.
- **Estado**: Dependiendo del estado actual de la empresa, el usuario puede seleccionar "Aceptar" o "Rechazar" si el estado está en "En Revisión". Una vez aprobado o rechazado, se muestra un botón indicando el estado final.
- **Acciones**: Un botón que permite al usuario descargar la constancia de AFIP de la empresa para su verificación.

El componente maneja la interacción dinámica mediante `Livewire.dispatch()` para actualizar el estado de las empresas sin necesidad de recargar la página. Además, se asegura de mantener actualizada la información relacionada, como el estado de los usuarios y los permisos de acceso. Esto proporciona una experiencia interactiva y eficiente para la gestión de solicitudes de alta.

