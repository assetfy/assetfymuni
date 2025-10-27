const tutorials = ['dashboard-usuario', 'dashboard-empresas', 'dashboard-estado'];  // Lista de vistas que tienen tutoriales definidos

const faqs = {
    'Empresa': [
        { question: "¿Qué es Vistas Aperturas?", answer: "Vistas Aperturas es una funcionalidad para empresas..." },
        { question: "¿Cómo registro una empresa?", answer: "Para registrar una empresa, debes completar el formulario específico para empresas..." },
        { question: "¿En qué vista estoy?", answer: "Estás en la vista actual: {view}, {userName}." },  // Respuesta personalizada
        { question: "tutorial", answer: "Claro, {userName}, puedes iniciar el tutorial de esta vista seleccionando esta opción." }
    ],
    'Prestadora': [
        { question: "¿Qué es Vistas Aperturas?", answer: "Vistas Aperturas es una funcionalidad para empresas..." },
        { question: "¿Cómo registro una empresa?", answer: "Para registrar una empresa, debes completar el formulario específico para empresas..." },
        { question: "¿En qué vista estoy?", answer: "Estás en la vista actual: {view}, {userName}." },  // Respuesta personalizada
        { question: "¿En qué perfil estoy?", answer: "Estás en el perfil: 'Estado', {userName}." },
        { question: "tutorial", answer: "Claro, {userName}, puedes iniciar el tutorial de esta vista seleccionando esta opción." }
    ],
    'Usuario': [
        { question: "¿Cómo accedo a mi dashboard?", answer: "Puedes acceder a tu dashboard desde el menú principal como usuario, {userName}." },
        { question: "¿Cómo accedo a mi dashboard de activos?", answer: "Puedes acceder a tu dashboard de activos desde el menú principal como usuario, {userName}." },
        { question: "¿En qué perfil me encuentro?", answer: "Actualmente estás en el perfil de Usuario, {userName}." },
        { question: "¿En qué vista estoy?", answer: "Estás en la vista actual: {view}, {userName}." },
        { question: "tutorial", answer: "Claro, {userName}, puedes iniciar el tutorial de esta vista seleccionando esta opción." }
    ],
    'Estado': [
        { question: "¿Cómo ver el estado de mi cuenta?", answer: "Puedes ver el estado de tu cuenta en la sección de estado, {userName}." },
        { question: "¿Cómo actualizar mi estado?", answer: "Para actualizar tu estado, ve a la sección de ajustes de estado, {userName}." },
        { question: "¿En qué vista estoy?", answer: "Estás en la vista actual: {view}, {userName}." },
        { question: "¿En qué perfil estoy?", answer: "Estás en el perfil: 'Estado', {userName}." },
        { question: "tutorial", answer: "Claro, {userName}, puedes iniciar el tutorial de esta vista seleccionando esta opción." }
    ]
};

const workerCode = `
importScripts('https://cdn.jsdelivr.net/npm/fuse.js@6.4.6/dist/fuse.min.js');

self.addEventListener('message', function(e) {
    const { panel, view, userName, tutorials } = e.data;
    let faq = ${JSON.stringify(faqs)}[panel];

    // Filtrar para eliminar la opción de tutorial si no está definida en la vista actual
    if (!tutorials.includes(view)) {
        faq = faq.filter(item => item.question.toLowerCase() !== 'tutorial');
    }

    const result = faq.map(item => ({
        question: item.question,
        answer: item.answer.replace("{view}", view).replace("{userName}", userName)
    }));

    self.postMessage(result);
}, false);
`;

const blob = new Blob([workerCode], { type: 'application/javascript' });
const worker = new Worker(URL.createObjectURL(blob));

let view = window.location.pathname.split('/').pop();
let userName = '';
let greetingMessage = null;

function sendFAQs() {
    const panel = 'Usuario'; // Puedes cambiar esto dinámicamente según la lógica de tu aplicación
    worker.postMessage({ panel, view, userName, tutorials });

    worker.onmessage = function(e) {
        const faqs = e.data;
        faqs.forEach(faq => {
            addFAQOption(faq.question, faq.answer);
        });
    };
}

function addFAQOption(question, answer) {
    const chatOutput = document.getElementById('chatOutput');
    const questionElement = document.createElement('button');
    questionElement.textContent = question;

    // Añade la clase de estilo estándar
    questionElement.classList.add('faq-option');

    questionElement.addEventListener('click', function() {
        addMessage(question, 'user');
        addMessage(answer, 'bot');
        showResetButton(); // Muestra el botón para mostrar preguntas frecuentes nuevamente

        if (question.toLowerCase().includes('tutorial')) {
            initializeTutorial(view);
        }

        if (greetingMessage) {
            greetingMessage.style.display = 'none'; // Oculta el saludo inicial
        }
    });

    chatOutput.appendChild(questionElement);
    chatOutput.scrollTop = chatOutput.scrollHeight;
}

function addMessage(message, sender) {
    const chatOutput = document.getElementById('chatOutput');
    const messageElement = document.createElement('div');
    messageElement.textContent = message;
    messageElement.classList.add('chat-message', sender);

    if (sender === 'bot') {
        const botIcon = document.createElement('i');
        botIcon.classList.add('fas', 'fa-robot');
        messageElement.insertBefore(botIcon, messageElement.firstChild);
    }

    chatOutput.appendChild(messageElement);
    chatOutput.scrollTop = chatOutput.scrollHeight;
}

function clearChat() {
    const chatOutput = document.getElementById('chatOutput');
    chatOutput.innerHTML = ''; // Limpia todo el contenido del chat

    // Muestra nuevamente el saludo inicial si no hay preguntas seleccionadas
    if (greetingMessage) {
        chatOutput.appendChild(greetingMessage);
    }
}

function showResetButton() {
    const chatOutput = document.getElementById('chatOutput');
    const resetButton = document.createElement('button');
    resetButton.textContent = "Mostrar Preguntas Frecuentes";
    resetButton.classList.add('reset-button');

    resetButton.addEventListener('click', function() {
        clearChat(); // Limpia el chat nuevamente
        sendFAQs(); // Muestra las preguntas frecuentes nuevamente
    });

    chatOutput.appendChild(resetButton);
    chatOutput.scrollTop = chatOutput.scrollHeight;
}

function initializeChatbot(userName) {
    view = window.location.pathname.split('/').pop();
    userName = userName || 'Usuario';

    document.getElementById('chatbot-icon').removeEventListener('click', handleChatbotIconClick);
    document.getElementById('close-chatbox').removeEventListener('click', handleCloseChatboxClick);

    document.getElementById('chatbot-icon').addEventListener('click', handleChatbotIconClick);
    document.getElementById('close-chatbox').addEventListener('click', handleCloseChatboxClick);

    greetingMessage = document.createElement('div');
    greetingMessage.classList.add('chat-message', 'bot');
    greetingMessage.innerHTML = `<i class="fas fa-robot"></i> Hola, ${userName}! Selecciona una pregunta para obtener más información:`;
    chatOutput.appendChild(greetingMessage); // Añadir el mensaje de saludo inicial

    sendFAQs(); // Mostrar preguntas frecuentes
}

function initializeTutorial(view) {
    if (view === 'dashboard-usuario') {
        introJs().setOptions({
            steps: [
                { intro: `Bienvenido al tutorial del dashboard, ${userName}.` },
                { element: document.querySelector('.flex.items-center.mb-8'), intro: `Este es tu perfil, ${userName}. Aquí puedes ver tu foto, nombre y correo electrónico.` },
                { element: document.querySelector('.custom-grid-cols-3'), intro: `Estas son las categorías de activos, ${userName}. Puedes hacer clic en cada una para ver más detalles.` },
                { element: document.querySelector('.space-y-4'), intro: `Aquí puedes ver las novedades y notificaciones recientes, ${userName}.` },
                { element: document.querySelector('.carousel'), intro: `Esta es la sección de ubicaciones, ${userName}. Puedes navegar entre las diferentes ubicaciones utilizando los botones de navegación.` }
            ]
        }).start();
    }
}

function handleChatbotIconClick() {
    const chatbox = document.getElementById('chatbox');
    chatbox.classList.toggle('hidden');
    if (!chatbox.classList.contains('hidden')) {
        chatbox.style.display = 'block';
    } else {
        chatbox.style.display = 'none';
    }
}

function handleCloseChatboxClick() {
    document.getElementById('chatbox').style.display = 'none';
}

window.initializeChatbot = initializeChatbot;
