// public/js/orgchart.js

const configs = [
  {
    evt:       'mostrarOrganigrama1',
    container: 'orgchart_google_1',
    dataVar:   'organigrama1',
    depth:     4,
    root:      null,
    selected:  null
  },
    {
    evt:       'mostrarOrganigrama2',
    container: 'orgchart_google_2',
    dataVar:   'organigrama2',
    depth:     4,
    root:      null,
    selected:  null
  },
];

const state = {};

// 1) Cuando Livewire envía datos:
configs.forEach(c => {
  Livewire.on(c.evt, raw => {
    // A veces llega [[…]] por Livewire 3
    if (Array.isArray(raw) && raw.length === 1 && Array.isArray(raw[0])) {
      raw = raw[0];
    }
    if (!Array.isArray(raw)) {
      console.error(`${c.evt}: payload no es array`);
      return;
    }
    // Normalizar
    state[c.dataVar] = raw.map(n => ({
      id:     String(n.id),
      padre:  n.padre != null ? String(n.padre) : '',
      nombre: n.nombre
    }));
    drawChart(c);
  });
});

// 2) Cargar Google Charts y redibujar tras cada mensaje de Livewire:
document.addEventListener('DOMContentLoaded', () => {
  google.charts.load('current', { packages: ['orgchart'] });
  google.charts.setOnLoadCallback(() =>
    configs.forEach(c => drawChart(c))
  );
  Livewire.hook('message.processed', () =>
    configs.forEach(c => drawChart(c))
  );
});

/**
 * Devuelve un array de nodos hijos hasta `depth`,
 * comenzando en los hijos del `root` (no incluye el mismo).
 */
function extractSubtree(raw, depth, root) {
  const map = {};
  raw.forEach(n => map[n.id] = { ...n, children: [] });
  raw.forEach(n => {
    if (n.padre && map[n.padre]) {
      map[n.padre].children.push(n.id);
    }
  });

  const out = [];
  function dfs(id, lvl) {
    if (lvl > depth) return;
    out.push(map[id]);
    map[id].children.forEach(ch => dfs(ch, lvl + 1));
  }

  if (root) {
    (map[root]?.children || []).forEach(ch => dfs(ch, 1));
  } else {
    raw.forEach(n => {
      if (!n.padre) dfs(n.id, 0);
    });
  }

  return out;
}

/**
 * Dibuja (o redibuja) el organigrama para la configuración `c`.
 */
function drawChart(c) {
  const raw = state[c.dataVar] || [];
  const cont = document.getElementById(c.container);
  if (!cont) return;
  cont.innerHTML = '';

  const dataTable = new google.visualization.DataTable();
  dataTable.addColumn('string','Name');
  dataTable.addColumn('string','Manager');
  dataTable.addColumn('string','ToolTip');

  // 1) Si estamos anidados en c.root, ponemos el botón <Volver> y luego el nombre del padre:
  if (c.root) {
    // Volver
    dataTable.addRow([
      { v: '__back__', f: `<div style="padding:6px;cursor:pointer;font-style:italic">🔙 Volver</div>` },
      '', ''
    ]);

    // Nombre del padre
    const padreNode   = raw.find(n => n.id === c.root);
    const padreNombre = padreNode ? padreNode.nombre : '(desconocido)';
    dataTable.addRow([
      { v: c.root, f: `<div style="padding:6px;cursor:default;font-weight:bold">${padreNombre}</div>` },
      '', padreNombre
    ]);
  }

  // 2) Extraer solo la sub-rama hasta `depth`, sin incluir `root` en los hijos:
  const branch = extractSubtree(raw, c.depth, c.root)
                   .filter(n => n.id !== c.root);

  // 3) Añadir cada nodo al chart y colorear el seleccionado:
  branch.forEach(n => {
    const isSel = n.id === c.selected;
    const bg    = isSel ? 'background:#DDEEFE;' : '';
    dataTable.addRow([
      { v: n.id, f: `<div style="padding:6px;cursor:pointer;${bg}">${n.nombre}</div>` },
      n.padre, n.nombre
    ]);
  });

  // 4) Dibujar
  const chart = new google.visualization.OrgChart(cont);
  chart.draw(dataTable, { allowHtml: true });

  // 5) Capturar clicks
  google.visualization.events.addListener(chart, 'select', () => {
    const sel = chart.getSelection()[0];
    if (!sel) return;
    const clicked = dataTable.getValue(sel.row, 0);

    if (clicked === '__back__') {
      // Subimos un nivel
      const parentOfRoot = raw.find(x => x.id === c.root)?.padre || '';
      c.root     = parentOfRoot || null;
      c.selected = null;
    } else {
      // Clic normal: si tiene hijos → drill-down; si no, solo seleccionar
      const hasKids = raw.some(x => x.padre === clicked);
      if (hasKids) {
        c.root     = clicked;
        c.selected = clicked;
      } else {
        c.selected = clicked;
      }
    }

    // 6) Redibujar para actualizar colores y botones
    drawChart(c);

    // 7) Notificar al componente Livewire (solo nodos reales)
    if (clicked !== '__back__') {
      const idInt = parseInt(clicked, 10);
      if (!isNaN(idInt)) {
        Livewire.dispatch('setPadre', [ idInt ]);

      }
    }
  });
}
