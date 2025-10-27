# Documentación del Componente Livewire: CreateAtributos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CreateAtributos` permite crear nuevos atributos para un sistema de gestión de activos. Los atributos pueden tener diferentes tipos de campos y pueden estar asociados con unidades de medida específicas según el tipo seleccionado.

## Propiedades

- **open**: Estado del modal para abrir o cerrar la vista de creación de atributos. Valor inicial: **false**.
- **nombre**: Nombre del atributo que se va a crear.
- **tipo_campo**: Tipo de campo que tiene el atributo (Ej: numérico, texto).
- **descripcion**: Descripción del atributo.
- **unidad_medida**: Unidad de medida asociada al atributo.
- **categorias2**: Lista de unidades de medida disponibles según el tipo de campo.

## Métodos

### crearAtributos()
Este método se activa mediante un listener y verifica si el usuario tiene permisos para crear atributos. Si los tiene, abre el modal para crear un nuevo atributo.

### save()
Este método valida los datos ingresados por el usuario y luego crea un nuevo atributo en la base de datos utilizando el modelo `AtributosModel`. Posteriormente, actualiza la tabla Livewire correspondiente para mostrar el nuevo registro.

### TipoId($value)
Este método carga las unidades de medida disponibles para un tipo de campo seleccionado.

### updated($propertyName)
Este método se encarga de validar los campos a medida que se actualizan sus valores.

### close()
Este método reinicia las propiedades del componente y cierra el modal de creación de atributos.

### render()
Renderiza la vista del componente y proporciona los datos necesarios, como los tipos de campos disponibles y otros atributos, para la interfaz del usuario.

## Vista y Controlador
El controlador principal del componente es la clase `CreateAtributos`, que maneja la lógica para la creación de atributos en el sistema. La vista proporciona un modal con los siguientes elementos:

- **Campos para Creación**: Incluye campos de texto para el nombre y la descripción del atributo, así como selectores para el tipo de campo y la unidad de medida.
- **Botones de Acción**: Se incluyen botones para crear el atributo o cancelar la operación.
- **Validación Dinámica**: Los campos se validan dinámicamente a medida que el usuario los llena.

## Dependencias
El componente `CreateAtributos` depende de los siguientes modelos y elementos:

- **AtributosModel**: Modelo principal que maneja la información de los atributos en el sistema.
- **TiposCamposModel**: Modelo que proporciona los diferentes tipos de campos que puede tener un atributo.
- **UnidadModel**: Modelo que maneja las unidades de medida asociadas a los atributos.
- **Livewire**: Utiliza Livewire para manejar la lógica del componente y la comunicación con la vista.
- **MiddlewareInvoker**: Servicio utilizado para verificar los permisos del usuario antes de permitir la creación de un atributo.

Este componente es esencial para gestionar la creación de nuevos atributos, permitiendo personalizar y enriquecer la información de los activos según las necesidades del sistema.