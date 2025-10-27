# Documentación del Componente Livewire: CreateControlesSubcategoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Reglas de Validación](#reglas-de-validación)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)

## Descripción General
El componente `CreateControlesSubcategoria` es parte del sistema de gestión de activos y está diseñado para crear nuevos controles asociados a subcategorías de activos. Este componente permite seleccionar un control, tipo, categoría y subcategoría, así como configurar diferentes propiedades para los controles, como la frecuencia y si es obligatorio o requiere fotos.

## Propiedades

- **open**: Controla si el modal de creación está abierto o cerrado.
- **unico**: Indica si el control es único.
- **obligatorio_carga_ini**: Define si el control es obligatorio en la carga inicial.
- **id_control, id_categoria, id_subcategoria, id_tipo**: IDs correspondientes a los controles, categorías, subcategorías y tipos.
- **es_periodico, frecuencia_control, cantidad_estandar, req_foto**: Propiedades adicionales que definen la periodicidad, frecuencia, cantidad estándar y si el control requiere fotos.
- **cat, subcat**: Colecciones para almacenar categorías y subcategorías.
- **controles, tipos**: Colecciones para almacenar los controles y tipos.
- **searchControl, searchTipo, searchCategoria, searchSubcategoria**: Propiedades para realizar la búsqueda de controles, tipos, categorías y subcategorías.

## Reglas de Validación
Las reglas de validación para el componente se definen en la propiedad `$rules` e incluyen:

- **id_control**: Requerido.
- **unico**: Requerido, máximo de 50 caracteres, mínimo de 2.
- **obligatorio_carga_ini, id_tipo, id_categoria, id_subcategoria, es_periodico, req_foto**: Requerido.
- **frecuencia_control, cantidad_estandar**: Requerido y deben ser valores numéricos.

## Métodos

### mount
El método `mount` se llama cuando se monta el componente y carga todos los controles y tipos disponibles. Inicializa las propiedades de categorías y subcategorías como colecciones vacías.

### save
Este método valida los datos y crea un nuevo registro en `ControlesSubcategoriaModel` con los valores proporcionados. También dispara un evento para actualizar la tabla y cierra el modal una vez guardada la información.

### setControl
El método `setControl` asigna el control seleccionado, limpiando el campo de búsqueda asociado.

### setTipo
Este método establece el tipo seleccionado, carga las categorías asociadas y reinicia las selecciones de categoría y subcategoría.

### setCategoria
`setCategoria` se utiliza para establecer la categoría seleccionada, cargar las subcategorías asociadas y reiniciar la selección de subcategoría.

### setSubcategoria
El método `setSubcategoria` establece la subcategoría seleccionada, limpiando el campo de búsqueda asociado.

### close
Este método cierra el modal de creación y reinicia el formulario mediante el método `resetForm`.

### resetForm
`resetForm` reinicia todas las propiedades relacionadas con el formulario, incluyendo las propiedades de búsqueda y selección.

## Vista y Controlador
El componente `CreateControlesSubcategoria` proporciona una interfaz para crear nuevos controles de subcategoría a través de un modal que se abre al hacer clic en el botón correspondiente. Los campos del formulario incluyen selecciones para controles, tipos, categorías y subcategorías, así como campos adicionales para definir si el control es único, la frecuencia, si requiere fotos, entre otros.

El flujo de trabajo del componente incluye:
- Validación de datos antes de guardar.
- Creación de un nuevo registro de control asociado a la subcategoría seleccionada.
- Actualización de la tabla de controles tras la creación del nuevo registro.
- Reinicio de las propiedades para asegurar un formulario limpio para futuras creaciones.

