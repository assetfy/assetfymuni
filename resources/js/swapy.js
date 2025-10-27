document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.sortable-container');

    containers.forEach(container => {
        if (container) {
            const swapy = Swapy.createSwapy(container, {
                animation: 'dynamic' // Puedes cambiar esto a 'spring' o 'none'
            });

            // Escuchar eventos de intercambio
            swapy.onSwap((event) => {
                console.log(event.data.object);
                console.log(event.data.array);
                console.log(event.data.map);
            });
        }
    });
});
