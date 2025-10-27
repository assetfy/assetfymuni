**Documentación del Componente Ubicaciones**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `Ubicaciones` se utiliza para mostrar una tabla interactiva con información sobre las ubicaciones almacenadas en la base de datos. Incluye funcionalidades para filtrar, buscar y exportar las ubicaciones. Además, permite crear nuevas ubicaciones a través de un modal.

Este componente está basado en Livewire y utiliza la extensión `LivewireTables` para la generación y gestión de tablas, así como `Excel` para la exportación de datos.

## Dependencias
### Modelos
- **UbicacionesModel**: Modelo principal que representa las ubicaciones en la base de datos.

### Servicios
- **Maatwebsite\Excel**: Utilizado para exportar los datos de las ubicaciones en formato Excel.
- **IdHelper**: Helper que proporciona un identificador de usuario para la consulta de ubicaciones.

### Traits
- **LivewireTable**: Proporciona la lógica para crear una tabla interactiva utilizando `LivewireTables`.
- **SelectFilter**: Utilizado para agregar filtros seleccionables a la tabla.
- **Action**: Utilizado para definir acciones que se pueden realizar en la tabla (por ejemplo, exportar datos).

## Propiedades
- **model** (string): El modelo utilizado para la tabla, en este caso, `UbicacionesModel`.
- **title** (string): El título de la tabla, definido como "Ubicaciones".
- **createForm** (string): Nombre del formulario de creación, en este caso, `'crearubicacion'`.
- **editForm** (string): Nombre del formulario de edición (actualmente sin utilizar).
- **userId** (string): Identificador del usuario, obtenido utilizando el helper `IdHelper`.
- **usuarios_empresas**: Almacena información sobre los usuarios de empresas.

## Métodos
### asignar()
Asigna el identificador de usuario utilizando `IdHelper::identificador()`.

### query(): Builder
Construye la consulta principal para obtener las ubicaciones del usuario actual, basándose en `cuil` o `cuit`. Si no se encuentran ubicaciones utilizando `cuil`, la consulta se reintenta utilizando `cuit`.

### abrirModal()
Desencadena un evento de Livewire (`reinicializarMapa`) para inicializar o actualizar un mapa relacionado con las ubicaciones.

### columns(): array
Define las columnas mostradas en la tabla de ubicaciones, incluyendo datos como:
- Nombre
- País
- Provincia
- Ciudad
- Código Postal
- Calle
- Altura
- Piso
- Dpto

Cada columna puede ser ordenada y buscada, proporcionando facilidad de navegación para el usuario.

### filters(): array
Define los filtros que se pueden aplicar a la tabla. Actualmente, incluye un filtro de selección basado en los tipos de ubicaciones.

### getTipos()
Obtiene los tipos de ubicaciones disponibles para el filtro, utilizando relaciones con el modelo `UbicacionesModel`.

### actions(): array
Define las acciones que se pueden realizar desde la tabla. Actualmente incluye la acción "Exportar" que permite exportar las ubicaciones visibles en un archivo Excel (`Ubicaciones.xlsx`).

### crearubicacion()
Desencadena el evento para abrir el formulario de creación de ubicaciones (`crearUbicacion`).

## Vista del Componente
La vista del componente se presenta mediante una tabla interactiva que permite al usuario:
- **Visualizar las ubicaciones** con datos relevantes como nombre, país, provincia, ciudad, código postal, etc.
- **Filtrar** las ubicaciones por tipo.
- **Exportar** los datos visibles a un archivo Excel.
- **Crear nuevas ubicaciones** mediante un modal que se abre al seleccionar la opción de creación.

Este componente proporciona una interfaz amigable para la gestión de ubicaciones, con funcionalidades avanzadas para filtrar, buscar y exportar información, asegurando una experiencia de usuario eficiente.

