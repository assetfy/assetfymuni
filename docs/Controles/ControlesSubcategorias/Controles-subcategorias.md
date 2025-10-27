# Documentación del Componente Livewire: ControlesSubcategoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Columnas](#columnas)
- [Filtros](#filtros)
- [Métodos](#métodos)

## Descripción General
El componente `ControlesSubcategoria` es parte del sistema de gestión de activos y tiene como objetivo la administración de controles asociados a subcategorías de activos. Este componente se presenta en una tabla que muestra detalles de los controles de las subcategorías, facilitando la gestión y actualización de la información.

## Propiedades

- **model**: Define el modelo de datos utilizado por el componente, que en este caso es `ControlesSubcategoriaModel`.
- **title**: El título que se muestra en la parte superior de la tabla es "Controles Subcategorias".
- **createForm**: Componente de creación predeterminado, en este caso `CrearControlesSubcategorias`.
- **useSelection**: Determina si se pueden seleccionar filas en la tabla (establecido en `false`).

## Columnas
El componente presenta varias columnas en la tabla para visualizar la información de los controles:

- **Control**: Nombre del control asociado a la subcategoría. Es una columna que se puede ordenar y buscar.
- **Obligatorio Carga Inicial**: Determina si el control es obligatorio al momento de la carga inicial.
- **Es Periodico**: Indica si el control es periódico.
- **Frecuencia de Control**: Muestra la frecuencia con la que se debe realizar el control.
- **Unico**: Indica si el control es único.
- **Requiere Fotos**: Muestra si el control requiere fotos.
- **Acciones**: Esta columna incluye un botón para editar los controles asociados a una subcategoría en específico.

## Filtros
El componente incluye un filtro para facilitar la búsqueda y segmentación de los registros:

- **Subcategoría**: Filtro por la subcategoría de la categoría, que permite buscar los registros según la subcategoría seleccionada.

## Métodos

### update
Este método se utiliza para abrir el modal de edición de una subcategoría de control específica. Envía un evento con el ID de la subcategoría a editar y redirige al componente `editar-controles-subcategoria`.

### getSubcategorias
Este método obtiene las subcategorías que tienen controles asociados. Utiliza la relación `subcategoria` y extrae los valores de nombre e ID de la subcategoría, que luego se utilizan en el filtro de la tabla.

### CrearControlesSubcategorias
Este método se utiliza para disparar el evento de creación de una nueva subcategoría de control y redirige al componente `create-controles-subcategoria`.

## Vista y Controlador
El componente `ControlesSubcategoria` proporciona una interfaz en forma de tabla para gestionar los controles asociados a las subcategorías. Esto facilita la visualización y administración de datos de manera ordenada y permite la edición y creación de nuevos controles mediante la interacción con el usuario.

El flujo de trabajo del componente incluye:
- Mostrar la información relacionada con los controles de subcategorías.
- Permitir el acceso al formulario de edición para modificar información existente.
- Proporcionar un flujo para crear nuevos controles asociados a subcategorías, utilizando el formulario de creación correspondiente.