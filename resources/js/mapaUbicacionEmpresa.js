// mapaUbicacionEmpresa.js

document.addEventListener('DOMContentLoaded', function () {
    Livewire.on('initMap', ({ lat, long }) => {
        console.log('llegue');
        
        const mapElement = document.getElementById('map');
        if (mapElement) {
            if (window.map && window.map instanceof L.Map) {
                window.map.off();
                window.map.remove();
            }

            if (lat && long) {
                window.map = L.map('map').setView([lat, long], 18); // Increased zoom level to 18
                L.marker([lat, long], { title: 'Ubicación de la empresa', color: 'red' }).addTo(window.map);
            } else {
                window.map = L.map('map').setView([-27.4676, -58.8341], 18); // Increased zoom level to 18
            }

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(window.map);

            setTimeout(() => {
                window.map.invalidateSize();
            }, 500);
        }
    });
});
