# Documentación del Componente Livewire: UsuariosEmpresas

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Filtros](#filtros)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `UsuariosEmpresas` es un componente Livewire que permite la gestión de los usuarios de una empresa, como su tipo, estado y permisos. Está basado en la clase `LivewireTable` del paquete `RamonRietdijk\LivewireTables`, lo cual proporciona funcionalidades avanzadas para mostrar y gestionar tablas en vivo. Además, se utilizan los listeners para realizar acciones específicas como la actualización del estado del usuario o del tipo de usuario.

## Propiedades
- **\$id**: Almacena el ID del componente actual.
- **\$usuarios**: Contiene la lista de usuarios de la empresa.
- **\$user**: Almacena información del usuario autenticado.
- **\$userId**: Identificador del usuario a gestionar.
- **\$permisosUser**: Permisos asignados al usuario.
- **\$cuit**: Almacena el CUIT de la empresa.
- **\$loadingEstados**: Lista para manejar el estado de carga para acciones específicas.

## Listeners
- **updateEstadoUsuario**: Vincula el método `updateEstadoUsuario` para actualizar el estado de un usuario.
- **updateTipoUsuario**: Vincula el método `updateTipoUsuario` para actualizar el tipo de usuario.
- **updateRepresentante**: Vincula el método `updateRepresentante` para asignar o remover un representante técnico.

## Filtros
El componente implementa filtros de selección que permiten filtrar los resultados en la tabla según el tipo de usuario, estado, y si es representante técnico.
- **Tipo de Usuario**: Filtra por tipo (Común o Administrador).
- **Estado**: Filtra por estado del usuario (Habilitado o Deshabilitado).
- **Representante Técnico**: Filtra por si el usuario es representante técnico (Sí o No).

## Métodos

### asignar()
Este método asigna el valor del CUIT de la empresa usando el helper `IdHelper`. Además, obtiene el usuario autenticado con `auth()->user()`.

### query()
Define la consulta principal que se ejecutará para obtener los usuarios de la empresa, excluyendo aquellos que tengan el cargo de "Apoderado". La consulta también utiliza la relación `permisos` para cargar los permisos del usuario actual.

### updateTipoUsuario(\$id, \$tipo)
Este método actualiza el tipo de usuario identificado por `\$id` con el tipo proporcionado en `\$tipo`.

### updateEstadoUsuario(\$id, \$estado)
Actualiza el estado del usuario con el ID proporcionado. Permite habilitar o deshabilitar a los usuarios.

### updateRepresentante(\$id, \$representante)
Actualiza el valor de `es_representante_tecnico` para el usuario especificado, lo que le permite ser o dejar de ser representante técnico.

### crearUsuario()
Este método despacha un evento para abrir el modal de creación de usuario en la vista correspondiente, llamando al componente `create-usuario`.

## Vista del Componente
El componente genera una vista con una tabla donde se muestran los usuarios de la empresa y sus detalles, así como las acciones disponibles, como editar el tipo de usuario, actualizar el estado, asignar permisos, y establecer un representante técnico. Los botones y los selectores en la tabla permiten realizar estas acciones directamente desde la interfaz.