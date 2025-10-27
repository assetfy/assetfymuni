 document.addEventListener("DOMContentLoaded", function() {
        const videoWidth = 420;
        const videoHeight = 320;
        const videoTag = document.getElementById("theVideo");
        const canvasTag = document.getElementById("theCanvas");
        const btnCapture = document.getElementById("btnCapture");
        const btnDownloadImage = document.getElementById("btnDownloadImage");
        const btnSendImageToServer = document.getElementById("btnSendImageToServer");
        const btnStartCamera = document.getElementById("btnStartCamera");

        let cameraActive = false; // Variable para rastrear el estado de la cámara
        let stream = null; // Variable para almacenar el stream de la cámara

        // Establecer estado inicial de los botones
        btnCapture.disabled = true;
        btnDownloadImage.disabled = true;
        btnSendImageToServer.disabled = true;

        // Set video and canvas attributes
        videoTag.setAttribute("width", videoWidth);
        videoTag.setAttribute("height", videoHeight);
        canvasTag.setAttribute("width", videoWidth);
        canvasTag.setAttribute("height", videoHeight);

        // Función para verificar si es un dispositivo móvil
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        // Verificar si es un dispositivo móvil y activar la cámara
        if (isMobileDevice()) {
            btnStartCamera.disabled = false;
        } else {
            btnStartCamera.disabled = true;
        }

        // Iniciar cámara
        btnStartCamera.addEventListener("click", async () => {
            try {
                // Obtener acceso a la cámara
                stream = await navigator.mediaDevices.getUserMedia({ video: true });

                // Asignar el stream a la etiqueta de vídeo
                videoTag.srcObject = stream;
                cameraActive = true;
                btnCapture.disabled = false; // Habilitar botón de captura
            } catch (error) {
                console.error("Error al iniciar la cámara:", error);
            }
        });

        // Capturar foto
        btnCapture.addEventListener("click", (event) => {
            event.stopPropagation(); // Detener propagación del evento
            const canvasContext = canvasTag.getContext("2d");
            canvasContext.drawImage(videoTag, 0, 0, videoWidth, videoHeight);
            btnDownloadImage.disabled = false;
            btnSendImageToServer.disabled = false;
        });

        // Evento para enviar la imagen capturada al servidor
        btnSendImageToServer.addEventListener("click", async (event) => {
            event.stopPropagation(); // Detener propagación del evento
            const dataURL = canvasTag.toDataURL();
            const blob = await dataURLtoBlob(dataURL);
            const data = new FormData();
            data.append("capturedImage", blob, "capturedImage.png");

            try {
                const response = await axios.post("/upload.php", data, {
                    headers: { "Content-Type": "multipart/form-data" },
                });
                alert(response.data);
            } catch (error) {
                console.error("Error al enviar la imagen:", error);
            }
        });

        async function dataURLtoBlob(dataURL) {
            const arr = dataURL.split(",");
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            const n = bstr.length;
            const u8arr = new Uint8Array(n);
            for (let i = 0; i < n; i++) {
                u8arr[i] = bstr.charCodeAt(i);
            }
            return new Blob([u8arr], { type: mime });
        }
});
