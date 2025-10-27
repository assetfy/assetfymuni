# Documentación del Componente Livewire: CreateControlesActivos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Reglas de Validación](#reglas-de-validación)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `CreateControlesActivos` permite crear y asignar nuevos controles a un activo específico. Los usuarios pueden seleccionar controles faltantes, proporcionar detalles de fechas de inicio y finalización, y cargar imágenes asociadas al control. Este componente es parte del sistema de gestión de activos, facilitando la administración de controles específicos y los registros de sus aplicaciones.

## Propiedades

- **open**: Determina si el modal de creación de controles está abierto.
- **id_control**: Almacena el ID del control seleccionado.
- **id_activo**: Almacena el ID del activo al que se aplicará el control.
- **id_tipo_activo**, **id_categoria_activo**, **id_subcategoria_activo**: Contienen la información relacionada con el tipo, la categoría y la subcategoría del activo.
- **fecha_inicio**, **fecha_final**: Fechas asociadas al inicio y final del control.
- **imagen**: Almacena las imágenes que se cargarán para el control.
- **controlesFaltantes**: Lista de controles faltantes para el activo.

## Reglas de Validación
Las reglas de validación utilizadas por el componente incluyen:

- **id_control**: Requerido.
- **id_activo**: Requerido.
- **id_tipo_activo, id_categoria_activo, id_subcategoria_activo**: Requeridos.
- **fecha_inicio**: Requerida, debe ser una fecha y no puede ser mayor a la fecha actual.
- **fecha_final**: Requerida, debe ser una fecha mayor o igual a `fecha_inicio` y no puede ser mayor a la fecha actual.
- **imagen**: Cada archivo debe ser una imagen válida (jpeg, png, jpg, gif, svg).

## Métodos

### openModal
Este método abre el modal de creación y carga los datos del activo seleccionado.

### save
Valida los datos proporcionados, almacena las imágenes y crea un registro en la base de datos que representa el control aplicado al activo. Posteriormente, cierra el modal y refresca la vista.

### validarFechas
Este método valida las fechas proporcionadas, asegurándose de que la fecha de finalización sea mayor o igual a la fecha de inicio y que ninguna de las fechas sea mayor que la fecha actual.

### storeImages
Este método almacena las imágenes cargadas en el almacenamiento local y devuelve las rutas donde fueron guardadas.

### createActivosControles
Crea un nuevo registro en la tabla `ActivosControlesModel` con los datos proporcionados, incluyendo los caminos de las imágenes almacenadas.

### updated
Valida un campo específico cada vez que se actualiza su valor.

### mount
Asigna los datos del activo inicial al componente cuando éste es montado.

### value
Asigna los datos del activo y carga los controles faltantes que deben ser aplicados.

### assignarDatosActivo
Este método asigna la información del activo, incluyendo tipo, categoría y subcategoría.

### CargarControlesFaltantes
Carga los controles faltantes según los controles asociados al activo o los controles disponibles en la subcategoría.

### ControlesFaltanteSubcategoria
Obtiene los controles faltantes basados en la subcategoría del activo, para aquellos controles que son obligatorios de carga inicial.

### ControlesFaltantesActivosControles
Obtiene los controles faltantes que no están asociados al activo, basándose en los controles actuales del mismo.

### filtro
Determina si un control requiere una imagen basándose en la propiedad `req_foto` del modelo `ControlesSubcategoriaModel`.

### render
Este método devuelve la vista del componente con todos los datos necesarios para renderizar el formulario.

### close
Cierra el modal y resetea todas las propiedades del componente.

## Vista y Controlador
La vista asociada al componente `CreateControlesActivos` presenta un modal que permite al usuario seleccionar un control y proporcionar la información necesaria para aplicarlo a un activo. A continuación se describe la estructura de la vista:

- **Selección de Control**: Se presenta un dropdown con los controles faltantes que deben ser aplicados al activo.
- **Carga de Imágenes**: Si el control seleccionado requiere fotos, se muestra un campo para cargar múltiples imágenes.
- **Fechas de Inicio y Finalización**: Se presentan dos campos de fecha para que el usuario seleccione el período durante el cual se aplicará el control.
- **Botones de Acción**: Se proporcionan botones para guardar el control o cancelar la operación y cerrar el modal.

## Dependencias
El componente `CreateControlesActivos` depende de los siguientes modelos y elementos:

- **ActivosModel**: Modelo que representa los activos.
- **ControlesModel**: Modelo que representa los controles.
- **ControlesSubcategoriaModel**: Modelo que relaciona los controles con las subcategorías de activos.
- **ActivosControlesModel**: Modelo que representa los controles aplicados a los activos.
- **Livewire**: Utiliza Livewire para manejar la interacción con la vista de forma reactiva.
- **WithFileUploads**: Trait utilizado para manejar la carga de archivos de imagen.
- **ValidationException**: Utilizado para lanzar excepciones de validación cuando las fechas no cumplen con los requisitos especificados.