// types-chart.js

// 1) Precarga de Google Charts para BarChart
google.charts.load('current', { packages: ['corechart'] });

// 2) Flags para readiness y buffer del payload
let googleReadyTypes = false;
let typesPayload = null;

// 3) Listener nativo: atrapa el CustomEvent desde Livewire.dispatch
window.addEventListener('chart-types-data', e => {
  typesPayload = e.detail;
  drawTypesIfReady();
});

// 4) Marca readiness cuando Google Charts esté cargado
google.charts.setOnLoadCallback(() => {
  googleReadyTypes = true;
  drawTypesIfReady();
});

// 5) Función que dibuja el bar chart cuando ambos, payload y librería, están listos
function drawTypesIfReady() {
  if (!googleReadyTypes || !typesPayload) return;

  // Extrae labels y series soportando payload anidado o directo
  let payloadObj;
  if (Array.isArray(typesPayload) && typesPayload.length === 1 && typeof typesPayload[0] === 'object') {
    payloadObj = typesPayload[0];
  } else if (typesPayload.labels && typesPayload.series) {
    payloadObj = typesPayload;
  } else {
    console.error('❌ Payload inválido para chart-types-data:', typesPayload);
    typesPayload = null;
    return;
  }

  const { labels, series } = payloadObj;

  // Validación básica
  if (!Array.isArray(labels) || !Array.isArray(series) || labels.length !== series.length) {
    console.error('❌ series/labels inválidos:', payloadObj);
    typesPayload = null;
    return;
  }

  // Construye DataTable para barras horizontales
  const dataTable = new google.visualization.DataTable();
  dataTable.addColumn('string', 'Tipo');
  dataTable.addColumn('number', 'Cantidad');
  labels.forEach((label, i) => {
    const count = Number(series[i]) || 0;
    dataTable.addRow([String(label), count]);
  });

  // Opciones del gráfico de barras horizontales
  const options = {
    height: 400,
    legend: { position: 'none' },
    hAxis: { title: 'Cantidad', minValue: 0 },
    vAxis: { title: 'Tipo' }
  };

  // Dibuja en el contenedor #typesChart
  const container = document.getElementById('typesChart');
  if (!container) {
    console.error('❌ Contenedor #typesChart no encontrado');
    typesPayload = null;
    return;
  }
  new google.visualization.BarChart(container).draw(dataTable, options);

  // Limpia payload para evitar redraws repetidos
  typesPayload = null;
}
