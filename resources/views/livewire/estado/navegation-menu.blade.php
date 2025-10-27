<nav x-data="{ open: false }">
    <nav class="navbar navbar-expand-lg navbar bg-primary border-bottom border-body fw-bold" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Activos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link rounded rounded-5" href="{{ route('dashboard') }}">Inicio</a>
                    </li>
                    @if (Auth::user()->tipo == 1)
                        <li class="nav-item">
                            <a class="nav-link rounded rounded-5" href="" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasWithBothOptions"
                                aria-controls="offcanvasWithBothOptions">Usuarios</a>
                        </li>
                    @endif
                    @if ($ubicaciones->count() > 0)
                        <li class="nav-item">
                            <a class="nav-link rounded rounded-5" href="" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasWithBothOptions2"
                                aria-controls="offcanvasWithBothOptions">Panel de Activos</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link rounded rounded-5" href="{{ route('ubicaciones') }}">Ubicaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded rounded-5" href="{{ route('servicios-actividades-economicas')}}">Cargar Servicios</a>
                    </li>
                 
                    @if ($empresa == 4)
                    <li class="nav-item dropdown rounded rounded-5">
                        <a class="nav-link dropdown-toggle rounded rounded-5 text-light position-relative" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge badge-danger"
                                style="color: #ec4242; text-decoration: none; position: absolute; top: -5px; right: 0;">
                                {{ $solicitudes->count() + $actividad->count() }}
                            </span>
                            Solicitudes y Actividades
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark rounded rounded-5 bg-dark" aria-labelledby="solicitudesDropdown">
                            @if ($solicitudes->count() > 0)
                                @foreach ($solicitudes as $solicitud)
                                    <li>
                                        <a class="dropdown-item rounded rounded-5 text-light"
                                            href="{{ route('autorizacion') }}">
                                            {{ $solicitud->razon_social }} Nueva Solicitud
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <hr class="dropdown-divider" style="border-color: #fff;">
                                </li>
                            @endif
                            @if ($actividad->count() > 0)
                                @foreach ($actividad as $act)
                                    <li>
                                        <a class="dropdown-item rounded rounded-5 text-light"
                                            href="{{ route('actividades-extra') }}">
                                            {{ $act->nombre }} Nueva Actividad
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <hr class="dropdown-divider" style="border-color: #fff;">
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item rounded rounded-5 text-light" href="{{ route('autorizacion') }}">
                                    Mostrar todas las solicitudes
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded rounded-5 text-light" href="{{ route('actividades-extra') }}">
                                    Mostrar todas las actividades
                                </a>
                            </li>
                        </ul>
                    </li>   
                @endif
                </ul>
                <span class="nav-item dropdown rounded rounded-5 ms-5">
                    <a class="nav-link dropdown-toggle rounded rounded-5 text-light" href="#" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end rounded rounded-5 bg-dark"
                        style="border: 1px solid #000;">
                        <li><a class="dropdown-item rounded rounded-5 text-light"
                                href="{{ route('profile.show') }}">Perfil</a></li>
                        <li>
                            <hr class="dropdown-divider" style="border-color: #fff;">
                        </li>
                        <li>
                            <a class="dropdown-item rounded rounded-5 text-light" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Desconectarse
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </span>

            </div>
        </div>
    </nav>
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Parametrizaciones</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body ">
            <ul class="nav flex-column">
                <li class="nav-item">
                    @livewire('empresas.create-usuario')
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('usuarios-empresas') }}">Usuarios Registrados</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions2"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Activos</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body ">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('activos-menu') }}">Mis
                        Activos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('tablas') }}">Tablas de Activos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Activos compartidos</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
