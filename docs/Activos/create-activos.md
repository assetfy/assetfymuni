# Documentación del Componente Livewire: CreateActivos

El componente `CreateActivos` permite la creación de activos dentro de la aplicación. Utiliza **Laravel Livewire 3.0** para gestionar un formulario de múltiples pasos y **WithFileUploads** para la carga de archivos, permitiendo a los usuarios agregar fotos y atributos específicos de un activo.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Validaciones](#validaciones)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)
- [Ejemplo de Uso](#ejemplo-de-uso)

## Descripción General
El componente `CreateActivos` facilita la creación y gestión de activos. Los usuarios pueden realizar selecciones a través de diferentes dropdowns personalizados, subir fotos, y completar información relacionada con el activo, como atributos obligatorios, ubicación, estado de alta, y más. El formulario está dividido en múltiples pasos para guiar al usuario durante la creación del activo.

## Propiedades
- **currentStep**: Indica el paso actual del formulario.
- **selectedAtributos, datoAtributo, campo_numerico, campo**: Utilizados para gestionar los atributos seleccionados y sus valores.
- **open**: Controla si el modal está abierto o cerrado.
- **id_estado_sit_alta, id_estado_sit_general, id_tipo, id_categoria, id_subcategoria, id_ubicacion**: Identificadores utilizados para almacenar los valores seleccionados en los dropdowns.
- **nombre, ubicacionesList, propietario, fotos, nuevasFotos, capturedPhotos**: Propiedades que almacenan datos del activo, fotos y ubicaciones.

## Métodos
### mount()
Inicializa las propiedades del componente y carga datos relacionados como tipos, estados de alta y ubicaciones.

### save()
Valida el formulario, guarda el activo y sus atributos y fotos en la base de datos, utilizando transacciones para asegurar la consistencia. La **transacción** asegura que si ocurre algún error durante la creación del activo, todos los cambios se revierten, manteniendo la integridad de los datos. Esto es crucial para evitar inconsistencias en caso de que falle alguna parte del proceso de guardado.

### setTipo($id)
Establece el tipo seleccionado y carga las categorías asociadas.

### setCategoria($id)
Establece la categoría seleccionada y carga las subcategorías asociadas.

### setSubcategoria($id)
Establece la subcategoría seleccionada y carga los atributos relacionados.

### updatedSearchTipo(), updatedSearchCategoria(), updatedSearchSubcategoria()
Actualizan los resultados de los dropdowns personalizados al cambiar el valor del campo de búsqueda.

### nextStep(), previousStep()
Permiten avanzar y retroceder entre los pasos del formulario. **El paso 2 (Atributos)** se puede omitir si el tipo seleccionado es `'inmueble'` o si no hay atributos obligatorios para el tipo específico. Esta lógica se maneja en el método `nextStep()`, donde se verifica la condición antes de avanzar al paso siguiente.

### close()
Cierra el modal y resetea todas las propiedades del componente.

### createActivo()
Crea un nuevo registro de activo en la base de datos.

### crearAtributos($activo)
Crea los atributos asociados al activo. Los atributos son obligatorios si el tipo de activo lo requiere. En el caso de activos que no sean del tipo `'inmueble'`, se debe seleccionar al menos un atributo obligatorio.

### guardarFotosSubidas($activoId), guardarFotosCapturadas($activoId)
Guarda las fotos subidas o capturadas desde la cámara para el activo. El componente permite cargar **múltiples fotos**, tanto subidas por el usuario como capturadas en tiempo real desde la cámara del dispositivo.

## Validaciones
Las reglas de validación se establecen en el método `rules()`. Algunas de las validaciones más importantes incluyen:
- **nombre, id_tipo, id_categoria, id_subcategoria, id_ubicacion, propietario**: Son obligatorios para la creación del activo.
- **nuevasFotos**: Las fotos deben ser imágenes con extensiones como `jpeg, png, jpg, gif, svg` y no superar los 2048 KB.
- **capturedPhotos**: Se permite capturar hasta 10 fotos en tiempo real.

Cada paso tiene su propia validación específica para garantizar que todos los campos requeridos se completen antes de avanzar.

## Eventos Livewire
- **crearActivo**: Escucha para iniciar el proceso de creación de un nuevo activo.
- **lucky, render**: Emitidos luego de una creación exitosa para actualizar la vista.
- **errorCreacion**: Emitido en caso de error durante la creación del activo y realiza un rollback.

## Vista y Controlador

### Interacción entre Vista y Controlador
El componente `CreateActivos` es un componente de **Livewire** que se comunica directamente con la vista a través de eventos y propiedades enlazadas. La vista utiliza el modelo de enlace de datos (`wire:model`) para sincronizar las propiedades del componente con los campos del formulario en tiempo real. Esto permite que cualquier cambio en los datos del formulario se refleje automáticamente en el controlador y viceversa.

La vista del formulario está dividida en varios pasos, controlados por la propiedad `$currentStep`. Dependiendo del paso actual, se muestran diferentes campos y opciones para guiar al usuario a través del proceso de creación del activo.

### Paso 2: Atributos
El **paso 2** se puede omitir si el tipo seleccionado es `'inmueble'` o si los atributos no son obligatorios o no existen atributos obligatorios para el tipo específico. Esta lógica está implementada en el método `nextStep()`, que verifica si el tipo es `'inmueble'` y si no hay atributos obligatorios antes de avanzar al siguiente paso. Esto permite simplificar el proceso para ciertos tipos de activos, evitando que el usuario tenga que completar información innecesaria.

### Manejo de Fotos
El componente permite la carga de múltiples fotos para un activo. Estas fotos pueden ser subidas por el usuario desde su dispositivo o capturadas directamente usando la cámara del dispositivo. Las fotos subidas se validan para asegurar que sean imágenes y se almacenan en el almacenamiento público de la aplicación. Las fotos capturadas también se procesan y almacenan de manera similar, permitiendo una gestión flexible de imágenes.

Para manejar la cámara en dispositivos móviles, se utiliza un script específico que detecta si el usuario está en un dispositivo móvil y luego permite iniciar la cámara. Este script controla la apertura y cierre de la cámara, la captura de imágenes y la comunicación de estas imágenes con Livewire para que puedan ser guardadas como parte del activo. El script es capaz de gestionar la interfaz de la cámara, ocultar o mostrar el formulario según sea necesario, y asegurar que el proceso sea intuitivo para el usuario.

### Transacciones durante la Creación del Activo
El método `save()` utiliza una transacción de base de datos para asegurar la consistencia de los datos. Esto significa que todas las operaciones de creación del activo, como guardar la información del activo, los atributos y las fotos, se realizan dentro de una transacción. Si alguna operación falla, la transacción se revierte, lo que garantiza que la base de datos no quede en un estado inconsistente. Esta es una práctica recomendada cuando se realizan múltiples operaciones relacionadas con la base de datos en un solo proceso.

## Dependencias
El componente `CreateActivos` depende de múltiples modelos y clases auxiliares. A continuación, se describen las dependencias clave:

- **IdHelper**: Utilizado para obtener identificadores parciales, como el CUIT o CUIL del titular.
- **Modelos**:
  - `TiposModel`, `CategoriaModel`, `SubcategoriaModel`: Utilizados para manejar la selección de tipo, categoría y subcategoría.
  - `ActivosModel`, `ActivosAtributosModel`, `ActivosFotosModel`: Utilizados para crear el activo, sus atributos y guardar las fotos.
  - `EstadosAltasModel`, `EstadoGeneralModel`: Utilizados para definir los estados del activo.
  - `UbicacionesModel`: Maneja la selección de ubicaciones para los activos.
  - `AtributosModel`, `AtributosSubcategoriaModel`: Utilizados para manejar los atributos específicos de cada subcategoría.

### Laravel Livewire
- Utiliza **WithFileUploads** para manejar la carga de archivos (fotos de los activos).

### Storage
- Utiliza **Storage** para guardar imágenes capturadas desde la cámara del dispositivo.

## Ejemplo de Uso
Para abrir el modal de creación de un nuevo activo, se utiliza un botón con un evento `wire:click` que establece la propiedad `open` a `true`:
Dentro del modal, se muestra un formulario con diferentes pasos que el usuario debe completar para crear un activo exitosamente. El formulario incluye selección de tipo, categoría, subcategoría, ubicación y adjuntar fotos del activo.

El componente también incluye un script para manejar la cámara del dispositivo en caso de que el usuario esté utilizando un dispositivo móvil, permitiendo capturar fotos en tiempo real y agregarlas al activo.


