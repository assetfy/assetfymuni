Livewire.on('showActivoMap', (coords) => {
    console.log('==== showActivoMap event disparado ====');
    console.log('Payload coords:', coords);

    // 1. Extraer lat, lng y mapId (el id del <div> donde quieres mostrar el mapa)
    let latRaw, lngRaw, mapId;

    // Puede llegar como Array o como Objeto
    if (Array.isArray(coords) && coords.length > 0) {
        latRaw = coords[0].lat;
        lngRaw = coords[0].lng;
        mapId = coords[0].mapId;  // El id que enviamos desde Livewire
    } else {
        latRaw = coords.lat;
        lngRaw = coords.lng;
        mapId = coords.mapId;
    }

    // 2. Convertir a número, o valor por defecto si vienen mal
    let lat = parseFloat(latRaw);
    let lng = parseFloat(lngRaw);
    if (isNaN(lat) || isNaN(lng)) {
        lat = -27.4799;   // Coordenadas por defecto
        lng = -58.8361;
    }

    // 3. Si no viene mapId, usamos uno por defecto (por si acaso)
    if (!mapId) {
        mapId = 'miniMap'; 
    }

    console.log(`Mapa con lat: ${lat}, lng: ${lng}, en contenedor: #${mapId}`);

    // 4. Verificar si existe el contenedor en el DOM
    const mapElement = document.getElementById(mapId);
    if (!mapElement) {
        console.warn(`No existe el elemento con id="#${mapId}" en el DOM.`);
        return;
    }

    // 5. Creamos/mostramos el mapa (con pequeño retraso por si el modal se está abriendo)
    setTimeout(() => {
        const map = new google.maps.Map(mapElement, {
            center: { lat, lng },
            zoom: 14,
            mapTypeId: 'roadmap',
        });

        new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: 'Ubicación del activo',
        });
    }, 300);
});
