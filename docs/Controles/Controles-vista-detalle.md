# Documentación del Componente Livewire: Controles Vista Detalle

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Renderizado](#renderizado)
- [Flujo del Componente](#flujo-del-componente)
- [Vista del Componente](#vista-del-componente)
- [Resumen de la Vista](#resumen-de-la-vista)

## Descripción General
El componente `ControlesVistaDetalle` es un componente de Livewire que permite visualizar los detalles de los controles asociados a un activo. Este componente es parte del sistema de gestión de activos y se encarga de listar los controles obligatorios que faltan por asociar a un activo, así como mostrar los controles ya existentes.

## Propiedades
- **\$id_activo**: Identificador del activo cuyos controles se desean visualizar.
- **\$id_subcategoria**: Identificador de la subcategoría del activo.
- **\$id_categoria**: Identificador de la categoría del activo.
- **\$id_tipo**: Identificador del tipo del activo.
- **\$controlesObligatorios**: Lista de controles que son obligatorios para el activo.
- **\$activosControles**: Lista de controles ya asociados al activo.
- **\$controlesFaltantes**: Lista de controles faltantes que son obligatorios para el activo.
- **\$controles**: Lista de controles únicos asociados al activo.
- **\$controlesModelo**: Lista de modelos de controles asociados al activo.
- **\$activos**: Lista de todos los activos.
- **\$controlesSubcategoria**: Lista de todos los controles por subcategoría.
- **\$previousUrl**: URL previa para permitir la navegación hacia atrás.

## Métodos
### setIdActivo(\$id_activo)
Este método se encarga de inicializar el componente con el activo cuyos controles se desean visualizar. Realiza las siguientes acciones:

- Guarda el ID del activo en la sesión para facilitar el acceso.
- Obtiene los controles asociados al activo desde el modelo `ActivosControlesModel`.
- Verifica si el activo no tiene controles asociados y, si es así, obtiene los controles obligatorios basados en la subcategoría, categoría y tipo del activo.
- En caso de que el activo tenga controles, se obtiene la lista de controles faltantes para cumplir con los requisitos iniciales.
- Organiza los controles de manera única y los ordena por fecha de creación.

## Renderizado
El método `render` devuelve la vista `livewire.controles.controles-vista-detalle` que incluye la información obtenida.

## Flujo del Componente
1. **Visualizar controles asociados**: Se obtienen y muestran los controles actualmente asociados al activo seleccionado.
2. **Visualizar controles faltantes**: Se identifican y muestran los controles faltantes que son obligatorios para el activo, basándose en su subcategoría, categoría y tipo.
3. **Acciones disponibles**: Se permite al usuario navegar entre los detalles de los diferentes controles asociados al activo mediante enlaces.

## Vista del Componente
El componente incluye una vista en `blade.php` con las siguientes secciones:
- Un botón para regresar a la página anterior (`$previousUrl`).
- Un mensaje informativo si existen controles faltantes obligatorios.
- Un listado de los controles ya asociados al activo en formato de tarjetas, con la opción de ver detalles adicionales.
- Un mensaje de "SIN REGISTROS PARA MOSTRAR" si no hay controles asociados al activo o faltantes obligatorios.

## Resumen de la Vista
La vista del componente permite al usuario ver cómo se organizan los controles relacionados con el activo seleccionado. Si existen controles faltantes que son obligatorios, se muestra un aviso para que el usuario pueda identificar qué controles necesitan ser asociados al activo.

- **Volver**: Botón para regresar a la URL anterior.
- **Controles Obligatorios**: Mensaje que lista los controles obligatorios faltantes si existen.
- **Controles Actuales**: Listado de los controles existentes, con enlaces para ver más detalles.
- **Sin Registros**: Mensaje mostrado si no hay controles que listar.

