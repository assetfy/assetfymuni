Livewire.on('initializeInformativeMap', function(coords) {
    console.log('Evento initializeInformativeMap recibido:', coords);
    
    // Asegúrate de que coords es un array con dos elementos
    if (!Array.isArray(coords) || coords.length !== 2) {
        console.error('Formato de coordenadas inválido:', coords);
        return;
    }

    const [lat, lng] = coords;

    // Validar que lat y lng sean números
    if (typeof lat !== 'number' || typeof lng !== 'number') {
        console.error('Coordenadas no son números:', { lat, lng });
        return;
    }

    const mapElement = document.getElementById('mapModal');
    if (!mapElement) {
        console.error('Elemento con id "mapModal" no encontrado.');
        return;
    }

    // Inicializar el mapa si no está ya inicializado
    if (!mapInstance) {
        mapInstance = new google.maps.Map(mapElement, {
            center: { lat: lat, lng: lng },
            zoom: 15,
            mapTypeId: 'roadmap',
            disableDefaultUI: false,
            zoomControl: true,
        });
        console.log('Mapa inicializado:', mapInstance);

        // Agregar el marcador fijo
        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: mapInstance,
            draggable: false,
        });
    } else {
        // Si el mapa ya está inicializado, solo actualizar su centro y marcador
        mapInstance.setCenter({ lat: lat, lng: lng });
        mapInstance.setZoom(15);
        console.log('Mapa actualizado:', mapInstance);

        if (marker) {
            marker.setPosition({ lat: lat, lng: lng });
        } else {
            marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: mapInstance,
                draggable: false,
            });
        }
    }

    // Deshabilitar la interacción del usuario para agregar/mover marcadores
    google.maps.event.clearListeners(mapInstance, 'click');
});
