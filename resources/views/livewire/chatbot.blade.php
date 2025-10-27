<div id="chatbot">
    <div id="chatbot-icon" class="breathing">
        <i class="fas fa-robot"></i>
    </div>
    <div id="chatbox" class="hidden">
        <div id="chatbox-header">
            <h2>Asistente Ayuda</h2>
            <button id="close-chatbox">&times;</button>
        </div>
        <div id="chatbox-body">
            <div id="chatOutput"></div>
            <div id="userInputContainer">
                <input type="text" id="userInput" placeholder="Escribe tu pregunta">
                <button id="sendButton">Enviar</button>
            </div>
        </div>
    </div>

    <style>
        /* Estilos generales del chatbot */
        #chatbox {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        #chatbox-header {
            padding: 10px;
            background-color: #4a90e2;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chatbox-body {
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 300px;
            overflow-y: auto;
        }

        #userInputContainer {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            width: 100%;
        }

        #userInput {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-right: 10px;
        }

        #sendButton {
            padding: 8px 12px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #sendButton:hover {
            background-color: #357ABD;
        }

        .faq-option {
            margin: 10px 0;
            padding: 10px;
            border-radius: 20px;
            background-color: white;
            color: black;
            border: 2px solid #4a90e2;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
            width: 85%;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .faq-option:hover {
            background-color: #357ABD;
            color: white;
            transform: scale(1.05);
        }

        .chat-message {
            margin: 10px;
            padding: 10px 15px;
            border-radius: 15px;
            background-color: #f1f1f1;
            color: black;
            text-align: left;
            width: 100%;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);
        }

        .chat-message.bot {
            background-color: #e9f6ff;
            display: flex;
            align-items: center;
        }

        .chat-message.user {
            background-color: #2e0ead;
            align-self: flex-end;
        }

        .chat-message i {
            margin-right: 10px;
        }

        /* Estilo para el botón de mostrar preguntas frecuentes */
        .reset-button {
            margin: 20px auto 10px auto;
            padding: 10px 20px;
            background-color: #4a90e2;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            display: block;
            width: fit-content;
        }

        .reset-button:hover {
            background-color: #357ABD;
            transform: scale(1.05);
        }

        /* Ocultar el input y el botón de envío si el usuario solo puede seleccionar preguntas frecuentes */
        #userInputContainer {
            display: none; /* Ocultar el input y el botón para enviar preguntas manualmente */
        }
    </style>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userName = @json($user->name);
        const primeraSession = @json($user->primera_session);
        initializeChatbot(userName, primeraSession);
    });
</script>
