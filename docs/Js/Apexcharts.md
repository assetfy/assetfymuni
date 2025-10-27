**Descripción del Código para la Gestión de Solicitudes de graficos**

Este código está diseñado para inicializar varios tipos de gráficos (barras y Donut) utilizando ApexCharts en una página web de forma dinámica y responsiva. A continuación se explican las principales partes del código:

### Descripción General
- **Biblioteca Utilizada**: Se utiliza **ApexCharts**, una librería poderosa para la visualización de datos en forma de gráficos.
- **Propósito**: El código permite la creación y visualización de gráficos de barras y gráficos Donut de manera asíncrona, ofreciendo información relevante sobre las solicitudes y servicios que maneja la aplicación.

### Inicialización de Gráficos de Barras (`initBarCharts`)
- **Verificación de Elementos DOM**: Primero se asegura de que los elementos HTML correspondientes para los gráficos existen en el DOM antes de proceder.
- **Configuración de Gráficos**: Se definen opciones comúnmente compartidas por ambos gráficos para mantener consistencia, tales como tipo de gráfico (barra), dimensiones y estilo de las barras.
- **Opciones Específicas**: Luego se crean dos configuraciones distintas, una para representar las solicitudes aprobadas y pendientes, y otra para mostrar el número de servicios y peticiones por mes.
- **Renderización**: Finalmente, se utiliza `ApexCharts` para renderizar estos gráficos en los elementos especificados del DOM.

### Inicialización del Gráfico Donut (`initDonutChart`)
- **Verificación de Elemento DOM**: Comprueba la existencia del contenedor HTML correspondiente al gráfico Donut.
- **Reasignación de Datos**: Reasigna los datos desde el objeto `window` para garantizar que se utilicen valores actualizados antes de la inicialización del gráfico.
- **Cálculo del Total de Servicios**: Calcula la suma de todos los valores proporcionados para asegurar que hay datos válidos antes de renderizar el gráfico.
- **Configuración del Gráfico Donut**: Se configura y se muestra el gráfico si hay datos disponibles, mostrando la proporción de cada categoría y leyendas.
- **Mensaje Alternativo**: En caso de que no haya datos disponibles, se muestra un mensaje dentro del contenedor informando que no hay datos para mostrar.

### Manejo de Gráficos
- **Opciones de Configuración**: Cada gráfico tiene opciones que se comparten y algunas específicas, incluyendo configuraciones responsivas para ajustar la visualización según el tamaño de pantalla.
- **Leyenda**: Los gráficos incluyen una leyenda detallada que muestra el porcentaje correspondiente para cada valor, lo cual facilita la interpretación de los datos.

### Exportar Funciones
- Tanto `initBarCharts` como `initDonutChart` se exportan y se asignan al objeto global `window` para facilitar su acceso desde otras partes del código.

### Consideraciones Generales
- **Validación y Manejo de Errores**: Se verifica si los elementos del DOM existen antes de intentar renderizar un gráfico, y se registran mensajes de error si los elementos necesarios no se encuentran.
- **Enfoque Modular**: Al exportar las funciones, se facilita la reutilización y se promueve la modularidad del código, mejorando su mantenibilidad.


