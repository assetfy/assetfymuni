**Documentación del Componente CrearUbicaciones**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `CrearUbicaciones` se encarga de crear una nueva ubicación en el sistema. Permite registrar detalles como la dirección, información geográfica (latitud y longitud), tipo de ubicación, entre otros. La funcionalidad incluye validación en tiempo real y verificación de ubicaciones duplicadas.

## Dependencias
### Modelos
- **TiposUbicacionesModel**: Utilizado para obtener los tipos de ubicaciones disponibles.
- **UbicacionesModel**: Modelo principal utilizado para crear y almacenar la nueva ubicación en la base de datos.

### Servicios
- **MiddlewareInvoker**: Verifica si el usuario tiene los permisos necesarios para realizar la acción de crear una nueva ubicación.

### Traits
- **VerificacionTrait**: Trait personalizado que proporciona métodos de verificación para las operaciones del componente.
- **SortableTrait**: Trait utilizado para funcionalidades relacionadas con la ordenación.

## Propiedades
- **open** (bool): Controla si el modal de creación de ubicaciones está abierto o cerrado.
- **nombre** (string): Nombre de la ubicación.
- **pais**, **provincia**, **ciudad** (string): Datos de la dirección de la ubicación.
- **codigo_postal**, **calle**, **altura**, **piso**, **depto** (string): Detalles específicos de la dirección.
- **lat**, **long** (float): Coordenadas de la ubicación (latitud y longitud).
- **propiedad** (string): Propiedad de la ubicación (e.g., propio, cliente).
- **tipo** (string): Tipo de ubicación seleccionado.
- **existe** (bool): Indica si ya existe una ubicación con el mismo nombre y CUIT o CUIL.
- **cuit**, **cuil** (string): Identificadores del propietario o responsable de la ubicación.

## Métodos
### crearUbicacion()
Abre el modal para crear una nueva ubicación, verificando primero si el usuario tiene permisos.

### openModal()
Verifica permisos y abre el modal, enviando un evento para mostrar el mapa.

### updated($propertyName)
Valida un campo específico cada vez que se actualiza su valor, asegurando la entrada correcta del usuario.

### save()
Realiza la validación de todos los campos, verifica si la ubicación ya existe y, si no es así, llama al método `crearRegistro()` para almacenar la nueva ubicación.

### crearRegistro()
Crea una nueva instancia del modelo `UbicacionesModel` y almacena la ubicación en la base de datos.

### existeUbicacion()
Verifica si ya existe una ubicación con el mismo nombre y CUIT o CUIL en la base de datos.

### setAddress($lat, $long)
Obtiene la dirección a partir de las coordenadas proporcionadas utilizando la API de `Nominatim OpenStreetMap` y actualiza las propiedades del componente con la información recibida.

### handleGeolocation($lat, $long)
Actualiza las propiedades de latitud y longitud y luego llama al método `setAddress()` para obtener la información de la dirección.

### close()
Cierra el modal y reinicia todas las propiedades relacionadas con la ubicación.

### render()
Renderiza la vista del componente `crear-ubicaciones` y despacha el evento `mapModalShown` para inicializar el mapa.

## Vista del Componente
La vista del componente `crear-ubicaciones` incluye un modal con un formulario dividido en dos partes principales:

- **Mapa**: Ubicado en la mitad izquierda del modal, permite al usuario interactuar con un mapa para obtener la geolocalización exacta de la ubicación.

- **Formulario de Información**: Ubicado en la mitad derecha del modal, contiene campos para ingresar la información general de la ubicación y su dirección detallada. Estos campos incluyen:
  - **Información General**: Nombre, país, provincia, ciudad.
  - **Dirección**: Calle, altura, piso, departamento, código postal.
  - **Propiedad** y **Tipo de Ubicación**: Secciones donde se selecciona la naturaleza de la propiedad y el tipo de ubicación.

El modal cuenta con botones para cancelar la acción y para guardar los datos ingresados. También muestra el estado de carga cuando se está procesando la acción de creación de la ubicación.


