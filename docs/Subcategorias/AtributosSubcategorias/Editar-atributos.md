**Documentación del Componente EditAtributosSubcategoria**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente **EditAtributosSubcategoria** permite a los usuarios editar los atributos de una subcategoría, como si el atributo es único y si es obligatorio. Este componente se utiliza dentro del flujo de gestión de atributos para subcategorías y facilita la actualización de estos valores mediante un formulario modal.

## Dependencias
### Namespaces y Paquetes Utilizados
- **App\Models\AtributosModel**: Modelo que representa los atributos.
- **App\Models\AtributosSubcategoriaModel**: Modelo para manejar los atributos de las subcategorías.
- **App\Models\SubcategoriaModel**: Modelo para la información de las subcategorías.
- **App\Models\CategoriaModel**: Modelo que representa las categorías.
- **App\Models\TiposModel**: Modelo para los tipos relacionados.
- **App\Traits\VerificacionTrait**: Trait que permite verificar los permisos del usuario.
- **Livewire\Component**: Proporciona la funcionalidad para construir componentes interactivos en tiempo real.
- **App\Services\MiddlewareInvoker**: Utilizado para verificar permisos antes de realizar acciones.

## Propiedades
- **$open**: Determina si el modal está abierto o cerrado.
- **$updateObligatorio**: Propiedad que representa si el atributo es obligatorio para la carga inicial.
- **$updateUnico**: Propiedad que indica si el atributo debe ser único.
- **$subcategorias, $categoria, $tipos**: Representan la información relacionada con la subcategoría, categoría y tipo respectivamente.
- **$atributoNombre**: Nombre del atributo que se está editando.
- **$atributoSubcategoria**: Instancia del modelo **AtributosSubcategoriaModel** con el atributo subcategoría que se está editando.
- **$listeners**: Define los eventos que el componente escucha. En este caso, **openModal** y **atributoSubcategoria**.

## Métodos

### `mount(AtributosSubcategoriaModel $value)`
Inicializa las propiedades del componente con la información de la subcategoría de atributo proporcionada.

### `atributoSubcategoria($data)`
Verifica si el usuario tiene los permisos necesarios para editar un atributo de subcategoría mediante **MiddlewareInvoker::checkPermisosRoles()**. Si tiene permisos, llama al método **openModal()**.

### `openModal($data)`
Busca la información del atributo de subcategoría por el ID proporcionado y abre el modal para editar los datos. Inicializa las propiedades con la información relevante del atributo.

### `guardarCambios()`
Emite el evento **check** para realizar las verificaciones antes de proceder a la actualización.

### `actualizarAtributoSubcategoria()`
Escucha el evento **guardado** para actualizar el atributo de subcategoría.

### `actualizar()`
Valida y guarda los cambios realizados en los atributos de la subcategoría. Después de guardar, cierra el modal y refresca la tabla de Livewire.

### `close()`
Cierra el modal y emite el evento **render** para actualizar la vista.

### `render()`
Renderiza el modal correspondiente al componente, que se encuentra en **livewire.subcategoria.edit-atributos-subcategoria**.

## Modal del Componente
El componente utiliza un modal de **Livewire** para editar los datos del atributo subcategoría. La vista incluye los siguientes elementos:

- **Nombre del Atributo**: Muestra el nombre del atributo en la parte superior del modal.
- **Tipo, Categoría y Subcategoría**: Muestra información del tipo, categoría y subcategoría relacionados con el atributo de subcategoría. Se presentan de manera separada y centrada en columnas.
- **Formulario de Edición**:
  - **Campo "Obligatorio Inicial"**: Permite al usuario seleccionar si el atributo es obligatorio para la carga inicial.
  - **Campo "Carga Inicial"**: Permite seleccionar si el atributo es único.
- **Botones de Acción**:
  - **Cancelar**: Cierra el modal sin realizar cambios.
  - **Actualizar**: Guarda los cambios realizados en el atributo de subcategoría.
- **Indicador de Carga**: Muestra un mensaje de "Cargando..." mientras se procesan los cambios.

El modal se encarga de recolectar los datos actualizados del atributo subcategoría y permite a los usuarios aplicar estos cambios desde la interfaz de usuario.

