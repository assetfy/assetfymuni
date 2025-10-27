/* Sidebar: búsqueda con filtrado recursivo + flyouts + tooltips opcionales */
(function () {
  'use strict';

  /* ===== TOOLTIPS (si existe Tippy) ===== */
  function initTooltips() {
    var nodes = document.querySelectorAll('.tooltip-item');
    if (!nodes.length || !window.tippy) return;
    for (var i = 0; i < nodes.length; i++) {
      if (nodes[i]._tippy) nodes[i]._tippy.destroy();
    }
    window.tippy(nodes, { placement: 'right', theme: 'ghost', delay: [300, 0], arrow: true });
  }

  /* ===== UTILIDADES ===== */
  function textOf(el) { return el && el.textContent ? el.textContent.toLowerCase().trim() : ''; }

  function getLiLabel(li) {
    // Busca label del li en este orden
    var el = li.querySelector(':scope > a .link_name, :scope > a .sub_link_name, :scope > a');
    return textOf(el);
  }

  /* ===== BÚSQUEDA con filtrado recursivo de submenús ===== */
  function initSidebarSearch() {
    var rail = document.querySelector('.sidebar');
    var list = rail ? rail.querySelector('.nav-list') : null;
    var input = document.getElementById('sbSearch');
    if (!rail || !list || !input) return;

    // Filtra recursivamente un <li>; devuelve si queda visible
    function filterLi(li, term) {
      var hasTerm = term.length > 0;
      var label = getLiLabel(li);
      var selfMatch = !hasTerm || label.indexOf(term) !== -1;

      var sub = li.querySelector(':scope > .sub-menu');
      if (sub) {
        var children = sub.querySelectorAll(':scope > li');
        var anyChildVisible = false;

        for (var i = 0; i < children.length; i++) {
          var vis = filterLi(children[i], term);
          anyChildVisible = anyChildVisible || vis;
        }

        var visible = hasTerm ? (selfMatch || anyChildVisible) : true;
        li.style.display = visible ? '' : 'none';

        // Durante la búsqueda, forzamos que el submenú quede abierto
        if (hasTerm) {
          sub.style.display = 'block';
        } else {
          sub.style.display = ''; // Alpine vuelve a manejarlo
        }
        return visible;
      } else {
        // Ítem hoja
        var match = selfMatch;
        li.style.display = match ? '' : 'none';
        return match;
      }
    }

    // Manejo de input
    function runFilter() {
      var term = (input.value || '').toLowerCase().trim();
      rail.classList.toggle('searching', term.length > 0);

      var items = list.querySelectorAll(':scope > li');
      for (var i = 0; i < items.length; i++) {
        filterLi(items[i], term);
      }
    }

    input.addEventListener('input', runFilter);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { input.value = ''; runFilter(); }
    });

    // Lupa centrada: si el rail está cerrado, lo abre y enfoca el input
    var magnifier = rail.querySelector('.search-box .bx-search');
    if (magnifier) {
      magnifier.addEventListener('click', function () {
        if (!rail.classList.contains('open')) {
          // Usamos el mismo trigger que tu Blade (logo con @click)
          var toggler = rail.querySelector('.logo_details .sidebar-logo');
          if (toggler) toggler.click();
        }
        setTimeout(function () { input.focus(); input.select(); }, 60);
      });
    }
  }

  /* ===== FLYOUTS en colapsado (cascada) ===== */
  function initSidebarFlyouts() {
    var rail = document.querySelector('.sidebar');
    if (!rail) return;
    var GAP = 8;

    function railOpen() { return rail.classList.contains('open'); }
    function clamp(v, min, max) { return v < min ? min : (v > max ? max : v); }
    function measureHeight(panel) {
      var d = panel.style.display, v = panel.style.visibility;
      panel.style.visibility = 'hidden'; panel.style.display = 'block';
      var h = panel.offsetHeight || 260;
      panel.style.display = d || ''; panel.style.visibility = v || '';
      return h;
    }

    var parents = document.querySelectorAll('.sidebar li.has-submenu');
    for (var i = 0; i < parents.length; i++) {
      var li = parents[i];
      if (li.getAttribute('data-flyout-init') === '1') continue;
      li.setAttribute('data-flyout-init', '1');

      (function (li) {
        var panel = li.querySelector(':scope > .sub-menu.flyout');
        if (!panel) return;

        var hideTimer = null;

        function baseRect() {
          var parentPanel = li.closest('.sub-menu.flyout');
          return parentPanel ? parentPanel.getBoundingClientRect()
                             : rail.getBoundingClientRect();
        }

        function positionPanel() {
          var ref = baseRect();
          var liRect = li.getBoundingClientRect();
          var ph = measureHeight(panel);
          var left = ref.right + GAP;
          var top  = clamp(liRect.top, 8, window.innerHeight - ph - 8);
          panel.style.left = left + 'px';
          panel.style.top  = top  + 'px';
        }

        function anyDescendantHovered(container) {
          var list = container.querySelectorAll('.sub-menu.flyout');
          for (var j = 0; j < list.length; j++) { if (list[j].matches(':hover')) return true; }
          return false;
        }

        function show() {
          if (railOpen()) return;
          positionPanel();
          panel.classList.add('show');
          panel.style.display = 'block';
        }

        function scheduleHide() {
          if (railOpen()) return;
          if (hideTimer) clearTimeout(hideTimer);
          hideTimer = setTimeout(function () {
            if (li.matches(':hover') || panel.matches(':hover') || anyDescendantHovered(li)) return;
            panel.classList.remove('show');
            panel.style.display = '';
          }, 220);
        }

        function cancelHide() { if (hideTimer) clearTimeout(hideTimer); }

        li.addEventListener('mouseenter', show);
        li.addEventListener('mouseleave', scheduleHide);
        panel.addEventListener('mouseenter', cancelHide);
        panel.addEventListener('mouseleave', scheduleHide);

        window.addEventListener('scroll', function () {
          if (!railOpen()) { panel.classList.remove('show'); panel.style.display = ''; }
        }, { passive: true });
        window.addEventListener('resize', function () {
          if (!railOpen()) { panel.classList.remove('show'); panel.style.display = ''; }
        });
      })(li);
    }
  }

  function initAll() {
    initTooltips();
    initSidebarSearch();
    initSidebarFlyouts();
  }

  document.addEventListener('DOMContentLoaded', initAll);
  document.addEventListener('livewire:navigated', initAll); // Livewire 3 SPA
})();
