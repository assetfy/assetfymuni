**Documentación del componente CreateEmpresas**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Filtros](#filtros)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)
  - [Paso 1: Defina el Área de Cobertura](#paso-1-defina-el-área-de-cobertura)
  - [Paso 2: Selección del Tipo de Empresa o Detalles del Inmueble](#paso-2-selección-del-tipo-de-empresa-o-detalles-del-inmueble)
  - [Paso 3 y 4: Selección de Actividad Económica o Tipo de Empresa](#paso-3-y-4-selección-de-actividad-económica-o-tipo-de-empresa)
  - [Paso 5 y 6: Buscar Empresa o Resumen](#paso-5-y-6-buscar-empresa-o-resumen)
- [Estilos y Animaciones](#estilos-y-animaciones)
- [Flujos de Decisión](#flujos-de-decisión)
- [Integración con Backend](#integración-con-backend)
- [Resumen](#resumen)

## Descripción General

El componente `CreateEmpresas` es un componente Livewire que gestiona el proceso de creación de una nueva empresa en el sistema. Está estructurado en varios pasos, cada uno de los cuales presenta un formulario específico para recopilar la información relevante de la empresa. A continuación, se detalla su estructura, funcionalidad y flujo.

## Propiedades

- **$open**: Indica si el modal de creación de empresas está abierto o cerrado.
- **$step**: Almacena el paso actual del formulario (1 a 6).
- **$isStepValid**: Indica si el paso actual es válido para avanzar al siguiente.
- **$maxStep**: Almacena el número máximo de pasos (5 o 6, según el valor de `opcion`).
- **$opcion**: Define si el usuario quiere agregar una ubicación como inmueble ('Si' o 'No').
- **$cuit, $razon_social, $estado, $constancia_afip** y otros: Propiedades utilizadas para almacenar los datos de la empresa que se está creando.
- **$tipoUbicaciones, $usuariosempresas, $tiposEmpresas**: Colecciones que contienen datos necesarios para los selectores en el formulario.

## Listeners

- **openModalCrearUsuario**: Escucha eventos para abrir el modal de creación de usuarios.
- **initMap**: Escucha eventos para inicializar el mapa cuando se monta el componente.

## Filtros

- **updated()**: Método que se activa cada vez que una propiedad es actualizada. Valida el valor del campo actualizado y vuelve a validar el paso actual.
- **updatedOpcion()**: Se ejecuta cuando cambia la propiedad `opcion` y ajusta las reglas de validación y el número máximo de pasos según la opción seleccionada.

## Métodos

- **mount()**: Se ejecuta al cargar el componente. Inicializa las propiedades y dispara un evento para inicializar el mapa en el frontend.

- **updatedOpcion()**: Actualiza las reglas de validación y el número máximo de pasos cuando cambia la opción (`opcion`).

- **nextStep()**: Avanza al siguiente paso del formulario después de validar el paso actual. Si el usuario seleccionó "Propietario de un activo" en el paso 2, salta al paso 4 directamente.

- **previousStep()**: Retrocede al paso anterior del formulario dependiendo del estado actual y las decisiones tomadas por el usuario.

- **validateCurrentStep()**: Valida los campos requeridos del paso actual antes de permitir avanzar al siguiente paso.

- **save()**: Guarda los datos del formulario en la base de datos. Utiliza una transacción para garantizar la integridad de los datos. Si ocurre un error, revierte todos los cambios y elimina los archivos temporales.

- **setAddress()**: Establece la dirección basada en la latitud y longitud ingresadas, utilizando la API de Nominatim.

- **verificarCuitAfip()**: Verifica el CUIT ingresado utilizando el `AfipController` y procesa los datos obtenidos.

- **removeConstanciaAfip() / removeLogo()**: Eliminan la constancia de AFIP o el logo previamente subido, si es necesario.

## Vista del Componente

### Paso 1: Defina el Área de Cobertura
- Muestra un mapa para seleccionar la ubicación de la empresa. Los campos como `lat`, `long`, `provincia`, `localidad`, y `domicilio` se completan según la ubicación seleccionada.
- **Campos Incluidos**: 
  - Latitud y Longitud (obtenidos automáticamente del mapa).
  - Provincia, Localidad, Domicilio, Piso, Código Postal (editable).
  - Opción para agregar la ubicación como inmueble ('Si' o 'No').

### Paso 2: Selección del Tipo de Empresa o Detalles del Inmueble
- Si se elige "Si" en `opcion`, se solicitan detalles del inmueble (nombre, propiedad, tipo de ubicación).
- Si se elige "No", se selecciona el tipo de empresa (propietario de un activo o proveedor de servicios) con tarjetas que contienen imágenes y descripciones.
- **Campos Incluidos**:
  - **Detalles del Inmueble**: Nombre, Propiedad, Tipo de Ubicación.
  - **Tipo de Empresa**: Selección interactiva mediante tarjetas.

### Paso 3 y 4: Selección de Actividad Económica o Tipo de Empresa
- Dependiendo de la selección previa, se presentan opciones para seleccionar la actividad económica o el tipo de empresa.
- **Tipo de Empresa**: Selección interactiva mediante tarjetas.
- **Actividades Económicas**:
  - Actividades sin servicios para el tipo "Propietario de un activo".
  - Actividades con servicios para el tipo "Proveedor de servicios".
- Las actividades están paginadas para una mejor visualización.

### Paso 5 y 6: Buscar Empresa o Resumen
- En estos pasos, se puede buscar una empresa mediante el CUIT o se muestra un resumen de los datos ingresados antes de confirmar la creación de la empresa.
- **Buscar Empresa**: Ingreso de CUIT y búsqueda en la base de datos.
- **Resumen de la Empresa**: Muestra todos los datos ingresados para la empresa.

## Estilos y Animaciones

- **Barra de Progreso**: Incluye una barra de progreso que refleja el avance del usuario en el formulario con efectos de transición suave.
- **Tarjetas Interactivas**: Las tarjetas utilizadas para seleccionar el tipo de empresa o actividad económica tienen efectos visuales de transición y animaciones al ser seleccionadas.
- **Animaciones Personalizadas**: Se aplican animaciones CSS para proporcionar una experiencia visual atractiva, como la transición de la barra de progreso y el efecto "persiana" para mostrar la descripción de la actividad económica.

## Flujos de Decisión

El flujo del formulario cambia dependiendo de la selección del usuario en algunos pasos:

1. Si en el paso 2 se selecciona "Propietario de un activo", se omite el paso 3 y se avanza directamente al paso 4.
2. Dependiendo de si se elige "Si" o "No" en la opción de agregar un inmueble, el número de pasos totales cambia (5 o 6 pasos).

## Integración con Backend

- **Transacciones con la Base de Datos**: Al guardar la información, el componente usa una transacción de base de datos para asegurarse de que los registros de empresa, usuario y actividades se crean correctamente o se revierte en caso de error.
- **Manejo de Archivos**: Los archivos de constancia AFIP y logo se guardan temporalmente y luego se mueven a su ubicación final al completar el registro.
- **API de Nominatim**: Se utiliza para obtener la dirección exacta de la empresa con base en la latitud y longitud seleccionadas en el mapa.
- **Verificación de CUIT**: Se realiza una consulta a la API de AFIP para verificar la existencia y estado de la empresa ingresada.

## Resumen

El componente `CreateEmpresas` es una implementación compleja y robusta que permite la creación de empresas mediante un formulario de múltiples pasos, con validaciones y flujos condicionales que dependen de las decisiones del usuario. Su integración con APIs externas, como la de Nominatim y AFIP, proporciona información adicional para mejorar la experiencia del usuario y asegurar la validez de los datos ingresados. El uso de transacciones garantiza la integridad de los datos y la correcta asociación entre empresas, usuarios y actividades económicas.

