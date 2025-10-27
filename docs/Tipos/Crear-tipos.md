**Documentación del Componente CreateTipos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateTipos` permite la creación de nuevos tipos dentro de la aplicación. Los usuarios pueden ingresar una sigla, nombre, descripción y adjuntar una imagen que se asociará con el nuevo tipo creado. Este componente utiliza Laravel Livewire para la gestión del estado y la interactividad en tiempo real.

## Dependencias
### Modelos
- **TiposModel**: Modelo que representa la entidad `Tipos` en la base de datos.

### Servicios
- **MiddlewareInvoker**: Servicio que verifica si el usuario tiene los permisos necesarios para realizar acciones sobre los tipos.

### Traits
- **WithFileUploads**: Trait de Livewire que facilita la carga de archivos desde el navegador hacia el servidor.
- **VerificacionTrait**: Trait personalizado que permite realizar verificaciones antes de llevar a cabo determinadas acciones.

## Propiedades
- **open** (bool): Indica si el modal de creación está abierto o cerrado.
- **search** (string): Campo de búsqueda utilizado para filtrar los tipos (en este caso no está activamente usado).
- **sigla**, **nombre**, **descripcion**: Propiedades que contienen los valores ingresados para la creación del nuevo tipo.
- **imagen**: Propiedad que contiene la imagen seleccionada para el tipo.

## Métodos
### updated($propertyName)
Valida los datos ingresados en el formulario de creación de manera dinámica conforme el usuario va completando los campos.

### crearTipo()
Abre el modal de creación verificando primero los permisos del usuario.

### openModal()
Comprueba los permisos del usuario mediante el servicio `MiddlewareInvoker` y, si tiene los permisos necesarios, abre el modal de creación.

### save()
Valida todos los datos ingresados y llama al método `registro()` para guardar el nuevo tipo.

### registro()
Almacena la imagen subida, guarda la nueva instancia de `TiposModel` en la base de datos y emite un evento para refrescar la tabla de tipos.

### removeImagen()
Elimina la imagen seleccionada del campo correspondiente.

### close()
Cierra el modal de creación y restablece las propiedades del componente.

### render()
Renderiza la vista `livewire.tipos.create-tipos` para desplegar el modal de creación.

## Vista del Componente
La vista del componente `CreateTipos` se muestra a través de un modal y contiene los siguientes elementos:
- **Campos de Formulario**:
  - **Sigla**: Campo de texto para ingresar la sigla del tipo.
  - **Nombre**: Campo de texto para ingresar el nombre del tipo.
  - **Descripción**: Campo de texto para proporcionar una breve descripción del tipo.
  - **Imagen**: Componente para subir una imagen asociada con el tipo, con vista previa de la imagen seleccionada.
- **Botones**:
  - **Cancelar**: Cierra el modal sin guardar los cambios.
  - **Crear Tipo**: Guarda los datos ingresados y crea un nuevo tipo.
  - **Cargando...**: Indicador visual que aparece mientras se está realizando la operación de guardado.

El modal proporciona una interfaz intuitiva para la creación de nuevos tipos, con validaciones en tiempo real y carga de archivos, mejorando la experiencia del usuario y asegurando la integridad de los datos ingresados.

