# Documentación del Componente Livewire: CargarActividad

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CargarActividad` permite cargar una nueva actividad económica ingresando un código de actividad, nombre, descripción y estado. Aunque este componente está programado, no se utiliza ya que se realizo una carga de todas las actividades por base de datos y solo se edita el estado de las mismas.

## Propiedades

- **COD_ACTIVIDAD**: Código de la actividad a ser registrada.
- **Nombre**: Nombre de la actividad económica.
- **Descripcion**: Descripción de la actividad económica.
- **open**: Controla si el modal de carga está abierto o cerrado. Inicialmente está establecido como **false**.

## Métodos

### updated($propertyName)
Este método se ejecuta cada vez que una propiedad es actualizada. Realiza la validación de la propiedad que ha cambiado mediante `validateOnly()`.

### render()
Retorna la vista asociada al componente `livewire.actividad.cargar-actividad`.

### save()
Método privado que se encarga de guardar la actividad económica. Realiza la validación de los campos y luego crea un nuevo registro en el modelo **ActividadesEconomicasModel**. También dispara el evento **refreshLivewireTable** para actualizar la tabla de actividades.

### close()
Restablece los valores de las propiedades **COD_ACTIVIDAD**, **Nombre** y **Descripcion** y cierra el modal estableciendo **open** a **false**.

## Vista y Controlador
La vista asociada a este componente se encuentra en el archivo `livewire.actividad.cargar-actividad`. Proporciona un formulario para ingresar el código de actividad, el nombre y la descripción, así como el estado de la actividad económica. El controlador principal del componente es la clase `CargarActividad`, que maneja la lógica de negocio y se comunica con los modelos para realizar operaciones en la base de datos.

## Dependencias

El componente `CargarActividad` depende de los siguientes elementos:

- **ActividadesEconomicasModel**: Modelo para manejar los datos de las actividades económicas.
- **SortableTrait**: Trait utilizado para funcionalidades de ordenamiento.
- **VerificacionTrait**: Trait utilizado para verificar la existencia de registros y validar los datos antes de guardar.

