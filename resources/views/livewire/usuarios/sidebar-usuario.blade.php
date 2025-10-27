<div>
    <div x-data="{
        currentPage: '{{ request()->path() }}',
        openMenu: @entangle('openMenu'),
        openParametrizaciones: @entangle('openParametrizaciones'),
    }">
        <div class="sidebar" :class="{ 'open': sidebarOpen }">
            <div class="logo_details">
                <img src="{{ asset('favicon.ico') }}" alt="Logo" class="sidebar-logo tooltip-item mt-2"
                    data-tippy-content="Desplegar" @click="sidebarOpen = !sidebarOpen" />
                <img src="{{ asset('asset-fy-nombre.png') }}" alt="Logo" class="ml-3" />
            </div>
            <ul class="nav-list">
                <!-- Inicio -->
                <li :class="{ 'active': currentPage === 'dashboard' }">
                    <a href="{{ route('dashboard-usuario') }}" class="ajax-link tooltip-item"
                        data-tippy-content="Inicio" @click.prevent="currentPage = 'dashboard'">
                        <i class="fa-solid fa-house"></i>
                        <span class="link_name ml-1">Inicio</span>
                    </a>
                </li>
                <!-- Activos -->
                <li wire:key="bienes-section" wire:poll.120s="maybeRefreshData">
                <li :class="{ 'open': openMenu === 'bienes' }">
                    <a href="#" class="tooltip-item" data-tippy-content="Bienes"
                        @click.prevent="openMenu = openMenu === 'bienes' ? null : 'bienes'">
                        <i class="fa-solid fa-car"></i>
                        <span class="link_name ml-1">Bienes</span>
                        <i class="fa-solid" :class="openMenu === 'bienes' ? 'fa-chevron-up' : 'fa-chevron-down'"
                            style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'bienes'" x-cloak>
                        <!-- Menú "Mis Bienes" comentado -->
                        <!--
                            <li :class="{ 'active': currentPage === 'dashboard-bienes' }">
                                <a href="{{ route('dashboard-activos') }}" class="ajax-link tooltip-item"
                                    data-tippy-content="Mis Bienes"
                                    @click.prevent="currentPage = 'dashboard-bienes'; openMenu = 'bienes';">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span class="sub_link_name ml-1">Mis Bienes</span>
                                </a>
                            </li>
                            -->
                        <li :class="">
                            <a href="#" class="ajax-link tooltip-item" data-tippy-content="Crear Bienes"
                                wire:click="crearActivos">
                                <i class="fa-solid fa-plus"></i>
                                <span class="sub_link_name ml-1">Crear Bienes</span>
                            </a>
                        </li>
                        <!-- Menú "Tablas de Bienes" -->
                        <li :class="{ 'active': currentPage === 'tablas' }">
                            <a href="{{ route('tablas') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Mis Bienes"
                                @click.prevent="currentPage = 'tablas'; openMenu = 'bienes';">
                                <i class="fa-solid fa-table-cells"></i>
                                <span class="sub_link_name ml-1">Mis Bienes</span>
                            </a>
                        </li>
                    </ul>
                </li>
                </li>

                <!-- Agenda -->
                <li :class="{ 'active': currentPage === 'agenda' }">
                    <a href="{{ route('agenda') }}" class="ajax-link tooltip-item" data-tippy-content="Agenda"
                        @click.prevent="currentPage = 'agenda'">
                        <i class="fa-solid fa-calendar-alt"></i>
                        <span class="sub_link_name ml-1">Agenda</span>
                    </a>
                </li>
                <!-- Cotizaciones -->
                <li :class="{ 'open': openMenu === 'cotizaciones' }">
                    <a href="#" class="tooltip-item" data-tippy-content="Cotizaciones"
                        @click.prevent="openMenu = openMenu === 'cotizaciones' ? null : 'cotizaciones'">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span class="link_name ml-1">Cotizaciones</span>
                        <i class="fa-solid" :class="openMenu === 'cotizaciones' ? 'fa-chevron-up' : 'fa-chevron-down'"
                            style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'cotizaciones'" x-cloak>
                        <li :class="{ 'active': currentPage === 'servicios-vista-formulario' }">
                            <a href="{{ route('servicios-vista-formulario') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Generar Cotización/Servicio"
                                @click.prevent="currentPage = 'servicios-vista-formulario'">
                                <i class="fa-solid fa-file-invoice"></i>
                                <span class="sub_link_name ml-1">Generar Cotización/Servicio</span>
                            </a>
                        </li>
                        @if (auth()->user()->tipo == 1)
                        <li :class="{ 'active': currentPage === 'usuarios-servicios' }">
                            <a href="{{ route('usuarios-servicios') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Historia de Cotizaciónes"
                                @click.prevent="currentPage = 'usuarios-servicios'">
                                <i class="fa-solid fa-history"></i>
                                <span class="sub_link_name ml-1">Historia de Cotizaciónes</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Servicios -->
                @if ($numeroActivo > 0)
                <li wire:key="servicios-section" wire:poll.120s="maybeRefreshData">
                <li :class="{ 'open': openMenu === 'servicios' }">
                    <a href="#" class="tooltip-item" data-tippy-content="Servicios"
                        @click.prevent="openMenu = openMenu === 'servicios' ? null : 'servicios'">
                        <i class="fa-solid fa-concierge-bell"></i>
                        <span class="link_name ml-1">Servicios</span>
                        <i class="fa-solid" :class="openMenu === 'servicios' ? 'fa-chevron-up' : 'fa-chevron-down'"
                            style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'servicios'" x-cloak>
                        <li :class="{ 'active': currentPage === 'calificacion-general' }">
                            <a href="{{ route('calificacion-general') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Historial de Servicios"
                                @click.prevent="currentPage = 'calificacion-general'; openMenu = 'servicios';">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="sub_link_name ml-1">Historial de Servicios</span>
                            </a>
                        </li>
                    </ul>
                </li>
                </li>
                @endif

                <!-- Proveedores -->
                <li wire:key="proveedores-section" wire:poll.120s="maybeRefreshData">
                <li :class="{ 'open': openMenu === 'proveedores' }">
                    <a href="#" class="tooltip-item" data-tippy-content="Proveedores"
                        @click.prevent="openMenu = openMenu === 'proveedores' ? null : 'proveedores'">
                        <i class="fa-solid fa-handshake"></i>
                        <span class="link_name ml-1">Proveedores</span>
                        <i class="fa-solid" :class="openMenu === 'proveedores' ? 'fa-chevron-up' : 'fa-chevron-down'"
                            style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'proveedores'" x-cloak>
                        <li :class="{ 'active': currentPage === 'descubrir-proveedores' }">
                            <a href="{{ route('proveedores') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Descubrir Proveedores"
                                @click.prevent="currentPage = 'descubrir-proveedores'; openMenu = 'proveedores';">
                                <i class="fa-solid fa-search"></i>
                                <span class="sub_link_name ml-1">Descubrir Proveedores</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'mis-proveedores-favoritos' }">
                            <a href="{{ route('mis-provedores-favoritos') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Mis Proveedores Favoritos"
                                @click.prevent="currentPage = 'mis-proveedores-favoritos'; openMenu = 'proveedores';">
                                <i class="fa-solid fa-heart"></i>
                                <span class="sub_link_name ml-1">Mis Proveedores Favoritos</span>
                            </a>
                        </li>
                    </ul>
                </li>
                </li>

                <!-- Ubicaciones -->
                <li :class="{ 'active': currentPage === 'ubicaciones' }">
                    <a href="{{ route('ubicaciones-general') }}" class="ajax-link tooltip-item"
                        data-tippy-content="Ubicaciones" @click.prevent="currentPage = 'ubicaciones'">
                        <i class="fa-solid fa-map-marked-alt"></i>
                        <span class="link_name ml-1">Ubicaciones</span>
                    </a>
                </li>

                <li :class="{ 'active': currentPage === 'reportes' }">
                    <a href="{{ route('reportes') }}" class="ajax-link tooltip-item" data-tippy-content="Reportes"
                        @click.prevent="currentPage = 'reportes'">
                        <i class="fa-solid fa-th-list"></i>
                        <span class="sub_link_name ml-1">Reportes</span>
                    </a>
                </li>

                <!-- Notificaciones -->

                <li :class="{ 'active': currentPage === 'notificaciones' }">
                    <a href="{{ route('notificaciones') }}" class="ajax-link tooltip-item"
                        data-tippy-content="Notificaciones" @click.prevent="currentPage = 'notificaciones'">
                        <i class="fa-solid fa-envelope"></i>
                        <span class="link_name ml-1">Notificaciones</span>
                    </a>
                </li>



                <!-- Parametrizaciones -->
                @if (auth()->user()->tipo == 1)
                <li :class="{ 'open': openMenu === 'parametrizaciones' || openParametrizaciones !== null }">
                    <a href="#" class="tooltip-item" data-tippy-content="Parametrizaciones"
                        @click.prevent="openMenu = openMenu === 'parametrizaciones' ? null : 'parametrizaciones'; openParametrizaciones = openMenu === 'parametrizaciones' ? openParametrizaciones : null;">
                        <i class="fa-solid fa-cogs"></i>
                        <span class="link_name ml-1">Parametrizaciones</span>
                        <i class="fa-solid"
                            :class="openMenu === 'parametrizaciones' ? 'fa-chevron-up' : 'fa-chevron-down'"
                            style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'parametrizaciones'" x-cloak>
                        <!-- Configuración de Activos -->
                        <li :class="{ 'open': openParametrizaciones === 'activos-settings' }">
                            <a href="#" class="tooltip-item" data-tippy-content="Activos"
                                @click.prevent="openParametrizaciones = openParametrizaciones === 'activos-settings' ? null : 'activos-settings'">
                                <i class="fa-solid fa-tools"></i>
                                <span class="sub_link_name">Activos</span>
                                <i class="fa-solid"
                                    :class="openParametrizaciones === 'activos-settings' ? 'fa-chevron-up' :
                                            'fa-chevron-down'"
                                    style="margin-left:auto; font-size: 0.8em;"></i>
                            </a>
                            <ul class="sub-menu" x-show="openParametrizaciones === 'activos-settings'" x-cloak>
                                <li :class="{ 'active': currentPage === 'actividad-empresas' }">
                                    <a href="{{ route('actividad-empresas') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Activos Empresa"
                                        @click.prevent="currentPage = 'actividad-empresas'">
                                        <i class="fa-solid fa-briefcase"></i>
                                        <span class="sub_link_name ml-1">Actividad Empresa</span>
                                    </a>
                                </li>
                                <li :class="{ 'active': currentPage === 'activos-controles' }">
                                    <a href="{{ route('activos-controles') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Activos Controles"
                                        @click.prevent="currentPage = 'activos-controles'">
                                        <i class="fa-solid fa-sliders-h"></i>
                                        <span class="sub_link_name ml-1">Activos Controles</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Permisos configuraciones -->
                        <li :class="{ 'open': openParametrizaciones === 'Permisos-Roles' }">
                            <a href="#" class="tooltip-item" data-tippy-content="Permisos y Rutas"
                                @click.prevent="openParametrizaciones = openParametrizaciones === 'Permisos-Roles' ? null : 'Permisos-Roles'">
                                <i class="fa-solid fa-user-lock"></i>
                                <span class="sub_link_name ml-1">Permisos y Rutas</span>
                                <i class="fa-solid"
                                    :class="openParametrizaciones === 'Permisos-Roles' ? 'fa-chevron-up' :
                                            'fa-chevron-down'"
                                    style="margin-left:auto; font-size: 0.8em;"></i>
                            </a>
                            <ul class="sub-menu" x-show="openParametrizaciones === 'Permisos-Roles'" x-transition>
                                <li :class="{ 'active': currentPage === 'Rutas' }">
                                    <a href="{{ route('rutas') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Rutas" @click.prevent="currentPage = 'Rutas'">
                                        <i class="fa-solid fa-map-signs"></i>
                                        <span class="sub_link_name ml-1">Rutas</span>
                                    </a>
                                </li>
                                <li :class="{ 'active': currentPage === 'Permisos' }">
                                    <a href="{{ route('permisos') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Permisos" @click.prevent="currentPage = 'Permisos'">
                                        <i class="fa-solid fa-lock-open"></i>
                                        <span class="sub_link_name ml-1">Permisos</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Configuración de Tipo -->
                        <li :class="{ 'active': currentPage === 'tipos' }">
                            <a href="{{ route('tipos') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Tipos" @click.prevent="currentPage = 'tipos'">
                                <i class="fa-solid fa-list"></i>
                                <span class="sub_link_name ml-1">Tipos</span>
                            </a>
                        </li>
                        <!-- Configuración de Categorías -->
                        <li :class="{ 'active': currentPage === 'categoria' }">
                            <a href="{{ route('categoria') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Categorias" @click.prevent="currentPage = 'categoria'">
                                <i class="fa-solid fa-folder-open"></i>
                                <span class="sub_link_name ml-1">Categorias</span>
                            </a>
                        </li>
                        <!-- Configuración de Subcategoria -->
                        <li :class="{ 'active': currentPage === 'subcategoria' }">
                            <a href="{{ route('subcategoria') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Subcategorias" @click.prevent="currentPage = 'subcategoria'">
                                <i class="fa-solid fa-sitemap"></i>
                                <span class="sub_link_name ml-1">Subcategorias</span>
                            </a>
                        </li>
                        <!-- Atributos -->
                        <li :class="{ 'open': openParametrizaciones === 'atributos-settings' }">
                            <a href="#" class="tooltip-item" data-tippy-content="Atributos"
                                @click.prevent="openParametrizaciones = openParametrizaciones === 'atributos-settings' ? null : 'atributos-settings'">
                                <i class="fa-solid fa-layer-group"></i>
                                <span class="sub_link_name ml-1">Atributos</span>
                                <i class="fa-solid"
                                    :class="openParametrizaciones === 'atributos-settings' ? 'fa-chevron-up' :
                                            'fa-chevron-down'"
                                    style="margin-left:auto; font-size: 0.8em;"></i>
                            </a>
                            <ul class="sub-menu" x-show="openParametrizaciones === 'atributos-settings'" x-cloak>
                                <li :class="{ 'active': currentPage === 'atributos' }">
                                    <a href="{{ route('atributos') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Atributos" @click.prevent="currentPage = 'atributos'">
                                        <i class="fa-solid fa-tags"></i>
                                        <span class="sub_link_name ml-1">Atributos</span>
                                    </a>
                                </li>
                                <li :class="{ 'active': currentPage === 'atributos-subcategoria' }">
                                    <a href="{{ route('atributos-subcategoria') }}"
                                        class="ajax-link tooltip-item"
                                        data-tippy-content="Atributos Subcategorias"
                                        @click.prevent="currentPage = 'atributos-subcategoria'">
                                        <i class="fa-solid fa-tag"></i>
                                        <span class="sub_link_name ml-1">Atributos Subcategorias</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- Controles -->
                        <li :class="{ 'open': openParametrizaciones === 'control-settings' }">
                            <a href="#" class="tooltip-item" data-tippy-content="Controles"
                                @click.prevent="openParametrizaciones = openParametrizaciones === 'control-settings' ? null : 'control-settings'">
                                <i class="fa-solid fa-check-circle"></i>
                                <span class="sub_link_name ml-1">Controles</span>
                                <i class="fa-solid"
                                    :class="openParametrizaciones === 'control-settings' ? 'fa-chevron-up' :
                                            'fa-chevron-down'"
                                    style="margin-left:auto; font-size: 0.8em;"></i>
                            </a>
                            <ul class="sub-menu" x-show="openParametrizaciones === 'control-settings'" x-cloak>
                                <li :class="{ 'active': currentPage === 'control-subcategoria' }">
                                    <a href="{{ route('control-subcategoria') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Controles Subcategorias"
                                        @click.prevent="currentPage = 'control-subcategoria'">
                                        <i class="fa-solid fa-shield-alt"></i>
                                        <span class="sub_link_name ml-1">Controles Subcategorias</span>
                                    </a>
                                </li>
                                <li :class="{ 'active': currentPage === 'controles' }">
                                    <a href="{{ route('controles') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Controles" @click.prevent="currentPage = 'controles'">
                                        <i class="fa-solid fa-check-double"></i>
                                        <span class="sub_link_name ml-1">Controles</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li :class="{ 'active': currentPage === 'estado-actividad' }">
                            <a href="{{ route('estado-actividad') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Estado de Actividad"
                                @click.prevent="currentPage = 'estado-actividad'">
                                <i class="fa-solid fa-chart-line"></i>
                                <span class="sub_link_name ml-1">Estado de Actividad</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'estado-gral' }">
                            <a href="{{ route('estado-gral') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Estado General" @click.prevent="currentPage = 'estado-gral'">
                                <i class="fa-solid fa-chart-bar"></i>
                                <span class="sub_link_name ml-1">Estado General</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'estado-alta' }">
                            <a href="{{ route('estados-altas') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Estado de alta" @click.prevent="currentPage = 'estado-alta'">
                                <i class="fa-solid fa-clipboard"></i>
                                <span class="sub_link_name ml-1">Estado de Alta</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'servicios' }">
                            <a href="{{ route('servicios') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Servicios" @click.prevent="currentPage = 'servicios'">
                                <i class="fa-solid fa-hands-helping"></i>
                                <span class="sub_link_name ml-1">Servicios</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'servicios-actividades-economicas' }">
                            <a href="{{ route('servicios-actividades-economicas') }}"
                                class="ajax-link tooltip-item"
                                data-tippy-content="Servicios Actividades Economicas"
                                @click.prevent="currentPage = 'servicios-actividades-economicas'">
                                <i class="fa-solid fa-business-time"></i>
                                <span class="sub_link_name ml-1">Servicios Actividades Economicas</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'actividad-empresas' }">
                            <a href="{{ route('actividad-empresas') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Actividad Empresa"
                                @click.prevent="currentPage = 'actividad-empresas'">
                                <i class="fa-solid fa-building"></i>
                                <span class="link_name ml-1">Actividad Empresa</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'servicios-subcategorias' }">
                            <a href="{{ route('servicios-subcategorias') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Servicios Subcategorias"
                                @click.prevent="currentPage = 'servicios-subcategorias'">
                                <i class="fa-solid fa-th-list"></i>
                                <span class="sub_link_name ml-1">Servicios Subcategorias</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'unidad_medida' }">
                            <a href="{{ route('unidad_medida') }}" class="ajax-link tooltip-item"
                                data-tippy-content="Unidad de medida"
                                @click.prevent="currentPage = 'unidad_medida'">
                                <i class="fa-solid fa-ruler"></i>
                                <span class="sub_link_name ml-1">Unidad de medida</span>
                            </a>
                        </li>
                        <!-- Configuración de Usuarios -->
                        <li :class="{ 'open': openParametrizaciones === 'usuarios-settings' }">
                            <a href="#" class="tooltip-item" data-tippy-content="Usuarios"
                                @click.prevent="openParametrizaciones = openParametrizaciones === 'usuarios-settings' ? null : 'usuarios-settings'">
                                <i class="fa-solid fa-users-cog"></i>
                                <span class="sub_link_name">Usuarios</span>
                                <i class="fa-solid"
                                    :class="openParametrizaciones === 'usuarios-settings' ? 'fa-chevron-up' :
                                            'fa-chevron-down'"
                                    style="margin-left:auto; font-size: 0.8em;"></i>
                            </a>
                            <ul class="sub-menu" x-show="openParametrizaciones === 'usuarios-settings'" x-cloak>
                                <li :class="{ 'active': currentPage === 'empresas-vista' }">
                                    <a href="{{ route('empresas-vista') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Solicitudes de Alta"
                                        @click.prevent="currentPage = 'empresas-vista'">
                                        <i class="fa-solid fa-file-signature"></i>
                                        <span class="sub_link_name ml-1">Solicitudes de Alta</span>
                                    </a>
                                </li>
                                <li :class="{ 'active': currentPage === 'tipos-usuarios' }">
                                    <a href="{{ route('tipos-usuarios') }}" class="ajax-link tooltip-item"
                                        data-tippy-content="Tipos de Usuarios"
                                        @click.prevent="currentPage = 'tipos-usuarios'">
                                        <i class="fa-solid fa-id-card"></i>
                                        <span class="sub_link_name ml-1">Tipos de Usuarios</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>

        </div>
        <div class="overlay" x-show="sidebarOpen" @click="sidebarOpen = false"></div>
    </div>
</div>