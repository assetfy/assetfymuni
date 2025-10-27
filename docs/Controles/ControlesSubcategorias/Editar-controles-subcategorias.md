# Documentación del Componente Livewire: EditarControlesSubcategoria

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Reglas de Validación](#reglas-de-validación)
- [Métodos](#métodos)
- [Vista y Controlador](#vista-y-controlador)

## Descripción General
El componente `EditarControlesSubcategoria` es parte del sistema de gestión de activos y está diseñado para editar controles asociados a subcategorías de activos. Este componente permite a los usuarios actualizar diversas propiedades de un control, como si es único, obligatorio, periódico, entre otros.

## Propiedades

- **open**: Controla si el modal de edición está abierto o cerrado.
- **upunico**: Indica si el control es único.
- **upobligatorio_carga_ini**: Define si el control es obligatorio en la carga inicial.
- **id_control, id_categoria, id_subcategoria, id_tipo**: IDs correspondientes al control, categoría, subcategoría y tipo.
- **upes_periodico, upfrecuencia_control, upcantidad_estandar, upreq_foto**: Propiedades adicionales que definen la periodicidad, frecuencia, cantidad estándar y si el control requiere fotos.
- **controlessub**: Instancia del modelo `ControlesSubcategoriaModel` que se está editando.
- **control, categoria, cat, subcat, ctrl, cont**: Variables auxiliares utilizadas para gestionar y mostrar datos del formulario de edición.

## Reglas de Validación
Las reglas de validación para el componente se definen en la propiedad `$rules` e incluyen:

- **id_control**: Requerido.
- **upunico**: Requerido, máximo de 50 caracteres, mínimo de 2.
- **upobligatorio_carga_ini, id_tipo, id_categoria, id_subcategoria, upes_periodico, upreq_foto**: Requerido.
- **upfrecuencia_control, upcantidad_estandar**: Requerido.

## Métodos

### mount
El método `mount` se llama cuando se monta el componente y carga los valores del modelo `ControlesSubcategoriaModel` en las propiedades del componente para su edición.

### openControlesSubcategoria
Este método se encarga de validar los permisos del usuario utilizando `MiddlewareInvoker`. Si el usuario tiene permiso, se llama a `openModal` para abrir el modal de edición.

### openModal
`openModal` busca el registro en la base de datos basado en el ID proporcionado, llama a `mount` para cargar los datos y establece el modal en abierto.

### guardarCambios
Este método se utiliza para disparar el evento de guardar cambios (`guardarCambios`) en el componente.

### actualizar
El método `actualizar` es llamado por un atributo `On` (`guardado`). Llama al método `actualizarControlSub` para guardar los cambios en la base de datos.

### actualizarControlSub
Este método valida los datos y actualiza las propiedades del registro del modelo `ControlesSubcategoriaModel`. Después de actualizar el registro, se dispara el evento `refreshLivewireTable` y se cierra el modal.

### close
Este método cierra el modal de edición.

## Vista y Controlador
El componente `EditarControlesSubcategoria` proporciona una interfaz en forma de modal para la edición de los controles asociados a las subcategorías de activos. Los campos del formulario incluyen propiedades como si el control es único, obligatorio, si requiere fotos, y otros parámetros específicos.

El flujo de trabajo del componente incluye:
- Verificación de permisos antes de permitir la edición.
- Carga de datos del control en el formulario para su edición.
- Validación y actualización de los datos ingresados.
- Actualización de la tabla de controles tras la edición del registro.
- Cierre del modal después de guardar los cambios.