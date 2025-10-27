<div>
    <div x-data="{ 
        currentPage: '{{ request()->path() }}', 
        openMenu: null, 
        openParametrizaciones: null, 
        navigate(url, page) { 
            history.pushState(null, '', url); 
            Livewire.dispatch('navigateTo', { page }); 
            this.currentPage = page; 
        },
    }">
        <div class="sidebar" :class="{ 'open': sidebarOpen }">
            <div class="logo_details">
                <i class="bx bxl-audible icon"></i>
                <div class="logo_name">Activos</div>
                <i class="fa-solid fa-bars" id="btn" @click="sidebarOpen = !sidebarOpen"></i>
            </div>
            <ul class="nav-list">
                <!-- Inicio -->
                <li :class="{ 'active': currentPage === 'dashboard' }">
                    <a href="{{ route('dashboard-estado') }}" class="ajax-link tooltip-item" data-tippy-content="Inicio" @click.prevent="currentPage = 'dashboard'">
                        <i class="fa-solid fa-house"></i>
                        <span class="link_name ml-1">Inicio</span>
                    </a>
                </li>
                <!-- Ubicaciones -->
                <li :class="{ 'active': currentPage === 'ubicaciones' }">
                    <a href="{{ route('ubicaciones') }}" class="ajax-link tooltip-item" data-tippy-content="Ubicaciones" @click.prevent="currentPage = 'ubicaciones'">
                        <i class="fa-solid fa-map-location"></i>
                        <span class="link_name ml-1">Ubicaciones</span>
                    </a>
                </li>
                <!-- Servicios -->
                <li :class="{ 'open': openMenu === 'servicios' }">
                    <a href="#" class="ajax-link tooltip-item" data-tippy-content="Servicios" @click.prevent="openMenu = openMenu === 'servicios' ? null : 'servicios'; openParametrizaciones = null;">
                        <i class="fa-solid fa-concierge-bell"></i>
                        <span class="link_name ml-1">Servicios</span>
                        <i class="fa-solid" :class="openMenu === 'servicios' ? 'fa-chevron-up' : 'fa-chevron-down'" style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'servicios'" x-transition>
                        <li :class="{ 'active': currentPage === 'servicios-vista-formulario' }">
                            <a href="{{ route('servicios-vista-formulario') }}" class="ajax-link tooltip-item" data-tippy-content="Formulario Servicios" @click.prevent="currentPage = 'servicios-vista-formulario'">
                                <i class="fa-solid fa-file-alt"></i>
                                <span class="sub_link_name ml-1">Formulario Servicios</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'solicitud-servicios' }">
                            <a href="{{ route('solicitud-servicios') }}" class="ajax-link tooltip-item" data-tippy-content="Activos Servicios" @click.prevent="currentPage = 'solicitud-servicios'">
                                <i class="fa-solid fa-tasks"></i>
                                <span class="sub_link_name ml-1">Activos Servicios</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Usuarios -->
                @if ($tipo == 2)
                <li :class="{ 'open': openMenu === 'Usuarios' }">
                    <a href="#" class="ajax-link tooltip-item" data-tippy-content="Usuarios" @click.prevent="openMenu = openMenu === 'Usuarios' ? null : 'Usuarios'; openParametrizaciones = null">
                        <i class="fa-solid fa-users"></i>
                        <span class="link_name ml-1">Usuarios</span>
                        <i class="fa-solid" :class="openMenu === 'Usuarios' ? 'fa-chevron-up' : 'fa-chevron-down'" style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openMenu === 'Usuarios'" x-transition>
                        <li :class="{ 'active': currentPage === 'tipo-user-empresa' }">
                            <a href="{{ route('empresas-tipo-user') }}" class="ajax-link tooltip-item" data-tippy-content="Tipos de Usuario" @click.prevent="currentPage = 'tipo-user-empresa'">
                                <i class="fa-solid fa-id-card"></i>
                                <span class="sub_link_name ml-1">Tipos de Usuario</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'usuarios-registrados-empresas' }">
                            <a href="{{ route('usuarios-empresas') }}" class="ajax-link tooltip-item" data-tippy-content="Usuarios Registrados" @click.prevent="currentPage = 'usuarios-registrados-empresas'">
                                <i class="fa-solid fa-address-book"></i>
                                <span class="sub_link_name ml-1">Usuarios Registrados</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                <!-- Activos -->
                @if ($ubicaciones->count() > 0)
                    <li :class="{ 'open': openMenu === 'activos' }">
                        <a href="#" class="ajax-link tooltip-item" data-tippy-content="Activos" @click.prevent="openMenu = openMenu === 'activos' ? null : 'activos'; openParametrizaciones = null;">
                            <i class="fa-solid fa-car"></i>
                            <span class="link_name ml-1">Activos</span>
                            <i class="fa-solid" :class="openMenu === 'activos' ? 'fa-chevron-up' : 'fa-chevron-down'" style="margin-left:auto; font-size: 0.8em;"></i>
                        </a>
                        <ul class="sub-menu" x-show="openMenu === 'activos'" x-transition>
                            <li :class="{ 'active': currentPage === 'activos-menu' }">
                                <a href="{{ route('activos-menu') }}" class="ajax-link tooltip-item" data-tippy-content="Mis Activos" @click.prevent="currentPage = 'activos-menu'">
                                    <i class="fa-solid fa-box"></i>
                                    <span class="sub_link_name ml-1">Mis Activos</span>
                                </a>
                            </li>
                            <li :class="{ 'active': currentPage === 'tablas' }">
                                <a href="{{ route('tablas') }}" class="ajax-link tooltip-item" data-tippy-content="Tablas de Activos" @click.prevent="currentPage = 'tablas'">
                                    <i class="fa-solid fa-table"></i>
                                    <span class="sub_link_name ml-1">Tablas de Activos</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                  <!-- Permisos y Roles -->
                  <li :class="{ 'open': openParametrizaciones === 'Permisos-Roles' }">
                    <a href="#" class="tooltip-item" data-tippy-content="Permisos y Roles" @click.prevent="openParametrizaciones = openParametrizaciones === 'Permisos-Roles' ? null : 'Permisos-Roles'">
                        <i class="fa-solid fa-user-lock"></i>
                        <span class="sub_link_name ml-1">Permisos y Roles</span>
                        <i class="fa-solid" :class="openParametrizaciones === 'Permisos-Roles' ? 'fa-chevron-up' : 'fa-chevron-down'" style="margin-left:auto; font-size: 0.8em;"></i>
                    </a>
                    <ul class="sub-menu" x-show="openParametrizaciones === 'Permisos-Roles'" x-transition>
                        <li :class="{ 'active': currentPage === 'Roles' }">
                            <a href="{{ route('roles') }}" class="ajax-link tooltip-item" data-tippy-content="Roles" @click.prevent="currentPage = 'Roles'">
                                <i class="fa-solid fa-id-badge"></i>
                                <span class="sub_link_name ml-1">Roles</span>
                            </a>
                        </li>
                        <li :class="{ 'active': currentPage === 'asignaciones-roles' }">
                            <a href="{{ route('asignaciones-roles') }}" class="ajax-link tooltip-item" data-tippy-content="Asignación Roles" @click.prevent="currentPage = 'asignaciones-roles'">
                                <i class="fa-solid fa-tasks-alt"></i>
                                <span class="sub_link_name ml-1">Asignación Roles</span>
                            </a>
                        </li>
                       
                        <li :class="{ 'active': currentPage === 'permisos-roles' }">
                            <a href="{{ route('permisos-roles') }}" class="ajax-link tooltip-item" data-tippy-content="Permisos Roles" @click.prevent="currentPage = 'permisos-roles'">
                                <i class="fa-solid fa-shield-alt"></i>
                                <span class="sub_link_name ml-1">Permisos Roles</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="overlay" x-show="sidebarOpen" @click="sidebarOpen = false"></div>
    </div>   
</div>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('sidebar', {
            open: false,
            toggle() {
                this.open = !this.open
            }
        })
    })
</script>
