# Documentación del Componente Livewire: CreateNuevoAtributosActivos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CreateNuevoAtributosActivos` permite a los usuarios agregar nuevos atributos a los activos existentes en el sistema. Proporciona una interfaz para seleccionar atributos disponibles y asociarlos con un activo específico, incluyendo campos adicionales como valores numéricos y descripciones.

## Propiedades

- **open**: Estado del modal para abrir o cerrar la vista de creación de atributos. Valor inicial: **false**.
- **selectedAtributos**: Arreglo que contiene los atributos seleccionados por el usuario.
- **id_categoria, id_subcategoria, id_tipo, id_atributo**: Identificadores utilizados para determinar la categoría, subcategoría, tipo y atributo que se está asociando al activo.
- **atributos, atributosFaltantes**: Listas de atributos disponibles y faltantes para ser seleccionados.
- **id_activo, id_subcategoria_activo, id_categoria_activo, id_tipo_activo**: Identificadores que se utilizan para almacenar la información del activo durante la creación de la relación.
- **campo, campo_numerico**: Campos adicionales que permiten definir valores específicos para el atributo seleccionado.

## Métodos

### openModal($data)
Este método se ejecuta cuando se recibe el evento **openModal**. Permite cargar la información del activo a partir del identificador recibido y preparar el modal para agregar atributos.

### updated($propertyName)
Este método valida únicamente la propiedad que ha sido actualizada, asegurando que todos los datos ingresados cumplan con las reglas de validación establecidas.

### mount(ActivosModel $activo)
Inicializa el componente asignando los atributos del activo proporcionado y carga los atributos faltantes para ese activo específico.

### asignarAtributo($activo)
Este método asigna los valores del activo recibido a las propiedades del componente y determina los atributos faltantes que se pueden agregar al activo.

### cargaAtributosSinActivos($activo)
Carga los atributos faltantes cuando el activo no tiene atributos asociados. Filtra los atributos que no son obligatorios.

### cargaAtributosConActivos($atributosActivos, $activo)
Carga los atributos faltantes cuando el activo ya tiene algunos atributos asociados, excluyendo los que ya están presentes.

### cargarAtributosFaltantes($atributos)
Este método establece los atributos faltantes en la propiedad **atributosFaltantes** para ser mostrados en la vista.

### render()
Renderiza la vista del componente y carga las listas de tipos, categorías, subcategorías, atributos y campos para ser utilizados en la interfaz del usuario.

### save()
Valida los datos ingresados y crea la relación entre el activo y los atributos seleccionados. Luego limpia los campos y cierra el modal.

### close()
Resetea todas las propiedades del componente y cierra el modal. También recarga la página para actualizar los cambios realizados.

### recargarPagina()
Recarga los datos de la sesión para asegurar que la información de tipos esté actualizada.

## Vista y Controlador
El controlador principal del componente es la clase `CreateNuevoAtributosActivos`, que maneja la lógica de selección y creación de atributos asociados a un activo. La vista proporciona un modal con los siguientes elementos:

- **Selección de Atributos**: Lista de atributos faltantes disponibles para agregar al activo.
- **Campos Adicionales**: Cuando se selecciona un atributo, se muestran campos adicionales donde el usuario puede ingresar valores específicos.
- **Botones de Acción**: Se incluyen botones para guardar los nuevos atributos o cancelar la operación.

## Dependencias
El componente `CreateNuevoAtributosActivos` depende de los siguientes modelos y elementos:

- **ActivosAtributosModel**: Modelo principal que maneja la información de los atributos asociados a los activos.
- **AtributosModel**: Modelo que gestiona la información de los atributos disponibles en el sistema.
- **AtributosSubcategoriaModel**: Modelo que relaciona los atributos con las subcategorías y categorías.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.
- **Session**: Para almacenar información relevante entre diferentes sesiones de usuario.

Este componente facilita la gestión y asociación de atributos específicos a los activos, proporcionando una interfaz dinámica y de fácil uso. Esto permite que los usuarios puedan extender las características de los activos según las necesidades específicas del sistema.

