<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl" class="modal-bien">
        <x-slot name="title">
            <div class="text-center text-2xl font-bold text-gray-800">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2">Crear Bien</h2>
            </div>
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Barra de progreso de pasos -->
                <div class="flex items-center justify-between w-full max-w-3xl mx-auto mb-8 px-4">
                    @for ($i = 1; $i <= $maxSteps; $i++)
                        <div class="flex items-center w-full relative">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                {{ $currentStep >= $i ? 'bg-black text-white' : 'bg-gray-300 text-gray-600' }}">
                                {{ $i }}
                            </div>

                            @if ($i < $maxSteps)
                                <div class="flex-1 h-1 {{ $currentStep > $i ? 'bg-black' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>
                    @endfor
                </div>
                <!-- Paso 1 -->
                @if ($currentStep === 1)
                    @include('livewire.activos.partials.step1')
                @endif
                <!-- Fin de Paso 1 -->
                <!-- Paso 2 -->
                @if ($currentStep === 2 && !$inmueble)
                    <div wire:init="prepararPaso2" wire:key="step2">
                        @include('livewire.activos.partials.step3')
                    </div>
                @endif
                <!-- Fin de Paso 2 -->

                <!-- Paso 3 -->
                @if ($currentStep === 3)
                    @include('livewire.activos.partials.step2')
                @endif
                <!-- Fin de Paso 3 -->

                <!-- Paso 4 -->
                @if ($currentStep === 4)
                    @include('livewire.activos.partials.step4')
                @endif
                <!-- Fin de Paso 4 -->

                <!-- Paso 5: Detalles de Compra y Garantía -->
                @if ($currentStep === 5)
                    <div wire:init="prepararPaso6" wire:key="step6-organigrama">
                        @include('livewire.activos.partials.step6')
                    </div>
                @endif

                <!-- Paso 6 -->
                {{-- @if ($currentStep === 6 && !$esUsuario && !$empleado)
                    <div wire:init="prepararPaso6" wire:key="step6-organigrama">
                        @include('livewire.activos.partials.step6')
                    </div>
                @endif --}}
            </x-slot>
            <x-slot name="footer">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <!-- Botón "Anterior" se muestra en todos los pasos excepto el primero -->
                    @if ($currentStep !== 1)
                        <x-secondary-button wire:click="previousStep">
                            Anterior
                        </x-secondary-button>
                    @endif

                    @if ($currentStep === 5 && ($esUsuario || $empleado))
                        <!-- Solo para usuarios -->
                        <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                            Crear Activo
                        </x-danger-button>
                        <span
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                            wire:loading wire:target="save">Cargando...</span>
                    @endif

                    <!-- Botón "Siguiente" se muestra en todos los pasos excepto el último -->
                    @if ($currentStep !== $maxSteps && !($currentStep === $maxSteps && ($esUsuario || $empleado)))
                        <x-danger-button wire:click="nextStep">
                            Siguiente
                        </x-danger-button>
                    @endif

                    <!-- Botón "Crear Activo" y indicador de carga en el último paso -->
                    @if ($currentStep === $maxSteps)
                        <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                            Crear Activo
                        </x-danger-button>
                        <span
                            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                            wire:loading wire:target="save">Cargando...</span>
                    @endif
                    <!-- Botón "Cancelar" siempre visible -->
                    <x-secondary-button class="mr-2" wire:click="close">
                        Cancelar
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>

    <style>
        .modal-bien {
            z-index: 50 !important;
        }
    </style>
</div>


<!-- Scripts para manejar la cámara (si es necesario) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para detectar si es un dispositivo móvil
        function isMobileDevice() {
            return /Mobi|Android/i.test(navigator.userAgent);
        }

        // Función para iniciar la cámara
        async function startCamera(event) {
            event.preventDefault();

            // Ocultamos/mostramos contenedores según sea necesario
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

                // Volvemos a mostrar el contenedor original
                document.getElementById('start-camera-container').style.display = 'block';
                document.getElementById('foto-container').style.display = 'block';
                document.getElementById('camera-container').style.display = 'none';
                document.getElementById('camera-controls').style.display = 'none';
            }
        }

        // Botón para iniciar la cámara (versión móvil)
        const btnStartCamera = document.getElementById('btnStartCamera');
        if (btnStartCamera) {
            btnStartCamera.addEventListener('click', startCamera);
        }

        // Botón para capturar la imagen
        const btnCapture = document.getElementById('btnCapture');
        if (btnCapture) {
            btnCapture.addEventListener('click', function(event) {
                event.preventDefault();
                const canvas = document.getElementById('theCanvas');
                const video = document.getElementById('theVideo');
                const context = canvas.getContext('2d');

                // Ajustamos el canvas al tamaño del video
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Dibujamos el frame de la cámara en el canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convertimos a Base64
                const imageDataURL = canvas.toDataURL('image/png');

                // Llamamos a Livewire para guardar la imagen
                @this.saveCapturedPhoto(imageDataURL);
            });
        }

        // Botón para finalizar la captura y apagar la cámara
        const btnFinishCapture = document.getElementById('btnFinishCapture');
        if (btnFinishCapture) {
            btnFinishCapture.addEventListener('click', function(event) {
                event.preventDefault();
                const video = document.getElementById('theVideo');
                const stream = video.srcObject;

                // Detenemos el stream de la cámara
                if (stream) {
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                    video.srcObject = null;
                }

                // Ajustamos la vista de vuelta al contenedor de fotos
                document.getElementById('camera-container').style.display = 'none';
                document.getElementById('camera-controls').style.display = 'none';
                document.getElementById('foto-container').style.display = 'block';
                document.getElementById('start-camera-container').style.display = 'block';
            });
        }

        // Detectamos si es móvil para mostrar/ocultar contenedores
        const isMobile = isMobileDevice();
        const fotoContainer = document.getElementById('foto-container');
        const startCameraContainer = document.getElementById('start-camera-container');
        const cameraContainer = document.getElementById('camera-container');
        const cameraControls = document.getElementById('camera-controls');

        if (isMobile) {
            // Mostrar opciones de cámara, ocultar fotos
            fotoContainer.style.display = 'none';
            startCameraContainer.style.display = 'block';
            cameraContainer.style.display = 'none';
            cameraControls.style.display = 'none';
        }
    });
</script>
