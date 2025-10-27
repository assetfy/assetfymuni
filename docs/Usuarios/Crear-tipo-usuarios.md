**Documentación del Componente CreateTiposUsuarios**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateTiposUsuarios` permite crear un nuevo tipo de usuario a través de un modal que presenta un formulario interactivo. Utiliza Livewire para gestionar el estado del componente y los eventos, permitiendo a los usuarios agregar un tipo de usuario con campos como el nombre y la descripción.

## Dependencias
El componente depende de los siguientes elementos:
- **Livewire**: Para la gestión del estado del componente y la comunicación en tiempo real.
- **TiposUsuariosModel**: Modelo que representa la tabla `tipos_usuarios` en la base de datos.

## Propiedades
- **search** (string): Propiedad utilizada para almacenar el texto de búsqueda.
- **open** (bool): Indica si el modal para crear un nuevo tipo de usuario está abierto.
- **nombre** (string): Nombre del tipo de usuario que se está creando.
- **descripcion** (string): Descripción del tipo de usuario que se está creando.

## Métodos
### updated($propertyName)
Valida la propiedad que se ha actualizado de manera individual.

### save()
Este método se encarga de validar y guardar la información ingresada en el formulario. Luego de la validación, crea una nueva instancia del `TiposUsuariosModel` y resetea los campos del formulario. También emite los eventos `render` y `lucky` para notificar que el tipo de usuario se ha creado correctamente.

### render()
Renderiza la vista del componente, que es `livewire.usuarios.create-tipos-usuarios`.

### close()
Reinicia las propiedades `nombre` y `descripcion` y cierra el modal estableciendo la propiedad **open** en `false`.

## Vista del Componente
La vista del componente `CreateTiposUsuarios` se presenta en un modal que contiene el formulario para crear un nuevo tipo de usuario. La interfaz incluye los siguientes elementos:
- **Botón de apertura del modal**: Un botón primario que permite abrir el modal para crear un nuevo tipo de usuario.
- **Formulario del modal**:
  - **Campo Nombre**: Un campo de texto que permite al usuario ingresar el nombre del tipo de usuario.
  - **Campo Descripción**: Un campo de texto que permite al usuario ingresar la descripción del tipo de usuario.
  - **Errores de validación**: Se muestra un mensaje de error si la validación falla en cualquiera de los campos.
- **Botones del modal**:
  - **Botón Cancelar**: Cierra el modal sin guardar los cambios.
  - **Botón Crear Nueva Tipo de Usuario**: Valida los datos y crea un nuevo tipo de usuario si la validación es exitosa.
  - **Indicador de carga**: Un indicador visual que aparece cuando se está procesando la creación del nuevo tipo de usuario.

