// states-chart.js

// 1) Precarga de Google Charts para PieChart (dona)
google.charts.load('current', { packages: ['corechart'] });

// 2) Flags para readiness y buffer de payload
let googleReady = false;
let donutPayload = null;

// 3) Listener nativo: atrapa el CustomEvent desde Livewire.dispatch
window.addEventListener('chart-states-data', e => {
  donutPayload = e.detail;
  drawDonutIfReady();
});

// 4) Marca readiness cuando Google Charts esté cargado
google.charts.setOnLoadCallback(() => {
  googleReady = true;
  drawDonutIfReady();
});

// 5) Función que dibuja la dona cuando payload y librería están listos
function drawDonutIfReady() {
  if (!googleReady || !donutPayload) return;

  // Extrae series y labels soportando payload anidado o directo
  let { series, labels } = Array.isArray(donutPayload) &&
    donutPayload.length === 1 &&
    typeof donutPayload[0] === 'object'
    ? donutPayload[0]
    : donutPayload;

  // Validación básica
  if (!Array.isArray(series) || !Array.isArray(labels) || series.length !== labels.length) {
    return console.error('❌ series/labels inválidos:', { series, labels });
  }

  // Construye DataTable para dona
  const dataTable = new google.visualization.DataTable();
  dataTable.addColumn('string', 'Estado');
  dataTable.addColumn('number', 'Cantidad');
  labels.forEach((label, idx) => {
    const val = Number(series[idx]) || 0;
    dataTable.addRow([String(label), val]);
  });

  // Opciones de gráfico de dona
const options = {
  pieHole: 0.4,
  legend: { position: 'right', alignment: 'center' },
  chartArea: {
    left: '10%',
    top: '10%',
    width: '80%',
    height: '80%'
  },
  width: '100%',
  height: '100%', // ¡clave! para ocupar todo el contenedor
};
  // Dibuja en el contenedor
  const container = document.getElementById('statesChart');
  if (!container) {
    return console.error('❌ #statesChart no encontrado');
  }

  new google.visualization.PieChart(container).draw(dataTable, options);
 
  // Limpia payload para evitar redraws
  donutPayload = null;
}