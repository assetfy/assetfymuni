// resources/js/organigrama.js  (sin imports; usa window.mermaid por CDN)
(() => {
  let inited = false;

  function waitForMermaid() {
    return new Promise((resolve) => {
      if (window.mermaid) return resolve();
      const id = setInterval(() => {
        if (window.mermaid) {
          clearInterval(id);
          resolve();
        }
      }, 30);
    });
  }

  async function ensureMermaid() {
    await waitForMermaid();
    if (!inited) {
      window.mermaid.initialize({
        startOnLoad: false,
        securityLevel: 'loose',
        theme: 'default',
        flowchart: { curve: 'basis', htmlLabels: true },
      });
      inited = true;
    }
  }

  const nodeId = (v) => 'N' + String(v).replace(/[^a-zA-Z0-9_]/g, '_');

  function buildMermaid(raw) {
    const arr = Array.isArray(raw) ? raw : [];
    const declared = new Set();
    const lines = ['graph TD'];

    for (const n of arr) {
      const id = nodeId(n.id);
      if (!declared.has(id)) {
        const label = String(n.nombre ?? '').replace(/"/g, '\\"');
        lines.push(`${id}["${label}"]`);
        declared.add(id);
      }
    }
    for (const n of arr) {
      if (n.padre != null && n.padre !== '') {
        lines.push(`${nodeId(n.padre)} --> ${nodeId(n.id)}`);
      }
    }
    return lines.join('\n'); // ¡importante!: saltos de línea
  }

  function normalizePayload(payload) {
    let p = payload;
    if (Array.isArray(p) && p.length === 1 && Array.isArray(p[0])) p = p[0];
    return (p || []).map((n) => ({
      id: String(n.id),
      padre: n.padre != null ? String(n.padre) : '',
      nombre: n.nombre ?? '',
    }));
  }

  function drawWhenElReady(payload) {
    // si el div aún no existe, esperamos a que aparezca y dibujamos UNA vez
    const elNow = document.getElementById('orgMermaid1');
    if (elNow) {
      return draw(elNow, payload);
    }
    const mo = new MutationObserver(() => {
      const el = document.getElementById('orgMermaid1');
      if (el) {
        mo.disconnect();
        draw(el, payload);
      }
    });
    mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
  }

  async function draw(el, payload) {
    await ensureMermaid();
    const data = normalizePayload(payload);
    const dsl = buildMermaid(data);
    try {
      el.innerHTML = '';
      if (!data.length) {
        el.innerHTML = '<div style="text-align:center;opacity:.6">Sin datos para el organigrama</div>';
        return;
      }
      const { svg } = await window.mermaid.render('org_' + Date.now(), dsl);
      el.innerHTML = svg;
    } catch (err) {
      console.error('Mermaid render error:', err);
      el.innerHTML = `<pre style="font-family:monospace;white-space:pre-wrap">${dsl.replace(/</g,'&lt;')}</pre>`;
    }
  }

  // 🔔 ÚNICO disparador: evento de Livewire/Browser
  window.addEventListener('mostrarRender1', (e) => {
    drawWhenElReady(e.detail);
  });
})();
