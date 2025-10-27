# Documentación del Componente Livewire: Editar Controles

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EditarControles` es un componente de Livewire que permite editar la información de un control específico. Este componente es parte del sistema de gestión y se encarga de actualizar el nombre y la descripción del control seleccionado.

## Propiedades
- **\$open**: Booleano que indica si el modal está abierto o cerrado.
- **\$control**: Instancia del modelo `ControlesModel` que representa el control que se está editando.
- **\$updatedNombre**: Valor actualizado del nombre del control.
- **\$updatedDescripcion**: Valor actualizado de la descripción del control.
- **\$controles**: Variable que almacena los datos del control encontrado para editar.

## Métodos
### mount(ControlesModel \$value)
Inicializa las propiedades del componente con los valores del control recibido como parámetro.

### EditarControles(\$value)
Este método se encarga de verificar si el usuario tiene los permisos necesarios para realizar la acción. Si es así, abre el modal para editar el control.

### openModal(\$value)
Busca el control a editar usando el ID proporcionado y abre el modal con la información del control cargada.

### actualizar()
Este método se ejecuta cuando se dispara el evento `guardado` y llama al método `actualizarControl()`.

### actualizarControl()
Valida los datos actualizados y verifica los cambios en el control antes de guardarlos en la base de datos. Posteriormente, emite un evento para refrescar la tabla de controles.

### close()
Cierra el modal y oculta el componente.

### render()
Renderiza la vista del componente para la edición de controles.

## Vista del Componente
La vista del componente contiene un modal que permite editar los detalles de un control. Incluye los siguientes elementos:

- **Nombre**: Campo de entrada para editar el nombre del control, con validación para permitir solo caracteres alfabéticos y ciertos símbolos.
- **Descripción**: Campo de entrada para editar la descripción del control, también con validación.
- **Botón "Cancelar"**: Botón para cerrar el modal sin realizar cambios.
- **Botón "Actualizar"**: Botón para guardar los cambios realizados en el control.

### Estructura de la Vista
El modal contiene los siguientes elementos:
- **Título**: "Editar Control"
- **Contenido**: Campos de edición para el nombre y la descripción del control.
- **Footer**: Botones para cancelar la edición o guardar los cambios realizados.