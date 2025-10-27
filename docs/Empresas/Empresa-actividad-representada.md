**Documentación del componente EmpresasActividadRepresentante**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Reglas de Validación](#reglas-de-validación)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General

El componente `EmpresasActividadRepresentante` es un componente de Livewire que gestiona las actividades económicas asignadas a un representante técnico de una empresa. Permite la selección y eliminación de actividades asociadas a un representante, garantizando que las actividades seleccionadas existen en el sistema. El componente utiliza varios modelos para gestionar la información, incluyendo `UsuarioActividadesRepresentadasModel`, `ActividadesEconomicasModel`, `EmpresasActividadesModel`, `User`, y `UsuariosEmpresasModel`.

### Propiedades

- **$id_usuario**: Identificador del usuario asociado al representante técnico.
- **$actividades**: Colección de actividades económicas disponibles para ser asignadas al representante.
- **$usuario**: Información del usuario obtenida de `UsuariosEmpresasModel`.
- **$actividadesCargadas**: Actividades previamente asignadas al representante.
- **$user**: Información del usuario seleccionado.
- **$datos**: Datos adicionales del usuario, incluyendo el nombre.
- **$selectedActividades**: Actividades seleccionadas por el usuario para ser asignadas al representante.
- **$open**: Indica si el modal para editar las actividades está abierto o cerrado.

### Listeners

- **openModal**: Abre el modal para la selección de actividades del representante.
- **openModalEditarRepresentante**: Abre el modal de edición de actividades del representante tras comprobar los permisos del usuario.

### Reglas de Validación

- **selectedActividades**: Debe ser un arreglo y debe contener al menos una actividad seleccionada (`'required|array|min:1'`).

### Métodos

- **openModalEditarRepresentante($data)**: Comprueba si el usuario tiene los permisos necesarios para editar las actividades del representante. Si los permisos son válidos, invoca el método `openModal()`.

- **openModal($data)**: Carga la información del usuario, las actividades previamente asignadas, y las actividades disponibles para ser asignadas. Luego, abre el modal para la edición.

- **cargarDatosEmpresa()**: Obtiene las actividades asignadas al representante técnico y actualiza la propiedad `$selectedActividades` con los códigos de actividad seleccionados.

- **cargarActividades()**: Obtiene las actividades económicas disponibles para la empresa asociada al representante.

- **save()**: Valida las actividades seleccionadas y crea o actualiza los registros en `UsuarioActividadesRepresentadasModel`. Luego cierra el modal.

- **crearRegistro()**: Itera sobre las actividades seleccionadas y crea o actualiza los registros correspondientes en `UsuarioActividadesRepresentadasModel`.

- **close()**: Resetea las actividades seleccionadas y cierra el modal.

- **eliminarRegistro($codigos)**: Elimina los registros de actividades representadas según los códigos proporcionados.

- **toggleActividad($actividadId)**: Añade o elimina una actividad de la lista de actividades seleccionadas.

- **render()**: Renderiza la vista principal del componente, que incluye el modal para la selección de actividades.

### Vista del Componente

La vista del componente `EmpresasActividadRepresentante` incluye los siguientes elementos:

- **Modal para la Selección de Actividades**: Utiliza un `x-dialog-modal` para mostrar un formulario donde se pueden asignar o eliminar actividades del representante técnico. Contiene un listado de actividades disponibles y otro de actividades ya asignadas, con la posibilidad de interactuar mediante checkboxes para seleccionar o deseleccionar actividades.

- **Botones de Acción**: Incluye un botón para guardar los cambios realizados, que valida las actividades seleccionadas y actualiza la base de datos, y un botón para cancelar, que cierra el modal sin realizar cambios.
