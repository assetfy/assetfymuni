**Documentación del componente Empresa**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)
- [Estilos y Scripts](#estilos-y-scripts)

### Descripción General
El componente `Empresa` es un componente de Livewire que permite a los usuarios visualizar y gestionar información relacionada con una empresa. Esto incluye actualizar descripciones, agregar o editar fotos, y eliminar logos asociados a la empresa. Además, cuenta con un modal de confirmación para la eliminación de la cuenta de la empresa.

### Propiedades
- **$id**: Almacena el identificador de la empresa, generalmente el CUIT.
- **$empresa**: Almacena los datos de la empresa seleccionada.
- **$updateDescripcion**: Contiene la descripción actualizada de la empresa para ser modificada.
- **$photo, $photoAgregar, $photoEditar, $logo**: Manejan la subida de imágenes para el perfil y las fotos adicionales de la empresa.
- **$fotoId**: Contiene el identificador de una foto específica seleccionada para edición o eliminación.
- **$search, $sort, $direction**: Permiten realizar búsquedas y ordenar los resultados dentro del componente.

### Métodos
- **mount()**: Inicializa el componente estableciendo la empresa seleccionada y la descripción de la actividad.
- **datosEmpresa($id)**: Obtiene los detalles de la empresa con el CUIT proporcionado.
- **guardarDescripcion()**: Valida y guarda la descripción de la empresa.
- **updatedPhoto()**: Valida las imágenes subidas para agregar, editar o cambiar el logo.
- **savePhoto()**: Almacena la foto proporcionada, ya sea para la empresa, edición de una foto existente o un logo.
- **eliminarFoto()**: Elimina la foto del logo de la empresa.
- **fotoSeleccionada($index)**: Selecciona una foto de la empresa para ser editada.
- **fotosEmpresa($id)**: Retorna todas las fotos de la empresa.
- **render()**: Renderiza la vista principal del componente con las fotos de la empresa.
- **close()**: Cierra los modales y resetea las propiedades relacionadas con la subida de fotos.

### Vista del Componente
La vista del componente `Empresa` proporciona una interfaz para que los usuarios gestionen información y fotos de la empresa:

- **Encabezado de Empresa**: Muestra el logo de la empresa o, si no existe, sus iniciales.
- **Descripción de la Empresa**: Muestra la descripción actual de la empresa junto con un botón para editarla.
- **Galería de Fotos**: Permite agregar, editar o eliminar fotos de la empresa.
- **Eliminación del Perfil de la Empresa**: Incluye un componente adicional para eliminar la empresa.
- **Modales**: Tres tipos de modales, uno para editar la descripción, otro para agregar nuevas fotos, y otro para editar las fotos existentes.

### Estilos y Scripts
- **Estilos Personalizados**:
  - Estilo para las tarjetas de fotos con efectos de sombra y transición.
  - Botones de acción personalizados con transiciones suaves.
  - Estilo adaptativo para dispositivos móviles y pantallas de diferentes tamaños.

- **Scripts**:
  - **Mostrar y Ocultar Modales**: Utiliza `Alpine.js` para manejar la visualización de los modales de edición, agregación y ampliación de imágenes.
  - **Interacción con Imágenes**: Scripts para mostrar y editar imágenes, asegurando una transición suave entre estados.

Este componente proporciona una interfaz completa para la gestión del perfil de la empresa, con el objetivo de facilitar a los usuarios la edición y personalización de los datos y las imágenes de su empresa. Los estilos y scripts incluidos garantizan una experiencia de usuario amigable y adaptativa a diferentes dispositivos.

