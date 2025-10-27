// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // CSS
        'resources/css/app.css',   
        'resources/css/welcome.css',
        // JS
        'resources/js/app.js',
        'resources/js/calendar.js',
        'resources/js/sidebar.js',
        'resources/js/swal.js',
        'resources/js/map-iniModal.js',
        'resources/js/carousel.js',
        'resources/js/swapy.js',
        'resources/js/chatbot.js',
        'resources/js/apexcharts.js',
        'resources/js/mapAndDonut.js',
        'resources/js/google.maps.js',
        'resources/js/customMap.js',
        'resources/js/orgchart.js', 
        'resources/js/orgchart-init.js',
        'resources/js/GraficoDonaReporte.js',
        'resources/js/GraficoDeTipos.js',
        'resources/js/GraficoGestor.js',
        'resources/js/jstree-setup.js',
        'resources/js/Js-treeEdit.js',
        'resources/js/soporte.js',
        'resources/js/organigrama.js'
      ],
      refresh: [
        'app/Livewire/**',
        'resources/views/**',
      ],
    }),
  ],
  
});