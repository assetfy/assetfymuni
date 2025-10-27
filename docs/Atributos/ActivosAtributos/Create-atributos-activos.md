# Documentación del Componente Livewire: CreateAtributosActivos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CreateAtributosActivos` permite a los usuarios agregar nuevos atributos a los activos existentes en el sistema. Proporciona una interfaz para seleccionar atributos disponibles y asociarlos con un activo específico, incluyendo campos adicionales como valores numéricos y descripciones.

## Propiedades

- **id_categoria, id_subcategoria, id_tipo**: Identificadores utilizados para determinar la categoría, subcategoría y tipo del atributo que se está asociando al activo.
- **atributos**: Arreglo que contiene los atributos disponibles para ser seleccionados.
- **id_atributo, id_activo, id_subcategoria_activo, id_categoria_activo, id_tipo_activo**: Identificadores que se utilizan para almacenar la información del atributo y del activo durante la creación de la relación.
- **campo, campo_numerico**: Campos adicionales que permiten definir valores específicos para el atributo seleccionado.
- **atributosDisponibles**: Lista de atributos disponibles según la subcategoría, categoría y tipo especificado.
- **open**: Estado del modal para abrir o cerrar la vista de creación de atributos.

## Métodos

### openModal($data)
Este método se ejecuta cuando se recibe el evento **openModal**. Permite cargar la información del activo a partir del identificador recibido y preparar el modal para agregar atributos.

### value(ActivosAtributosModel $value)
Este método inicializa los valores de la propiedad **atribu** con los datos del modelo proporcionado, y se carga la información de las categorías, subcategorías y tipos disponibles.

### render()
Renderiza la vista del componente y carga los atributos disponibles que coincidan con la categoría, subcategoría y tipo definidos. Estos atributos son pasados a la vista para ser seleccionados por el usuario.

### save()
Valida los datos ingresados y llama al método **CrearRegistro** para crear la relación entre el activo y el atributo. Luego limpia los campos y cierra el modal.

### CrearRegistro()
Este método crea un nuevo registro en la base de datos en la tabla `activos_atributos` utilizando los valores ingresados por el usuario.

### close()
Este método resetea todas las propiedades del componente y cierra el modal.

### validateInput()
Valida los datos del formulario antes de proceder a guardar la información, garantizando que todos los campos requeridos estén completos y sean correctos.

## Vista y Controlador
El controlador principal del componente es la clase `CreateAtributosActivos`, que maneja la lógica de selección y creación de atributos asociados a un activo. La vista proporciona un modal con los siguientes elementos:

- **Selección de Atributos**: Lista de atributos faltantes disponibles para agregar al activo.
- **Campos Adicionales**: Cuando se selecciona un atributo, se muestran campos adicionales donde el usuario puede ingresar valores específicos.
- **Botones de Acción**: Se incluyen botones para guardar los nuevos atributos o cancelar la operación.

## Dependencias
El componente `CreateAtributosActivos` depende de los siguientes modelos y elementos:

- **ActivosAtributosModel**: Modelo principal que maneja la información de los atributos asociados a los activos.
- **AtributosModel**: Modelo que gestiona la información de los atributos disponibles en el sistema.
- **AtributosSubcategoriaModel**: Modelo que relaciona los atributos con las subcategorías y categorías.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.
- **Session**: Para almacenar información relevante entre diferentes sesiones de usuario.

Este componente facilita la gestión y asociación de atributos específicos a los activos, proporcionando una interfaz dinámica y de fácil uso. Esto permite que los usuarios puedan extender las características de los activos según las necesidades específicas del sistema.