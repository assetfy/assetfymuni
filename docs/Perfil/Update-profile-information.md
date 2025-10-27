**Documentación del Componente UpdateProfileInformation**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

### Descripción General
El componente `UpdateProfileInformation` es un componente de Livewire que permite a los usuarios actualizar la información de su perfil, como el nombre, el correo electrónico y la foto de perfil. La interfaz proporciona una forma intuitiva de modificar la información personal y cargar una nueva foto de perfil. Este componente se utiliza en la sección de perfil del usuario.

### Propiedades
- **$photo**: Almacena la nueva foto de perfil cargada por el usuario.
- **$state**: Almacena la información actual del perfil, incluyendo el nombre, el correo electrónico y la ruta de la foto de perfil.
- **$user**: Instancia del usuario autenticado que está actualizando su información.
- **$photoPreview**: Almacena la vista previa de la foto cargada antes de ser guardada definitivamente.

### Métodos
- **mount()**: Este método se ejecuta al montar el componente y carga la información inicial del usuario, incluyendo el nombre, el correo electrónico y la foto de perfil.

- **updateProfile()**: Valida los datos ingresados por el usuario y actualiza la información del perfil. Si el usuario ha seleccionado una nueva foto, la almacena en la carpeta 'profile-photos' y redimensiona la imagen a 200x200 píxeles antes de guardarla.

- **deleteProfilePhoto()**: Permite eliminar la foto de perfil del usuario y establece el valor de la ruta de la foto como `null`. Luego, guarda los cambios en el modelo de usuario.

- **render()**: Renderiza la vista principal del componente `update-profile-information`.

### Vista del Componente
La vista del componente `update-profile-information` está definida en `livewire.perfil.update-profile-information`. Esta vista contiene un formulario que permite a los usuarios actualizar su información de perfil, como el nombre y el correo electrónico, y también permite cambiar la foto de perfil.

#### Elementos de la Vista
- **Foto de Perfil**: Los usuarios pueden cargar una nueva foto de perfil. La vista previa de la foto se muestra inmediatamente después de seleccionarla, permitiendo al usuario confirmar la imagen antes de guardarla.
- **Nombre**: Campo de texto que permite al usuario actualizar su nombre.
- **Correo Electrónico**: Campo de texto que permite al usuario actualizar su correo electrónico.
- **Mensajes de Verificación**: Si la dirección de correo electrónico no está verificada, se muestra un mensaje que permite al usuario reenviar el correo de verificación.

#### Acciones de la Vista
- **Guardar Cambios**: Un botón que permite al usuario guardar los cambios realizados.
- **Eliminar Foto**: Un botón que permite al usuario eliminar su foto de perfil actual.

### Reglas de Validación
- **name**: Requerido, debe ser un string con un máximo de 255 caracteres.
- **photo**: Opcional, debe ser un archivo de tipo `jpg`, `jpeg` o `png` y no debe superar 1MB de tamaño.

### Funcionalidades Adicionales
- **Intervention Image**: Este componente utiliza la biblioteca `Intervention Image` para redimensionar las fotos cargadas a 200x200 píxeles antes de almacenarlas. Esto asegura que todas las fotos de perfil tengan un tamaño uniforme, mejorando la consistencia visual en la aplicación.
- **Verificación de Correo Electrónico**: Si el usuario no ha verificado su correo electrónico, la vista proporciona un enlace para que puedan solicitar un nuevo correo de verificación.

### Notas
Este componente es parte de la funcionalidad de gestión de perfil de usuario en la aplicación y está diseñado para integrarse fácilmente con la librería de `Jetstream` para gestión de perfiles.

