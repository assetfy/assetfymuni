# Documentación del Componente Livewire: EditActivos

El componente `EditActivos` permite la edición de activos dentro de la aplicación. Utiliza **Laravel Livewire 3.0** para gestionar un formulario modal, permitiendo a los usuarios actualizar información de activos previamente creados.

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Métodos](#métodos)
- [Validaciones](#validaciones)
- [Eventos Livewire](#eventos-livewire)
- [Vista y Controlador](#vista-y-controlador)
- [Dependencias](#dependencias)

## Descripción General
El componente `EditActivos` facilita la actualización y gestión de activos ya existentes. Los usuarios pueden modificar propiedades clave de un activo, como la etiqueta, número de serie, propietario, nombre, estado de alta y situación general, entre otros. El formulario está diseñado para ser intuitivo, guiando al usuario durante el proceso de edición del activo.

## Propiedades
- **open**: Controla si el modal de edición está abierto o cerrado.
- **upetiqueta, upnumero_serie, uppropietario, upnombre**: Almacenan los valores actuales del activo a editar.
- **id_estado_sit_alta, upcomentarios_sit_alta, upestado_inventario, upmotivo_baja, id_estado_sit_general**: Almacenan datos del estado del activo.
- **id_tipo, id_categoria, id_subcategoria**: Utilizados para almacenar los valores seleccionados en los dropdowns.
- **activo, cat, subcat**: Almacenan el modelo del activo y las listas de categorías y subcategorías asociadas al tipo seleccionado.

## Métodos
### mount(ActivosModel $activo)
Inicializa las propiedades del componente con los datos del activo que se va a editar. También carga las categorías y subcategorías asociadas al tipo del activo.

### actualizar()
Valida los datos ingresados y actualiza la información del activo en la base de datos. Utiliza el método `save()` para guardar los cambios y emite un evento para notificar que la vista debe actualizarse. Luego de actualizar el activo, cierra el modal.

### value($value)
Carga las categorías y subcategorías relacionadas con el tipo de activo seleccionado. Si no hay categorías disponibles, resetea las subcategorías para evitar inconsistencias.

### render()
Renderiza la vista del componente, cargando las listas de tipos, estados de alta, estados generales, categorías y subcategorías para los dropdowns del formulario.

## Validaciones
Las reglas de validación se establecen en la propiedad `rules`. Algunas de las validaciones más importantes incluyen:
- **upetiqueta, upnumero_serie, uppropietario, upnombre, id_estado_sit_alta, upcomentarios_sit_alta, upestado_inventario, upmotivo_baja, id_estado_sit_general, id_tipo, id_categoria, id_subcategoria**: Son obligatorios para la actualización del activo.

## Eventos Livewire
- **render**: Emitido luego de una actualización exitosa para actualizar la vista.
- **prueba**: Emitido para mostrar un mensaje de éxito luego de actualizar los datos del activo.

## Vista y Controlador

### Interacción entre Vista y Controlador
El componente `EditActivos` se comunica directamente con la vista a través de eventos y propiedades enlazadas. La vista utiliza el modelo de enlace de datos (`wire:model`) para sincronizar las propiedades del componente con los campos del formulario en tiempo real. Esto permite que cualquier cambio en los datos del formulario se refleje automáticamente en el controlador y viceversa.

### Dropdowns Dinámicos
Los dropdowns de tipo, categoría y subcategoría están interrelacionados. Cuando el usuario selecciona un tipo, se cargan las categorías correspondientes. De la misma manera, al seleccionar una categoría, se cargan las subcategorías asociadas.

### Manejo del Modal
El modal se abre haciendo clic en el botón de "Editar". Una vez abierto, el usuario puede editar los datos del activo y, al hacer clic en "Editar Activo", se actualiza la información en la base de datos. Si la actualización es exitosa, el modal se cierra y se muestra un mensaje de confirmación.

## Dependencias
El componente `EditActivos` depende de varios modelos y clases auxiliares:
- **Modelos**:
  - `ActivosModel`, `TiposModel`, `CategoriaModel`, `SubcategoriaModel`: Utilizados para manejar los datos del activo, tipo, categoría y subcategoría.
  - `EstadosAltasModel`, `EstadoGeneralModel`: Utilizados para definir los estados del activo.

