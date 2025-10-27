// tailwind.config.js
module.exports = {
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
      // Si tienes otros directorios o tipos de archivos donde usas clases de Tailwind, añádelos aquí
    ],
    safelist: [
      // Clases personalizadas utilizadas en tu proyecto
      'sidebar',
      'active',
      'open',
      'tooltip-item',
      'sub-menu',
      'sub_link_name',
      'link_name',
      'logo_details',
      'logo_name',
      'carousel-item',
      'ml-1',
      'ml-auto',
      // Añade cualquier otra clase específica que utilices
    ],
    theme: {
      extend: {
        colors: {
          primary: '#1DA1F2',
          secondary: '#14171A',
          // Otros colores personalizados
        },
        spacing: {
          '128': '32rem',
          '144': '36rem',
          // Otros tamaños personalizados
        },
        // Extensiones adicionales
      },
    },
    plugins: [
      // Añade cualquier plugin que estés usando, por ejemplo:
      // require('@tailwindcss/forms'),
    ],
  };
  