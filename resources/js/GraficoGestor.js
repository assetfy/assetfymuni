+// summary-chart.js

// 1) Precarga Google Charts para ColumnChart
google.charts.load('current', { packages: ['corechart'] });

let googleReady = false;
let gestoresCounts = null;
let gestoresLabels = null;

// 2) Captura el evento de Livewire; guarda labels y series
window.addEventListener('chart-gestores-data', e => {
  const payload = (Array.isArray(e.detail) && e.detail.length === 1) ? e.detail[0] : e.detail;
  gestoresLabels = Array.isArray(payload.labels) ? payload.labels : [];
  gestoresCounts = Array.isArray(payload.series) ? payload.series.map(n => Number(n) || 0) : null;
  drawSummary();
});

// 3) Marca readiness cuando Google Charts esté cargado
google.charts.setOnLoadCallback(() => {
  googleReady = true;
  drawSummary();
});

// 4) Dibuja bar chart con cada usuario y total
function drawSummary() {
  if (!googleReady || !gestoresCounts || !gestoresLabels) return;
  const labels = gestoresLabels;
  const counts = gestoresCounts;
  if (labels.length !== counts.length) {
    console.error('❌ Labels y series de distinto largo', labels, counts);
    return;
  }

  // Calcula total
  const total = counts.reduce((sum, v) => sum + v, 0);

  // 5) Arma DataTable con todos los usuarios y luego TOTAL
  const data = new google.visualization.DataTable();
  data.addColumn('string', 'Gestor');
  data.addColumn('number', 'Cantidad');
  data.addColumn({ type: 'string', role: 'style' });

  labels.forEach((label, i) => {
    data.addRow([label, counts[i], 'color:#4f81bd']);
  });
  data.addRow(['TOTAL', total, 'color:#17365d']);

  // 6) Opciones del chart
  const options = {
    title: 'Asignaciones por Gestor y TOTAL',
    legend: 'none',
    tooltip: { trigger: 'focus' },
    hAxis: { title: 'Gestor' },
    vAxis: { title: 'Cantidad', minValue: 0 },
    height: 400,
    chartArea: { left: 60, top: 50, width: '80%', height: '75%' }
  };

  // 7) Dibuja en contenedor #summaryChart
  const container = document.getElementById('summaryChart');
  if (!container) {
    console.error('❌ Contenedor #summaryChart no existe');
    return;
  }
  new google.visualization.ColumnChart(container).draw(data, options);
}
