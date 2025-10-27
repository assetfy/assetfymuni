{{-- resources/views/livewire/empresas/sidebar-empresa.blade.php --}}
@php
    /* ================= Helpers ================= */
    $hasPermittedItems = null;
    $hasPermittedItems = function (array $items, ?array $rutasPermitidas, $tipoEmpresa) use (
        &$hasPermittedItems,
    ): bool {
        foreach ($items as $item) {
            if (isset($item['ruta'])) {
                $permite = empty($item['permiso']) || in_array($item['permiso'], $rutasPermitidas ?? []);
                $empresaOk = !isset($item['company_types']) || in_array($tipoEmpresa, $item['company_types']);
                if ($permite && $empresaOk) {
                    return true;
                }
            } else {
                $nested = $item['items'] ?? ($item['subitems'] ?? []);
                if (is_array($nested) && $hasPermittedItems($nested, $rutasPermitidas, $tipoEmpresa)) {
                    return true;
                }
            }
        }
        return false;
    };

    $submenuHasActive = null;
    $submenuHasActive = function (array $items) use (&$submenuHasActive): bool {
        foreach ($items as $it) {
            if (isset($it['ruta']) && request()->routeIs($it['ruta'])) {
                return true;
            }
            $nested = $it['items'] ?? ($it['subitems'] ?? []);
            if (is_array($nested) && $submenuHasActive($nested)) {
                return true;
            }
        }
        return false;
    };

    $renderMenuLevel = null;
    $renderMenuLevel = function ($items, $rutasPermitidas, $colorSidebar, $openKeyPrefix, $tipoEmpresa) use (
        &$renderMenuLevel,
        $hasPermittedItems,
        $submenuHasActive,
    ) {
        foreach ($items as $item) {
            if (!empty($item['permiso']) && !in_array($item['permiso'], $rutasPermitidas ?? [])) {
                continue;
            }
            if (isset($item['company_types']) && !in_array($tipoEmpresa, $item['company_types'])) {
                continue;
            }

            // Label genérico
            $label = '';
            if (isset($item['label'])) {
                $label = is_array($item['label'])
                    ? $item['label'][$colorSidebar] ?? ($item['label']['default'] ?? '')
                    : $item['label'];
            } elseif (isset($item['titulo'])) {
                $label = is_array($item['titulo'])
                    ? $item['titulo'][$colorSidebar] ?? ($item['titulo']['default'] ?? '')
                    : $item['titulo'];
            }

            // Item directo
            if (isset($item['ruta'])) {
                $isActive = request()->routeIs($item['ruta']) ? 'active' : '';
                echo '<li class="' . $isActive . '">';
                echo '<a href="' .
                    route($item['ruta']) .
                    '" wire:navigate.hover wire:current="active" class="tooltip-item" data-tippy-content="' .
                    e($label) .
                    '">';
                echo '<i class="' . e($item['icon'] ?? 'fa-solid fa-circle') . '"></i>';
                echo '<span class="link_name ml-1">' . e($label) . '</span>';
                echo '</a>';
                echo '</li>';
                continue;
            }

            // Submenú
            $nested = $item['items'] ?? ($item['subitems'] ?? []);
            if (!$hasPermittedItems(is_array($nested) ? $nested : [], $rutasPermitidas, $tipoEmpresa)) {
                continue;
            }

            $key = $openKeyPrefix . '_' . (is_string($label) ? $label : '');
            $escKey = addslashes($key);
            $subAct = $submenuHasActive(is_array($nested) ? $nested : []);
            $xInit = $subAct ? ' x-init="openMenu[\''.$escKey.
                    '\'] = (openMenu[\''.$escKey.
                    '\'] ?? false) || true"' : '';
            $classBind =
                ' :class="{ \'open\': openMenu[\'' .
                $escKey .
                '\'] === true, \'active-parent\': ' .
                ($subAct ? 'true' : 'false') .
                ' }"';

            echo '<li class="has-submenu"' . $xInit . $classBind . ' data-flyout="' . e($escKey) . '">';
            echo '<a href="#" class="tooltip-item" data-tippy-content="' .
                e($label) .
                '" @click.prevent="openMenu[\'' .
                $escKey .
                '\'] = !openMenu[\'' .
                $escKey .
                '\']">';
            echo '<i class="' . e($item['icon'] ?? 'fa-solid fa-folder') . '"></i>';
            echo '<span class="link_name ml-1">' . e($label) . '</span>';
            echo '<i class="fa-solid chev" :class="openMenu[\'' .
                $escKey .
                '\'] ? \'fa-chevron-up\' : \'fa-chevron-down\'" style="margin-left:auto; font-size:.85em;"></i>';
            echo '</a>';

            // Subitems (en expandido normal con x-show; en colapsado se muestra como flyout por JS)
            echo '<ul class="sub-menu flyout" x-show="openMenu[\'' . $escKey . '\']" x-transition>';
            $renderMenuLevel(is_array($nested) ? $nested : [], $rutasPermitidas, $colorSidebar, $key, $tipoEmpresa);
            echo '</ul>';

            echo '</li>';
        }
    };

    /* ========= Unificación de menú (sin renombrar variables) ========= */
    $menuUnificado = is_array($menuUnificado ?? null) ? $menuUnificado : [];
    if (empty($menuUnificado) && isset($menuConfig) && is_array($menuConfig)) {
        $tmp = [];
        foreach ($menuConfig['directos'] ?? [] as $d) {
            $d['type'] = 'directo';
            $tmp[] = $d;
        }
        foreach ($menuConfig['submenus'] ?? [] as $s) {
            $s['type'] = 'submenu';
            $tmp[] = $s;
        }
        usort($tmp, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        $menuUnificado = $tmp;
    }

    $colorSidebar = $colorSidebar ?? 'default';
@endphp

<div x-data="{
    sidebarOpen: JSON.parse(localStorage.getItem('sb_open') || 'false'),
    openMenu: JSON.parse(localStorage.getItem('sb_openMenu') || '{}'),
    init() {
        this.$watch('sidebarOpen', v => localStorage.setItem('sb_open', JSON.stringify(!!v)));
        this.$watch('openMenu', v => localStorage.setItem('sb_openMenu', JSON.stringify(v || {})));
    }
}" x-effect="document.body.classList.toggle('sidebar-open', sidebarOpen)">
    <!-- SIDEBAR pegado a la izquierda -->
    <div class="sidebar sidebar-empresa" :class="{ 'open': sidebarOpen }">
        <!-- Logo + Toggle + Wordmark ajustable -->
        <div class="logo_details">
            @if (($tipoEmpresa ?? null) == 2)
                <img src="{{ asset('logo-prestadora.ico') }}" alt="Logo" class="sidebar-logo tooltip-item"
                    data-tippy-content="Abrir/Cerrar" @click="sidebarOpen = !sidebarOpen" />
            @else
                <img src="{{ asset('logo-gestora.ico') }}" alt="Logo" class="sidebar-logo tooltip-item"
                    data-tippy-content="Abrir/Cerrar" @click="sidebarOpen = !sidebarOpen" />
            @endif

            {{-- Wordmark adaptable al ancho del sidebar --}}
            <img src="{{ asset('asset-fy-nombre.png') }}" alt="Assetfy" class="brand-wordmark" loading="lazy" />
        </div>

        <!-- Buscador -->
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass bx-search"></i>
            <input id="sbSearch" type="text" placeholder="Buscar…" />
        </div>

        <!-- Lista -->
        <ul class="nav-list" x-ref="sidebar">
            @foreach ($menuUnificado as $menu)
                @if (($menu['type'] ?? null) === 'directo')
                    @php
                        $visible =
                            (!isset($menu['company_types']) ||
                                in_array($tipoEmpresa ?? null, $menu['company_types'])) &&
                            (empty($menu['permiso']) || in_array($menu['permiso'], $rutasPermitidas ?? []));
                    @endphp
                    @if ($visible)
                        @php
                            $isActive = request()->routeIs($menu['ruta']);
                            $label = is_array($menu['label'] ?? null)
                                ? $menu['label'][$colorSidebar] ?? ($menu['label']['default'] ?? '')
                                : $menu['label'] ?? '';
                        @endphp
                        <li class="{{ $isActive ? 'active' : '' }}">
                            <a href="{{ route($menu['ruta']) }}" wire:navigate.hover wire:current="active"
                                class="tooltip-item" data-tippy-content="{{ $label }}">
                                <i class="{{ $menu['icon'] }}"></i>
                                <span class="link_name ml-1">{{ $label }}</span>
                            </a>
                        </li>
                    @endif
                @elseif (($menu['type'] ?? null) === 'submenu')
                    @php
                        if (isset($menu['company_types']) && !in_array($tipoEmpresa ?? null, $menu['company_types'])) {
                            continue;
                        }

                        $items = $menu['items'] ?? [];
                        $itemsPermitidos = array_filter($items, function ($it) use ($rutasPermitidas, $tipoEmpresa) {
                            if (!empty($it['permiso']) && !in_array($it['permiso'], $rutasPermitidas ?? [])) {
                                return false;
                            }
                            if (isset($it['company_types']) && !in_array($tipoEmpresa ?? null, $it['company_types'])) {
                                return false;
                            }
                            return true;
                        });

                        $titulo = '';
                        if (isset($menu['titulo'])) {
                            $titulo = is_array($menu['titulo'])
                                ? $menu['titulo'][$colorSidebar] ?? ($menu['titulo']['default'] ?? '')
                                : $menu['titulo'];
                        } elseif (isset($menu['label'])) {
                            $titulo = is_array($menu['label'])
                                ? $menu['label'][$colorSidebar] ?? ($menu['label']['default'] ?? '')
                                : $menu['label'];
                        }

                        $submenuIsActive = $submenuHasActive($items);
                        $escTitulo = addslashes($titulo);

                        $xInit = $submenuIsActive ? ' x-init="openMenu[\''.$escTitulo.
                                '\'] = (openMenu[\''.$escTitulo.
                                '\'] ?? false) || true"' : '';
                        $classBind =
                            ' :class="{ \'open\': openMenu[\'' .
                            $escTitulo .
                            '\'] === true, \'active-parent\': ' .
                            ($submenuIsActive ? 'true' : 'false') .
                            ' }"';
                    @endphp

                    @if (!empty($itemsPermitidos))
                        <li class="has-submenu"{!! $xInit !!}{!! $classBind !!}
                            data-flyout="{{ $escTitulo }}">
                            <a href="#" class="tooltip-item" data-tippy-content="{{ $titulo }}"
                                @click.prevent="openMenu['{{ $escTitulo }}'] = !openMenu['{{ $escTitulo }}']">
                                <i class="{{ $menu['icon'] }}"></i>
                                <span class="link_name ml-1">{{ $titulo }}</span>
                                <i class="fa-solid chev"
                                    :class="openMenu['{{ $escTitulo }}'] ? 'fa-chevron-up' : 'fa-chevron-down'"
                                    style="margin-left:auto; font-size:.85em;"></i>
                            </a>

                            <ul class="sub-menu flyout" x-show="openMenu['{{ $escTitulo }}']" x-transition>
                                @php
                                    $renderMenuLevel(
                                        $items,
                                        $rutasPermitidas ?? [],
                                        $colorSidebar,
                                        $titulo,
                                        $tipoEmpresa ?? null,
                                    );
                                @endphp
                            </ul>
                        </li>
                    @endif
                @endif
            @endforeach
        </ul>
    </div>

    <!-- Overlay móvil -->
    <div class="overlay" x-show="sidebarOpen" @click="sidebarOpen = false"></div>
</div>
