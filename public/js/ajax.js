/**
 * Función para cargar contenido dinámicamente con AJAX usando jQuery.
 * @param {string} url - URL del contenido.
 * @param {Function} [callback] - Función opcional a ejecutar después de cargar el contenido.
 */
function loadContentWithAjax(url, callback) {
    const container = $('#main-content');

    if (container.length === 0) {
        console.error('Elemento #main-content no encontrado en el DOM.');
        return;
    }

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'html',
        success: function(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const mainContentElement = $(doc).find('#main-content');

            if (mainContentElement.length) {
                // Reemplazar el contenido sin manipular Alpine.js ni Livewire manualmente
                container.html(mainContentElement.html());

                // Inicializar otros componentes que no están manejados por Livewire
                initializeComponents();

                // Livewire 3 debería detectar automáticamente los nuevos componentes mediante MutationObserver

                // Actualizar el historial del navegador
                history.pushState(null, '', url);

                // Ejecutar la función de callback si se proporcionó
                if (callback && typeof callback === 'function') callback();

                // Emitir un evento personalizado utilizando eventos del navegador si necesitas realizar acciones adicionales
                window.dispatchEvent(new Event('contentLoaded'));
            } else {
                console.warn('Elemento #main-content no encontrado en la respuesta.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cargando contenido:', error);
        }
    });
}

/**
 * Manejar clics en enlaces con clase 'ajax-link'
 */
$(document).on('click', 'a.ajax-link', function (e) {
    e.preventDefault();
    const url = $(this).attr('href');
    if (url) {
        loadContentWithAjax(url);
    } else {
        console.warn('La URL del enlace no está definida.');
    }
});

/**
 * Manejar eventos de navegación del historial (botón "Atrás" del navegador)
 */
window.addEventListener('popstate', function () {
    loadContentWithAjax(location.href);
});

/**
 * Inicializa todos los componentes dinámicos presentes (no-Livewire).
 * Este código asume que los componentes Livewire ya estaban presentes o se manejan de otra forma.
 */
function initializeComponents() {
    initializeChartsIfPresent();
    initializeCalendarIfPresent();
    initializeChatbotIfPresent();
    initializeCarouselIfPresent();
}

/**
 * Inicializa los gráficos si están presentes en la página.
 */
function initializeChartsIfPresent() {
    destroyCharts();

    const solicitudesChart = document.getElementById('solicitudesChart');
    if (solicitudesChart && typeof initBarCharts === 'function') {
        initBarCharts();
    }

    const chart = document.getElementById('chart');
    if (chart && typeof initializeDonutChart === 'function') {
        initializeDonutChart();
    }
}

/**
 * Destruye los gráficos existentes para evitar duplicaciones.
 */
function destroyCharts() {
    if (window.chartDonut && typeof window.chartDonut.destroy === 'function') {
        window.chartDonut.destroy();
    }
    if (window.chartSolicitudes && typeof window.chartSolicitudes.destroy === 'function') {
        window.chartSolicitudes.destroy();
    }
}

/**
 * Inicializa el calendario si está presente en la página.
 */
function initializeCalendarIfPresent() {
    if (typeof window.initializeCalendar === 'function') {
        try {
            window.initializeCalendar();
        } catch (error) {
            console.error('Error inicializando el calendario:', error);
        }
    }
}

/**
 * Inicializa el chatbot si está presente en la página.
 */
function initializeChatbotIfPresent() {
    if (typeof window.initializeChatbot === 'function') {
        try {
            window.initializeChatbot();
        } catch (error) {
            console.error('Error inicializando el chatbot:', error);
        }
    }
}

/**
 * Inicializa el carrusel si está presente en la página.
 */
function initializeCarouselIfPresent() {
    initializeComponent('[x-data*="carousel"]', (carouselElement) => {
        try {
            const xDataValue = carouselElement.getAttribute('x-data');
            const jsonData = xDataValue.substring(xDataValue.indexOf('(') + 1, xDataValue.lastIndexOf(')'));
            const data = JSON.parse(jsonData);
            if (typeof window.carousel === 'function') {
                Alpine.data('carousel', () => window.carousel(data));
            } else {
                console.error('La función window.carousel no está definida.');
            }
        } catch (error) {
            console.error('Error inicializando el carrusel:', error);
        }
    });
}

/**
 * Inicializa un componente específico si aún no ha sido inicializado.
 * @param {string} selector - Selector CSS para el elemento del componente.
 * @param {Function} initializer - Función de inicialización para el componente.
 */
function initializeComponent(selector, initializer) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(element => {
        if (!element.__componentInitialized) {
            initializer(element);
            element.__componentInitialized = true;
        } else {
            console.warn(`Componente para ${selector} ya está inicializado.`);
        }
    });
}


