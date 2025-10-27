# Documentación del Componente Livewire: Foto

## Tabla de Contenidos
- [Descripción General](#descripción-general)
- [Propiedades](#propiedades)
- [Listeners](#listeners)
- [Métodos](#métodos)
- [Vista del Componente](#vista-del-componente)

## Descripción General
El componente `Foto` es un componente Livewire que permite mostrar fotos asociadas a los controles de activos. Utiliza el trait `WithFileUploads` para manejar la subida de archivos y se encarga de visualizar las fotos de los controles almacenados.

## Propiedades
- **\$open**: Controla si el modal de la foto está abierto o cerrado.
- **\$id_controles**: Almacena el ID del control asociado para mostrar la foto correspondiente.

## Listeners
- **cerrar**: Vincula el método `close` para cerrar el componente.
- **render**: Vuelve a renderizar la vista del componente.

## Métodos

### mount(ActivosControlesModel \$controles)
Este método se ejecuta cuando se instancia el componente. Inicializa la propiedad **\$id_controles** con el modelo de controles recibido como argumento.

### render()
Este método renderiza la vista del componente. Se obtiene una lista de todos los controles de activos disponibles y se pasa a la vista.

### close()
Este método se encarga de cerrar el modal de fotos. Además, restablece la propiedad **imagen** para evitar problemas cuando el modal se vuelva a abrir.

## Vista del Componente
La vista asociada al componente es `livewire.controles.mostrarfoto`, la cual muestra las fotos asociadas a los controles de activos. Se pasa la lista de controles para que se visualicen en la vista correspondiente.

