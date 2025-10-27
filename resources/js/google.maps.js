
document.addEventListener('DOMContentLoaded', function() {
  // ➤ Diccionarios para guardar múltiples mapas y sus marcadores
  const maps    = {};
  const markers = {};

  const corrientes = { lat: -27.4799, lng: -58.8361 };

  // ➤ Escucha el evento y recibe el id dinámico (mapModal1, mapModal2, ...)
  window.addEventListener('mapModalShown', event => {
    // Livewire 3 te manda detail = ['mapModal1'] o detail.id = 'mapModal1'
    let id;
    if (event.detail && event.detail.id)        id = event.detail.id;
    else if (Array.isArray(event.detail))       id = event.detail[0];
    else if (typeof event.detail === 'string')  id = event.detail;

    if (!id) return console.error('ID inválido en mapModalShown:', event.detail);

    const container = document.getElementById(id);
    if (!container) return console.error('No existe #'+id);

    // ➤ Si ya existe un mapa para este id, sólo redimensiona
    if (maps[id]) {
      google.maps.event.trigger(maps[id], 'resize');
      return;
    }

    // ➤ Si no existe, créalo y guárdalo en maps[id]
    maps[id] = initializeMap(container, id);
  });

  function initializeMap(container, id) {
    const map = new google.maps.Map(container, {
      center: corrientes,
      zoom: 13,
      mapTypeId: 'roadmap',
      disableDefaultUI: true,
      zoomControl: true,
    });

    addSearchBox(map, id);

    map.addListener('click', e => {
      // cada clic mueve solo su marcador
      if (markers[id]) markers[id].setMap(null);
      markers[id] = new google.maps.Marker({ position: e.latLng, map });
      // y envía coords a Livewire
      const wireId = container.closest('[wire\\:id]').getAttribute('wire:id');
      const comp   = Livewire.find(wireId);
      if (comp) {
        comp.set('lat', e.latLng.lat());
        comp.set('long', e.latLng.lng());
        comp.call('setAddress', e.latLng.lat(), e.latLng.lng());
      }
    });

    return map;
  }

  function addSearchBox(map, id) {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = 'Buscar ubicación';
    Object.assign(input.style, {
      width: '320px',
      padding: '10px',
      border: '1px solid #ccc',
      borderRadius: '4px',
      marginTop: '10px',
    });
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

    const searchBox = new google.maps.places.SearchBox(input);
    searchBox.addListener('places_changed', () => {
      const places = searchBox.getPlaces();
      if (!places.length) return;
      const loc = places[0].geometry.location;

      // mueve marcador
      if (markers[id]) markers[id].setMap(null);
      markers[id] = new google.maps.Marker({ position: loc, map });

      // re-centra y zoom
      map.setCenter(loc);
      map.setZoom(15);

      // notifica a Livewire
      const wireId = map.getDiv().closest('[wire\\:id]').getAttribute('wire:id');
      const comp   = Livewire.find(wireId);
      if (comp) {
        comp.set('lat', loc.lat());
        comp.set('long', loc.lng());
        comp.call('setAddress', loc.lat(), loc.lng());
      }
    });
    if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      const userLoc = {
        lat: pos.coords.latitude,
        lng: pos.coords.longitude
      };
      map.setCenter(userLoc);
      map.setZoom(14);
    }, err => {
      console.warn('Geolocalización denegada o no disponible:', err);
    });
  }
  }
});
