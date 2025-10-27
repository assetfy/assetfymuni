**Documento sobre la Integración del Chatbot con Preguntas Frecuentes**

Este documento describe cómo se implementa una funcionalidad de chatbot que permite a los usuarios interactuar con un conjunto de preguntas frecuentes (FAQs) relacionadas con diferentes tipos de paneles, como "Empresa", "Prestadora", "Usuario" y "Estado". Además, se detalla el uso de un Web Worker para manejar la lógica y la personalización de respuestas basadas en la vista actual del usuario.

### 1. Listado de Tutoriales y FAQs

La aplicación define una lista de vistas que tienen tutoriales disponibles:

- **Lista de vistas con tutoriales**:
  ```javascript
  const tutorials = ['dashboard-usuario', 'dashboard-empresas', 'dashboard-estado'];
  ```

- **Definición de FAQs**:
  El objeto `faqs` contiene preguntas y respuestas para cada tipo de panel (“Empresa”, “Prestadora”, “Usuario”, “Estado”). Las respuestas se personalizan usando placeholders (`{view}`, `{userName}`), que se reemplazan dinámicamente con el nombre de la vista actual y el nombre del usuario.

### 2. Uso de Web Worker para Manejo de FAQs

Para mejorar el rendimiento y gestionar de manera eficiente la generación de FAQs, se utiliza un **Web Worker**. Este componente permite ejecutar el código en un hilo separado, evitando bloquear la interfaz de usuario mientras se procesa la lógica del chatbot.

- **Código del Web Worker**:
  El Web Worker importa la librería `fuse.js` para manejar la búsqueda de preguntas frecuentes. También se encarga de filtrar las preguntas relacionadas con el tutorial si la vista actual no tiene un tutorial definido.
  ```javascript
  const workerCode = `
  importScripts('https://cdn.jsdelivr.net/npm/fuse.js@6.4.6/dist/fuse.min.js');
  ...
  `;
  ```

- **Envío de Mensaje al Worker**:
  Cuando el chatbot se inicializa, se envía un mensaje al Web Worker con la información necesaria, como el tipo de panel, la vista y el nombre del usuario.

### 3. Inicialización del Chatbot

- **sendFAQs()**:
  Esta función envía un mensaje al Web Worker para obtener las FAQs del panel actual y las muestra en el chat.
  
- **addFAQOption()**:
  Crea los elementos de botón para cada pregunta frecuente. Cuando el botón es presionado, el chatbot muestra la respuesta correspondiente.

### 4. Personalización de Mensajes y Respuestas

El bot personaliza las respuestas usando las variables `{view}` y `{userName}`:
- **Vista Actual**: Muestra el nombre de la vista en la que se encuentra el usuario.
- **Nombre del Usuario**: Personaliza la experiencia saludando al usuario por su nombre.

### 5. Ejecución de Tutoriales

Si una de las FAQs seleccionadas incluye "tutorial", el chatbot inicia el tutorial correspondiente a la vista actual. Para ello se utiliza `introJs()`:

- **initializeTutorial()**: Esta función configura y ejecuta un conjunto de pasos para guiar al usuario a través de la interfaz de la vista actual.

### 6. Interacción con la Interfaz

El chatbot tiene botones de FAQ y de reinicio para mostrar las preguntas frecuentes nuevamente. Además, tiene un mensaje de saludo que se muestra al usuario cuando se inicia el chatbot:

- **Saludo Inicial**: Muestra un mensaje de bienvenida al usuario con su nombre.
- **Mostrar Preguntas Frecuentes**: Un botón de "Mostrar Preguntas Frecuentes" permite al usuario limpiar el chat y volver a ver las preguntas disponibles.

