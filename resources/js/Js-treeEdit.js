// resources/js/jstree-setup.js

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'jstree/dist/jstree.min.js';
import 'jstree/dist/themes/default/style.css';

const CONTAINER = '#jstree_container-edit';
const SEARCH    = '#jstree_search';

/**
 * Inicializa o re-inicializa jsTree en el contenedor dado.
 * @param {Array} treeData Array de nodos [{ id, parent, text }, ...]
 */
function initTree(treeData) {
  const $ctr = $(CONTAINER);
  if (!$ctr.length || !$ctr.is(':visible')) {
    console.error(`jsTree container no encontrado o no visible: ${CONTAINER}`);
    return;
  }
  // Inicializa jsTree
  $ctr.jstree({
    core: {
      data: treeData,
      multiple: false,
      check_callback: true,
      themes: { stripes: true },
      animation: 0
    },
    plugins: ['search', 'wholerow']
  });

  // Selección → Livewire
  $ctr
    .off('select_node.jstree')
    .on('select_node.jstree', (e, data) => {
      const idInt = parseInt(data.node.id, 10);
      if (!isNaN(idInt)) {
        Livewire.dispatch('setPadre', [ idInt ]);
      }
    });

  // Búsqueda
  const $search = $(SEARCH);
  if ($search.length) {
    $search
      .off('keyup.jstree')
      .on('keyup.jstree', () => {
        $ctr.jstree(true).search($search.val());
      });
  }
}

// Livewire init
document.addEventListener('livewire:init', () => {
  window.addEventListener('init-jstree-edit', event => {
    let raw = event.detail;

    // dispatch('init-jstree', [ { data: [...] } ])
    if (Array.isArray(raw) && raw.length) {
      const first = raw[0];
      if (first && first.data !== undefined) raw = first.data;
    }
    // dispatch('init-jstree', { data: [...] })
    else if (raw && raw.data !== undefined) {
      raw = raw.data;
    }

    if (!Array.isArray(raw)) {
      console.error('init-jstree payload no es array', raw);
      return;
    }
    const treeData = raw.map(node => ({
      id:     String(node.id),
      parent: node.padre != null ? String(node.padre) : '#',
      text:   node.nombre
    }));
    // Espera a que el contenedor exista/sea visible
    (function waitForContainer() {
      const $ctr = $(CONTAINER);
      if ($ctr.length && $ctr.is(':visible')) {
        initTree(treeData);
      } else {
        setTimeout(waitForContainer, 100);
      }
    })();
  });
});
