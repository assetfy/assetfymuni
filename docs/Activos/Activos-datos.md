# Documentación del Componente ActivosDatos

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `ActivosDatos` permite mostrar la información detallada de un activo específico en una vista estructurada. Utiliza Livewire para gestionar el estado del componente y actualizar los datos de los activos seleccionados. Este componente se utiliza para proporcionar detalles como el tipo, la categoría, la ubicación, el estado del inventario y el último servicio realizado, incluyendo información adicional como la empresa responsable y el responsable del servicio.

## Dependencias
El componente depende de los siguientes elementos:
- **Livewire**: Para la gestión del estado y actualización dinámica de los datos del componente.
- **Modelos**: Los modelos siguientes se utilizan para obtener los datos necesarios:
  - `ActivosModel`: Para obtener información del activo.
  - `TiposModel`: Para obtener los diferentes tipos de activos.
  - `EstadosAltasModel`: Para consultar el estado de alta del activo.
  - `EstadoGeneralModel`: Para obtener el estado general del activo.
  - `SubcategoriaModel` y `CategoriaModel`: Para categorizar el activo.
  - `UbicacionesModel`: Para conocer la ubicación del activo.
  - `ServiciosActivosModel` y `ServiciosModel`: Para obtener información sobre los servicios realizados en el activo.
  - `EmpresasModel`: Para obtener los datos de las empresas encargadas de los servicios.
  - `User`: Para identificar al responsable del servicio.
- **Carbon**: Para el formateo de fechas.

## Propiedades
- **id_activo** (int): Almacena el identificador del activo que se está mostrando.
- **activo** (objeto): Contiene los datos del activo actual.
- **tipos** (colección): Lista de todos los tipos de activos disponibles.
- **altas** (colección): Lista de estados de alta del activo.
- **general** (colección): Lista de estados generales del activo.
- **subcategorias** (colección): Lista de subcategorías de activos.
- **categorias** (colección): Lista de categorías de activos.
- **ubicaciones** (colección): Lista de ubicaciones disponibles.
- **servicios** (objeto): Datos del último servicio realizado en el activo.
- **tipoServicios** (colección): Lista de tipos de servicios disponibles.
- **empresas** (colección): Lista de empresas.
- **usuarios** (colección): Lista de usuarios.
- **ultimaFechaServicio** (string): Fecha del último servicio realizado en el activo, formateada.

## Métodos
### render($id_activo)
Renderiza la vista del componente y establece el ID del activo que se debe mostrar. Llama al método `datos()` para obtener la información necesaria del activo. Devuelve la vista `livewire.activos-datos` con los datos del activo y sus detalles.

### datos($id_activo)
Obtiene los datos relacionados con el activo especificado:
- Consulta el modelo `ActivosModel` para obtener la información del activo.
- Obtiene todas las listas relacionadas, como tipos, estados de alta, estados generales, subcategorías, categorías, ubicaciones, empresas y usuarios.
- Obtiene el último servicio realizado al activo usando el modelo `ServiciosActivosModel` y formatea la fecha con `Carbon`.

## Vista del Componente
La vista del componente `ActivosDatos` presenta la información del activo de manera estructurada en una tarjeta. Incluye las siguientes secciones:
- **Detalles del Activo**: Incluye información sobre el tipo de activo, categoría, subcategoría, ubicación, estado de alta y general, comentarios, y motivo de baja.
- **Último Servicio Realizado**: Si el activo tiene un servicio registrado, muestra información sobre el último servicio realizado, incluyendo el nombre del servicio, la empresa encargada, la fecha, descripción, responsable y otros detalles relevantes.

### Elementos de la Vista
- **Tarjeta Detalles Activos**: Presenta el título "Detalles Activos" seguido de la información detallada del activo.
  - **Tipo, Categoría, Subcategoría**: Se muestran utilizando los modelos correspondientes para obtener el nombre de cada elemento.
  - **Ubicación**: Se muestra la ubicación actual del activo.
  - **Estado de Alta y General**: Se muestran los nombres correspondientes a los estados almacenados.
  - **Comentarios y Motivo de Baja**: Campos adicionales del activo que se muestran si están disponibles.
- **Último Servicio Realizado**: Si existe, se muestra la información del último servicio realizado, incluyendo:
  - **Nombre del Servicio y Empresa**: Detalles del tipo de servicio y la empresa responsable.
  - **Fecha y Descripción**: Fecha formateada del servicio y comentarios.
  - **Responsable**: Nombre del usuario responsable del servicio.
  - **Vigencia del Servicio y Empresa Avalada**: Campos opcionales dependiendo del activo.

### Estilos
La vista utiliza clases de Bootstrap para la estructura y el estilo, incluyendo clases para tarjetas (`card`), filas (`row`), y columnas (`col`). Además, se han definido algunas clases personalizadas para ajustar los anchos y el margen.

### Script de la Vista
La vista incluye scripts para cargar dependencias de Bootstrap y jQuery, con el fin de proporcionar funcionalidad adicional para el modal y otros elementos interactivos.

