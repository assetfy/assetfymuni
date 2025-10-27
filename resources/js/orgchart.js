// locations-chart.js

// 1) Precarga Google Charts
google.charts.load('current', { packages: ['corechart'] });

// 2) Espera a que Livewire arranque
document.addEventListener('livewire:init', () => {
  // 3) Registra listener para el evento con payload posiblemente anidado
  Livewire.on('locations-chart-data', payload => {
    // 3a) Desempaqueta el array si viene anidado
    let raw;
    if (Array.isArray(payload) && payload.length === 1 && Array.isArray(payload[0])) {
      raw = payload[0];
    } else if (Array.isArray(payload)) {
      raw = payload;
      console.log('✅ raw es payload directamente:', raw);
    } else if (payload.data && Array.isArray(payload.data)) {
      raw = payload.data;
    } else {
      console.error('❌ No se pudo extraer raw de payload:', payload);
      return;
    }
    if (raw.length === 0) {
      console.warn('⚠️ No hay datos para graficar');
      return;
    }
    // 4) Dibuja cuando Google Charts esté listo
    google.charts.setOnLoadCallback(() => {
      // 4a) Construye DataTable
      const dataTable = new google.visualization.DataTable();
      dataTable.addColumn('string', 'Ubicación');
      dataTable.addColumn('number', 'Activos');
      raw.forEach(item => {
        // Convierte a número seguro
        const count = Number(item.activos);
        dataTable.addRow([ String(item.ubicacion), isNaN(count) ? 0 : count ]);
      });

      // 4b) Configuración
      const options = {
        height: 300,
        legend: 'none',
        hAxis: { title: 'Ubicación' },
        vAxis: { title: 'Activos', minValue: 0 }
      };

      // 4c) Dibuja o redibuja
      const container = document.getElementById('locationsChart');
      if (!container) {
        console.error('❌ Contenedor #locationsChart no encontrado');
        return;
      }
      const chart = new google.visualization.ColumnChart(container);
      chart.draw(dataTable, options);
    });
  });
});
