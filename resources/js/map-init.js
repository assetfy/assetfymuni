document.addEventListener('DOMContentLoaded', function () {
    let mapInitialized = false;

    Livewire.on('initMap', () => {
        initializeMap();
    });

    function initializeMap() {
        const mapElement = document.getElementById('map');
        if (mapElement) {
            if (window.map && window.map instanceof L.Map) {
                window.map.off();
                window.map.remove();
            }

            window.map = L.map('map').setView([-27.4676, -58.8341], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(window.map);

            const searchControl = new GeoSearch.GeoSearchControl({
                provider: new GeoSearch.OpenStreetMapProvider(),
                style: 'bar',
                showMarker: true,
                retainZoomLevel: false,
                autoClose: true,
                searchLabel:  'Buscar dirección...',
                keepResult: true
            });

            window.map.addControl(searchControl);

            let marker;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const latlng = [position.coords.latitude, position.coords.longitude];
                    window.map.setView(latlng, 14);

                    marker = L.marker(latlng).addTo(window.map);

                    const component = Livewire.find(mapElement.closest('[wire\\:id]').getAttribute('wire:id'));
                    component.set('lat', latlng[0]);
                    component.set('long', latlng[1]);
                    component.call('handleGeolocation', latlng[0], latlng[1]);
                });
            }

            window.map.on('click', function(e) {
                const latlng = e.latlng;
                if (marker) {
                    window.map.removeLayer(marker);
                }
                marker = L.marker(latlng).addTo(window.map);
                const component = Livewire.find(mapElement.closest('[wire\\:id]').getAttribute('wire:id'));
                component.set('lat', latlng.lat);
                component.set('long', latlng.lng);
                component.call('setAddress', latlng.lat, latlng.lng);
            });

            window.map.on('geosearch/showlocation', function(result) {
                const latlng = result.location;
                if (marker) {
                    window.map.removeLayer(marker);
                }
                marker = L.marker([latlng.y, latlng.x]).addTo(window.map);
                const component = Livewire.find(mapElement.closest('[wire\\:id]').getAttribute('wire:id'));
                component.set('lat', latlng.y);
                component.set('long', latlng.x);
                component.call('setAddress', latlng.y, latlng.x);
            });

            setTimeout(() => {
                window.map.invalidateSize();
            }, 500);

            mapInitialized = true; // Marcar el mapa como inicializado
        }
    }
});
