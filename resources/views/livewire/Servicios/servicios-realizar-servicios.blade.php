<div class="flex justify-center items-start min-h-screen bg-gray-100 py-10">
    <div class="was-validated bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <form wire:submit.prevent="save" id="serviceForm">
            <div class="mb-4">
                <x-label value="Servicios" />
                @foreach($servicios as $servicio)
                <h4>{{ $servicio->nombre }}</h4>
                @endforeach
            </div>
            <div class="mb-4">
                <x-label value="Descripción" />
                <h4>{{ $servicio->descripcion }}</h4>
            </div>
            <div class="mb-4">
                <x-label value="Activo" />
                <h4>{{ $activo->nombre}}</h4>
            </div>
            <div class="mb-4">
                <label for="fechaHora">Fecha y Hora:</label>
                <input type="datetime-local" id="fechaHora" class="w-full" wire:model="fechaHora" value="{{ $fechaHora }}" />
                <x-input-error for="fechaHora" />
            </div>
            <div class="mb-3">
                <label for="validationTextarea" class="form-label">Descripcion de la tarea realizada</label>
                <textarea class="form-control is-invalid" id="validationTextarea" wire:model="comentarios" placeholder="Descripcion de la tarea a realizada" required></textarea>
            </div>
            <div class="mb-4" id="foto-container" style="display: none;">
                <label for="foto">Adjuntar Fotos:</label>
                <input type="file" id="foto" wire:model="foto">
                @error('imagen')
                <span class="error">{{ $message }}</span>
                @enderror
            </div>
            <div class="flex justify-center mt-4" id="camera-container" style="display: none;">
                <video id="theVideo" autoplay muted></video>
                <canvas id="theCanvas"></canvas>
            </div>
            <div class="d-grid gap-2 d-md-block" id="camera-controls" style="display: none;">
                <button type="button" class="btn btn-primary" id="btnCapture">Tomar foto</button>
                <button type="button" class="btn btn-primary" id="btnDownloadImage" hidden>Descargar imagen</button>
                <button type="button" class="btn btn-primary" id="btnSendImageToServer" hidden>Guardar imagen</button>
            </div>
            <div class="flex justify-center mt-4" id="start-camera-container">
                <button type="button" class="btn btn-primary" id="btnStartCamera" onclick="startCamera(event)">Iniciar cámara</button>
            </div>
            <br>
            <div class="flex justify-end space-x-4" id="form-buttons">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Realizar Servicio
                </x-danger-button>
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </form>
    </div>
</div>
<script src="{{ asset('js/script.js') }}" defer></script>

<script>
    function isMobileDevice() {
        return /Mobi|Android/i.test(navigator.userAgent);
    }

    function startCamera(event) {
        event.preventDefault();
        document.getElementById('start-camera-container').style.display = 'none';
        document.getElementById('foto-container').style.display = 'none'; // Ocultar contenedor de foto
        document.getElementById('camera-container').style.display = 'block';
        document.getElementById('camera-controls').style.display = 'block';
    }

    window.addEventListener('DOMContentLoaded', (event) => {
        const isMobile = isMobileDevice();
        const fotoContainer = document.getElementById('foto-container');
        const startCameraContainer = document.getElementById('start-camera-container');
        const cameraControls = document.getElementById('camera-controls');
        const cameraContainer = document.getElementById('camera-container');

        if (isMobile) {
            fotoContainer.style.display = 'none';
            startCameraContainer.style.display = 'block';
            cameraContainer.style.display = 'none';
            cameraControls.style.display = 'none';
        } else {
            fotoContainer.style.display = 'block';
            startCameraContainer.style.display = 'none';
            cameraContainer.style.display = 'none';
            cameraControls.style.display = 'none';
        }

        document.getElementById('btnCapture').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('btnDownloadImage').hidden = false;
            document.getElementById('btnSendImageToServer').hidden = false;
        });

        // Función para ajustar el tamaño del canvas y mostrar la imagen capturada
        function displayCapturedImage() {
            const containerWidth = cameraContainer.offsetWidth;
            const containerHeight = cameraContainer.offsetHeight;
            canvas.width = containerWidth;
            canvas.height = containerHeight;
            context.drawImage(video, 0, 0, containerWidth, containerHeight);
        }
    });

    document.querySelectorAll('#btnCapture, #btnStartCamera').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
        });
    });
</script>