# Documentación del Componente Livewire: EmpresasTipoUser

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `EmpresasTipoUser` es un componente Livewire utilizado para gestionar la visualización de los usuarios de una empresa, así como sus permisos, roles, y la asignación de representantes técnicos. Además, el componente permite mostrar un calendario de servicios y gestionar fotos de la empresa.

## Propiedades
- **\$search**: Cadena para buscar usuarios.
- **\$sort**: Campo utilizado para ordenar los resultados (por defecto, `cuit`).
- **\$direction**: Dirección del orden (`desc` por defecto).
- **\$id**: CUIT de la empresa.
- **\$users**: Lista de usuarios.
- **\$empresa**: Datos de la empresa.
- **\$servicios**: Servicios próximos de la empresa.
- **\$serviciosExpirados**: Servicios expirados de la empresa.
- **\$serviceDates**: Fechas de todos los servicios.
- **\$currentYear**: Año actual.
- **\$currentMonth**: Mes actual.
- **\$photo**: Foto de la empresa a subir.
- **\$open**: Controla si el modal está abierto o cerrado.
- **\$usuarios**: Usuarios paginados para mostrar en la vista.

## Listeners
- **render**: Escucha el evento `render` para volver a renderizar el componente.

## Métodos

### mount()
Este método se ejecuta cuando se instancia el componente. Inicializa las propiedades con la información necesaria de la empresa, los usuarios y los servicios, entre otros.

### render()
Renderiza la vista del componente con los usuarios paginados y las imágenes de la empresa. Las imágenes se completan con `null` si hay menos de 4 fotos disponibles.

### cargaUsuariosEmpresa()
Obtiene una colección paginada de los usuarios asociados a la empresa seleccionada (basada en el CUIT). Filtra según la búsqueda y el estado (`Aceptado`).

### datosEmpresa(\$id)
Devuelve la información de la empresa, incluyendo las actividades relacionadas, utilizando el CUIT.

### cargarUsuario()
Obtiene todos los usuarios registrados para ser mostrados en la vista.

### updateRepresentanteTecnico(\$usuarioId, \$opcion)
Actualiza el campo `es_representante_tecnico` del usuario especificado, según la opción proporcionada (`Si` o `No`).

### serviciosCalendario(\$id)
Obtiene los servicios próximos que están confirmados por el cliente y esperan visita, filtrando por la empresa prestadora y ordenándolos por fecha.

### serviciosExpiradosCalendario(\$id)
Obtiene los servicios que ya han expirado y que están confirmados por el cliente, filtrando por la empresa prestadora y ordenándolos por fecha.

### fetchServiceDates()
Devuelve un array con las fechas de todos los servicios (tanto próximos como expirados).

### savePhoto()
Guarda una foto de la empresa en el almacenamiento público y crea un registro en la base de datos para asociar la foto con la empresa.

### updatePermisos(\$usuario)
Despliega un modal para editar los permisos del usuario especificado.

### updateRepresentate(\$usuarioId)
Despliega un modal para actualizar el representante técnico de la actividad.

### fotosEmpresa(\$id)
Obtiene las fotos relacionadas con la empresa utilizando el CUIT proporcionado.

### usuarioTieneRoles(\$usuarioId)
Comprueba si el usuario especificado tiene roles asignados en la empresa.

## Vista del Componente
La vista asociada a este componente es `livewire.empresas.EmpresasUsuarios.empresas-tipo-user`, la cual muestra una lista de usuarios y sus detalles. Además, proporciona botones para actualizar los permisos y roles de los usuarios y muestra un calendario con los servicios próximos y expirados.

La vista incluye una sección de "Equipo" que muestra una lista de los usuarios de la empresa con sus roles y un botón para agregar nuevos usuarios. Además, se puede administrar cada usuario mediante tarjetas interactivas que giran para mostrar las opciones de edición. También incluye un calendario de servicios que lista los servicios próximos.

Se utilizan estilos CSS personalizados para dar un efecto 3D a las tarjetas de los usuarios, que giran al hacer clic para mostrar las opciones de administración.

