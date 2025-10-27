**Documentación del Componente AuditoriaActivos**

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Dependencias](#dependencias)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `AuditoriaActivos` se utiliza para visualizar los movimientos de un activo en la aplicación. Se muestran los datos de auditoría como la ubicación actual del activo, la ubicación a la que fue trasladado, la fecha del movimiento, y el usuario que realizó la acción. Este componente se basa en LivewireTables para proporcionar una tabla interactiva y dinámica.

## Dependencias
- **Modelos**:
  - `AuditoriaUbicacionActivoModel`: Modelo que proporciona los datos de auditoría de los movimientos de ubicación del activo.
- **Paquetes**:
  - `RamonRietdijk\LivewireTables\Livewire\LivewireTable`: Base para la creación de tablas interactivas en la aplicación con Laravel Livewire.
  - `RamonRietdijk\LivewireTables\Columns\DateColumn`: Para definir columnas de tipo fecha en la tabla.
  - `RamonRietdijk\LivewireTables\Columns\Column`: Para definir columnas básicas en la tabla.
- **Facades**:
  - `Illuminate\Support\Facades\Session`: Utilizada para almacenar y recuperar datos de sesión, en particular el identificador del activo y la URL anterior.

## Propiedades
- **title** (string): Define el título de la tabla. En este caso, es "Movimientos Del Activo".
- **createForm** (string): Nombre del componente de creación (vacío, ya que no se utiliza en este componente).
- **editForm** (string): Nombre del componente de edición (vacío, ya que no se utiliza en este componente).
- **cuit**, **empresa**, **valor**, **identificadores**: Propiedades protegidas que pueden ser usadas en el componente para almacenar información relevante del contexto.
- **previousUrl** (string): Almacena la URL previa para facilitar la navegación del usuario.
- **useSelection** (bool): Propiedad que indica si se permite la selección de filas en la tabla (en este caso, está desactivada).

## Métodos
### mount()
Inicializa el componente, almacena la URL previa y llama al método `initializeActivo()` para configurar el activo que se está auditando.

### initializeActivo()
Obtiene el identificador del activo desde la ruta de la solicitud y lo almacena en la sesión para poder utilizarlo en otras partes del componente.

### query(): Builder
Define la consulta que se utilizará para recuperar los datos de la auditoría. Se filtran los registros por el identificador del activo almacenado en la sesión.

### columns(): array
Define las columnas que se muestran en la tabla de auditoría:
- **Ubicación Actual**: Nombre de la ubicación actual del activo.
- **Activo**: Identificador del activo.
- **Trasladado**: Nombre de la ubicación a la que el activo fue trasladado.
- **Fecha**: Fecha del movimiento, con formato `Y-m-d H:i`.
- **Usuario**: Nombre del usuario que realizó el movimiento.

## Vista del Componente
La vista del componente se genera dinámicamente utilizando LivewireTables. Proporciona una tabla con la siguiente información sobre los movimientos del activo:
- **Ubicación Actual**: La ubicación en la que actualmente se encuentra el activo.
- **Trasladado**: La ubicación destino del activo cuando fue trasladado.
- **Fecha**: La fecha y hora del movimiento para permitir un seguimiento cronológico.
- **Usuario**: Nombre del usuario que realizó la acción, para auditoría.

La interfaz permite ordenar y buscar entre los registros, facilitando la identificación de movimientos específicos del activo. La tabla está diseñada para ser fácil de usar y permite una navegación intuitiva para el usuario final.

