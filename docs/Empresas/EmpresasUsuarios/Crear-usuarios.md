# Documentación del Componente Livewire: CreateUsuario

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Reglas de Validación](#reglas-de-validación)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CreateUsuario` es un componente Livewire utilizado para crear usuarios y asociarlos con una empresa. Permite ingresar el CUIL del usuario, buscarlo en la base de datos, y si el usuario no está asociado a la empresa, registrar la asociación.

## Propiedades
- **\$open**: Controla si el modal de creación de usuario está abierto o cerrado.
- **\$cargo**: Cargo del usuario.
- **\$legajo**: Legajo del usuario.
- **\$cuit**: CUIT de la empresa a la cual se asociará el usuario.
- **\$cuil**: CUIL del usuario que se desea registrar.
- **\$id_usuario**: ID del usuario encontrado.
- **\$usuario**: Variable que almacena la información del usuario buscado.

## Reglas de Validación
- **cuil**: Requerido, con un máximo de 50 caracteres.

## Listeners
- **openModalCrearUsuario**: Abre el modal para la creación de un nuevo usuario.

## Métodos

### openModalCrearUsuario()
Este método se encarga de abrir el modal para la creación de un usuario nuevo. Verifica los permisos antes de abrir el modal, utilizando el servicio `MiddlewareInvoker`. Si el usuario no tiene permisos, se emite el evento `no-permission`.

### mount()
El método `mount` se ejecuta cuando se instancia el componente. Carga el CUIT de la empresa seleccionada de la sesión o el CUIT del usuario autenticado.

### updated(\$propertyName)
Este método se utiliza para validar la propiedad que está siendo actualizada.

### save()
Este método guarda un nuevo registro de usuario asociado a la empresa. Realiza la validación de los campos y, si es exitosa, crea el registro en la base de datos mediante el modelo `UsuariosEmpresasModel`.

### buscarUsuario()
El método `buscarUsuario` busca un usuario en la base de datos según el CUIL proporcionado. Si el usuario no está asociado a la empresa, se almacena su ID para poder registrarlo posteriormente.

### close()
El método `close` se encarga de cerrar el modal y restablecer las propiedades **cargo**, **legajo** y **cuit**.

### render()
El método `render` renderiza la vista del componente. Se pasan los usuarios, las empresas, y las asociaciones de usuarios con empresas a la vista.

## Vista del Componente
La vista asociada al componente es `livewire.empresas.EmpresasUsuarios.create-usuario`. En la vista, se despliega un formulario donde se puede ingresar el CUIL del usuario y buscarlo en la base de datos.

### Contenido del Modal
- **CUIL**: Campo de texto para ingresar el CUIL del usuario. Incluye un botón para buscar el usuario en la base de datos.
- **Información del Usuario**: Si se encuentra el usuario, se muestra su nombre.
- **Acciones**:
  - **Registrar Usuario**: Botón para registrar al usuario en la empresa, disponible solo si el usuario no está previamente registrado.
  - **Cancelar**: Botón para cerrar el modal.

