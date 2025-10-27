<div class="min-h-screen flex flex-col items-start justify-start py-16 relative">
    <div class="w-full max-w-8xl p-12 space-y-6 bg-white mx-auto">
        <!-- Botón Volver -->
        <div class="text-first mt-4" style="padding-bottom: 20px;">
            <a href="{{ $previousUrl }}" class="btn-3 text-white rounded-lg">
                Volver
            </a>
        </div>

        <!-- Línea de progreso -->
        <div class="relative w-full flex items-center mb-8">
            <div class="flex-1 h-1 bg-gray-300 relative overflow-hidden">
                <div class="absolute h-1 bg-blue-500 animate-progress"></div>
            </div>
            <div class="absolute top-1/2 transform -translate-y-1/2 w-full flex justify-between">
                @for ($i = 0; $i < 4; $i++)
                    <div class="relative flex justify-center items-center">
                        <div class="absolute h-6 w-6 bg-white border-2 border-blue-500 rounded-full"></div>
                        <div class="absolute h-6 w-6 rounded-full animate-circle-progress delay-{{ $i * 500 }}ms">
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <h2 class="text-2xl font-bold text-center mb-6">Envía el informe del servicio brindado</h2>
        <p class="text-center mb-6">
            Ya te encuentras a punto de finalizar el proceso servicio brindado. Es momento de
            subir el informe que valida el servicio brindado y que le permitirá al propietario llevar un registro de los
            servicios que afectaron su inmueble.
        </p>

        <form wire:submit.prevent="save" id="serviceForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-concierge-bell text-blue-600"></i>
                        <x-label value="Servicios" class="block text-lg font-medium text-gray-700" />
                    </div>
                    @foreach ($servicios as $servicioItem)
                        <h4 class="text-base text-gray-800">{{ $servicioItem->nombre }}</h4>
                    @endforeach
                </div>

                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        <x-label value="Descripción" class="block text-lg font-medium text-gray-700" />
                    </div>
                    <h4 class="text-base text-gray-800">{{ $servicio->descripcion }}</h4>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-toggle-on text-blue-600"></i>
                        <x-label value="Activo" class="block text-lg font-medium text-gray-700" />
                    </div>
                    <h4 class="text-base text-gray-800">{{ $activo->nombre }}</h4>
                </div>
                <!-- Mostrar el técnico asignado si existe -->
                @if ($tecnicoAsignado)
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-700 font-medium">Técnico Asignado:</span>
                        <i class="fas fa-hard-hat text-yellow-500"></i> <!-- Ícono de casco -->
                        <h4 class="text-base font-semibold text-gray-800">{{ $tecnicoAsignado->name }}</h4>
                    </div>
                @endif

            </div>

            <div class="space-y-4">
                <label for="fechaHora" class="block text-lg font-medium text-gray-700">Fecha y Hora:</label>
                <input type="datetime-local" id="fechaHora"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    wire:model="fechaHora" />
                <x-input-error for="fechaHora" class="mt-2" />
            </div>

            <div class="space-y-4">
                <label for="validationTextarea" class="block text-lg font-medium text-gray-700">Descripción de la tarea
                    realizada</label>
                <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    id="validationTextarea" wire:model="comentarios" required></textarea>
                <x-input-error for="comentarios" class="mt-2" />
            </div>

            <!-- Subida de Fotos -->
            <div id="foto-container" class="space-y-4">
                <label class="block text-lg font-medium text-gray-700">Adjuntar Fotos:</label>
                <div class="flex flex-wrap gap-4">
                    <!-- Mostrar vistas previas de las fotos subidas -->
                    @foreach ($fotos as $index => $foto)
                        <div wire:key="uploaded-photo-{{ $index }}"
                            class="w-32 h-32 bg-white rounded-lg shadow-md overflow-hidden relative">
                            <img src="{{ $foto->temporaryUrl() }}" class="object-cover w-full h-full">
                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1"
                                wire:click="removeFoto({{ $index }})">&times;</button>
                        </div>
                    @endforeach

                    <!-- Mostrar vistas previas de las fotos capturadas -->
                    @foreach ($capturedPhotos as $index => $capturedPhoto)
                        <div wire:key="captured-photo-{{ $index }}"
                            class="w-32 h-32 bg-white rounded-lg shadow-md overflow-hidden relative">
                            <img src="{{ $capturedPhoto }}" class="object-cover w-full h-full">
                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1"
                                wire:click="removeCapturedPhoto({{ $index }})">&times;</button>
                        </div>
                    @endforeach

                    <!-- Cuadrado '+' para agregar más fotos -->
                    @if (count($fotos) + count($capturedPhotos) < 10)
                        <div class="flex items-center justify-center w-32 h-32 bg-gray-100 rounded-lg cursor-pointer border-2 border-dashed border-gray-400"
                            onclick="document.getElementById('foto').click()">
                            <span class="text-2xl text-gray-500">+</span>
                        </div>
                    @endif
                </div>
                <!-- Input de archivos oculto con nuevo wire:model -->
                <input type="file" id="foto" multiple class="hidden" wire:model="nuevasFotos">
                @error('fotos.*')
                    <span class="error text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contenedor para la cámara -->
            <div class="flex flex-col items-center hidden" id="camera-container">
                <video id="theVideo" class="rounded-md shadow-md" autoplay muted></video>
                <canvas id="theCanvas" class="mt-4 rounded-md shadow-md" style="display:none;"></canvas>
            </div>

            <div class="flex space-x-4 hidden" id="camera-controls">
                <button type="button" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-700"
                    id="btnCapture">Tomar foto</button>
                <button type="button" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-700"
                    id="btnFinishCapture">Terminar captura</button>
            </div>

            <!-- Botón para iniciar la cámara en dispositivos móviles -->
            <div class="flex justify-center sm:hidden" id="start-camera-container">
                <button type="button" class="bg-blue-600 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-700"
                    onclick="startCamera(event)">Iniciar cámara</button>
            </div>

            <div class="flex justify-end space-x-4" id="form-buttons">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button type="submit" wire:loading.remove wire:target="save">
                    Realizar Servicio
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </form>
    </div>
    <style>
        @keyframes progress {
            0% {
                width: 0;
            }

            100% {
                width: 100%;
            }
        }

        .animate-progress {
            top: 0;
            left: 0;
            width: 0;
            background: #1E3A8A;
            animation: progress 2s forwards;
        }

        @keyframes circle-progress {
            0% {
                background-color: white;
            }

            100% {
                background-color: #1E3A8A;
            }
        }

        .animate-circle-progress {
            animation: circle-progress 2s forwards;
        }

        .delay-0 {
            animation-delay: 0s;
        }

        .delay-500ms {
            animation-delay: 0.5s;
        }

        .delay-1000ms {
            animation-delay: 1s;
        }

        .delay-1500ms {
            animation-delay: 1.5s;
        }

        /* Estilos adicionales para las tarjetas de imágenes */
        .rounded-lg {
            border-radius: 0.5rem;
        }

        .shadow-md {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .bg-white {
            background-color: white;
        }

        .bg-gray-100 {
            background-color: #f7fafc;
        }

        .bg-red-500 {
            background-color: #f56565;
        }

        .text-white {
            color: white;
        }

        .text-gray-500 {
            color: #a0aec0;
        }

        .border-dashed {
            border-style: dashed;
        }

        .border-gray-400 {
            border-color: #cbd5e0;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    <script>
        function isMobileDevice() {
            return /Mobi|Android/i.test(navigator.userAgent);
        }

        async function startCamera(event) {
            event.preventDefault();
            document.getElementById('start-camera-container').style.display = 'none';
            document.getElementById('foto-container').style.display = 'none';
            document.getElementById('camera-container').style.display = 'block';
            document.getElementById('camera-controls').style.display = 'flex';

            const video = document.getElementById('theVideo');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                video.srcObject = stream;
                video.play();
            } catch (error) {
                console.error("Error accessing the camera: ", error);
                alert("No se pudo acceder a la cámara. Por favor, verifica los permisos.");
                document.getElementById('start-camera-container').style.display = 'block';
                document.getElementById('foto-container').style.display = 'block';
                document.getElementById('camera-container').style.display = 'none';
                document.getElementById('camera-controls').style.display = 'none';
            }
        }

        document.getElementById('btnCapture').addEventListener('click', function(event) {
            event.preventDefault();
            const canvas = document.getElementById('theCanvas');
            const video = document.getElementById('theVideo');
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageDataURL = canvas.toDataURL('image/png');

            // Enviar la imagen capturada a Livewire
            @this.saveCapturedPhoto(imageDataURL);
        });

        document.getElementById('btnFinishCapture').addEventListener('click', function(event) {
            event.preventDefault();

            // Detener la cámara
            const video = document.getElementById('theVideo');
            const stream = video.srcObject;
            if (stream) {
                const tracks = stream.getTracks();

                tracks.forEach(function(track) {
                    track.stop();
                });

                video.srcObject = null;
            }

            // Ocultar la cámara y mostrar el formulario
            document.getElementById('camera-container').style.display = 'none';
            document.getElementById('camera-controls').style.display = 'none';
            document.getElementById('foto-container').style.display = 'block';
            document.getElementById('start-camera-container').style.display = 'block';
        });

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
        });

        // Manejar el evento de feedback personalizado
        window.addEventListener('servicio-guardado', () => {
            alert('Servicio realizado y fotos guardadas correctamente.');
            // Opcional: Redirigir o limpiar formularios adicionales
        });
    </script>
</div>
